<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\MessageAttachment;
use App\Models\MessageReadStatus;
use App\Models\ConversationParticipant;
use App\Models\User;
use App\Models\Beneficiary;
use App\Models\FamilyMember;

class MessageController extends Controller
{
    /**
     * Display the messaging interface based on user role.
     */
    public function index()
    {
        try {
            Log::info('Messaging index accessed', ['user_id' => Auth::id()]);
            $user = Auth::user();
            $rolePrefix = $this->getRoleRoutePrefix();
            $view = $this->getRoleViewPrefix() . '.messaging';
            
            // Get user's conversations without the problematic relationship
            $conversations = $this->getUserConversations($user);
            
            // Process participant names manually for private conversations
            foreach ($conversations as $conversation) {
                if (!$conversation->is_group_chat) {
                    // For private conversations, get the other participant's name
                    foreach ($conversation->participants as $participant) {
                        if ($participant->participant_id != $user->id || $participant->participant_type != 'cose_staff') {
                            $conversation->other_participant_name = $this->getParticipantName($participant);
                            break;
                        }
                    }
                }
                
                // Check for unread messages
                $conversation->has_unread = $conversation->messages->filter(function($message) {
                    return $message->readStatuses->isEmpty();
                })->isNotEmpty();
            }
            
            // Return the view with the data
            return view($view, compact('conversations', 'rolePrefix'));
        } catch (\Exception $e) {
            Log::error('Error in messaging index', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return view($this->getRoleViewPrefix() . '.messaging', [
                'conversations' => collect([]),
                'rolePrefix' => $this->getRoleRoutePrefix(),
                'error' => 'Unable to load conversations. Please try again later.'
            ]);
        }

        // Add this to handle the conversation parameter from dropdown
        $selectedConversationId = $request->input('conversation');
        
        return view('admin.messaging', [
            'conversations' => $conversations,
            'rolePrefix' => $this->getRoleRoutePrefix(),
            'selectedConversationId' => $selectedConversationId // Pass this to the view
        ]);
    }
    
    /**
     * View a specific conversation
     */
    public function viewConversation($id)
    {
        try {
            // Get the conversation with modified eager loading (removed the problematic relationship)
            $conversation = Conversation::with([
                'participants',  // Keep this
                'messages.attachments'  // Keep this
            ])->findOrFail($id);
            
            // Add other_participant_name to conversation object for display purposes
            if (!$conversation->is_group_chat) {
                $otherParticipant = $conversation->participants
                    ->where('participant_type', '!=', 'cose_staff')
                    ->where('participant_id', '!=', Auth::id())
                    ->first();
                
                if ($otherParticipant) {
                    // Get participant name using the helper method instead of the relationship
                    $conversation->other_participant_name = $this->getParticipantName($otherParticipant);
                } else {
                    $conversation->other_participant_name = 'Unknown User';
                }
            }
            
            // Get messages for this conversation
            $messages = Message::with(['attachments', 'readStatuses']) // <-- CHANGE HERE from readStatus to readStatuses
            ->where('conversation_id', $id)
            ->orderBy('message_timestamp', 'asc')
            ->get();
            
            // Add sender name to each message for display
            foreach ($messages as $message) {
                if ($message->sender_type == 'cose_staff') {
                    $sender = User::find($message->sender_id);
                    if ($sender) {
                        $message->sender_name = $sender->first_name . ' ' . $sender->last_name;
                    }
                } elseif ($message->sender_type == 'beneficiary') {
                    $sender = Beneficiary::find($message->sender_id);
                    if ($sender) {
                        $message->sender_name = $sender->first_name . ' ' . $sender->last_name;
                    }
                } elseif ($message->sender_type == 'family_member') {
                    $sender = FamilyMember::find($message->sender_id);
                    if ($sender) {
                        $message->sender_name = $sender->first_name . ' ' . $sender->last_name;
                    }
                }
            }
            
            // Mark all messages as read
            foreach ($messages as $message) {
                if ($message->sender_id != Auth::id() || $message->sender_type != 'cose_staff') {
                    $this->markMessageAsRead($message->message_id, Auth::id(), 'cose_staff');
                }
            }
            
            // Get all conversations for the sidebar using the getUserConversations method
            $conversations = $this->getUserConversations(Auth::user());
            
            // Process participant names manually for private conversations
            foreach ($conversations as $convo) {
                if (!$convo->is_group_chat) {
                    // For private conversations, get the other participant's name
                    foreach ($convo->participants as $participant) {
                        if ($participant->participant_id != Auth::id() || $participant->participant_type != 'cose_staff') {
                            $convo->other_participant_name = $this->getParticipantName($participant);
                            break;
                        }
                    }
                }
            }
            
            // Get role prefix for routes
            $rolePrefix = $this->getRoleRoutePrefix();
            
            return view('admin.conversation', compact('conversation', 'messages', 'conversations', 'rolePrefix'));
        } catch (\Exception $e) {
            Log::error('Error viewing conversation: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()->route($this->getRoleRoutePrefix() . '.messaging.index')->with('error', 'Unable to view conversation: ' . $e->getMessage());
        }
    }

    /**
     * Mark a message as read by a specific user.
     */
    private function markMessageAsRead($messageId, $userId, $userType)
    {
        try {
            // Check if read status already exists
            $readExists = MessageReadStatus::where('message_id', $messageId)
                ->where('reader_id', $userId)
                ->where('reader_type', $userType)
                ->exists();
                
            if (!$readExists) {
                MessageReadStatus::create([
                    'message_id' => $messageId,
                    'reader_id' => $userId,
                    'reader_type' => $userType,
                    'read_at' => now()
                ]);
            }
            
            return true;
        } catch (\Exception $e) {
            Log::error('Error marking message as read: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send a message via AJAX or form submission
     */
    public function sendMessage(Request $request)
    {

        Log::info('Request data for file upload', [
            'has_file_attachments' => $request->hasFile('attachments'),
            'all_files' => $request->allFiles(),
            'request_keys' => array_keys($request->all())
        ]);

        try {
            $user = Auth::user();
            $rolePrefix = $this->getRoleRoutePrefix();
            
            $validatedData = $request->validate([
                'conversation_id' => 'required|exists:conversations,conversation_id',
                'content' => 'nullable|string',
                'attachments.*' => 'sometimes|file|max:10240|mimes:jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,txt', // 10MB max with specific mime types
            ]);
            
            $conversationId = $request->conversation_id;
            $conversation = Conversation::findOrFail($conversationId);
            
            // Check if user is participant
            $isParticipant = $conversation->hasParticipant($user->id, 'cose_staff');
            if (!$isParticipant) {
                return $this->jsonResponse(false, 'You are not a participant in this conversation', 403);
            }
            
            // Create message with all required fields
            $message = new Message([
                'conversation_id' => $conversationId,
                'sender_id' => $user->id,
                'sender_type' => 'cose_staff',
                'content' => $request->content, // Add this to set content
                'message_timestamp' => now(),
            ]);
            $message->save();
            
            // Handle attachments
            if ($request->hasFile('attachments')) {
                Log::info("Request has attachments", [
                    'count' => is_array($request->file('attachments')) ? count($request->file('attachments')) : 1,
                    'conversation_id' => $conversationId
                ]);

                // Handle both array and non-array formats
                $files = $request->file('attachments');
                Log::info("Found files in request:", [
                    'count' => is_array($files) ? count($files) : 1
                ]);

                if (!is_array($files)) {
                    $files = [$files];
                }

                foreach ($files as $file) {
                    // Check if file is valid
                    if ($file && $file->isValid()) {
                        // Get file info
                        $fileName = $file->getClientOriginalName();
                        $fileType = $file->getMimeType();
                        $fileSize = $file->getSize();
                        
                        // Determine if it's an image
                        $isImage = str_starts_with($fileType, 'image/');
                        
                        // Store file in storage/app/public/attachments folder
                        $filePath = $file->store('attachments', 'public');
                        
                        // Create attachment record
                        $attachment = new MessageAttachment([
                            'message_id' => $message->message_id,
                            'file_name' => $fileName,
                            'file_path' => $filePath,
                            'file_type' => $fileType,
                            'file_size' => $fileSize,
                            'is_image' => $isImage
                        ]);
                        $attachment->save();
                        
                        // Log successful file upload
                        Log::info("File attachment created", [
                            'message_id' => $message->message_id,
                            'file_name' => $fileName,
                            'file_path' => $filePath
                        ]);
                    } else {
                        Log::error('Invalid file upload', ['filename' => $file->getClientOriginalName()]);
                    }
                }
            } else {
                Log::warning("No attachments found in request", [
                    'has_file' => $request->hasFile('attachments'),
                    'files' => $request->allFiles(),
                ]);
            }
            
            // Update last message in conversation
            $conversation->last_message_id = $message->message_id;
            $conversation->save();

            // After saving the message and attachments
            $attachmentData = [];
            if ($message->attachments->count() > 0) {
                foreach ($message->attachments as $attachment) {
                    $attachmentData[] = [
                        'id' => $attachment->id,
                        'file_name' => $attachment->file_name,
                        'file_path' => $attachment->file_path,
                        'file_type' => $attachment->file_type,
                        'file_size' => $attachment->file_size,
                        'is_image' => $attachment->is_image
                    ];
                }
            }
            
            // If AJAX request, return JSON
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message_id' => $message->message_id,
                    'message' => [
                        'content' => $message->content,
                    ],
                    'attachments' => $attachmentData
                ]);
            }

            Log::info('Request data for file upload', [
                'has_file_attachments' => $request->hasFile('attachments'),
                'has_array_attachments' => is_array($request->file('attachments')),
                'files_count' => $request->hasFile('attachments') ? count($request->file('attachments')) : 0,
                'all_files' => $request->allFiles()
            ]);
            
            // Otherwise redirect back
            return redirect()->route($rolePrefix . '.messaging.index', ['conversation' => $conversationId])
                ->with('success', 'Message sent successfully.');
        } catch (\Exception $e) {
            Log::error('Error sending message: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error sending message: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Message could not be sent: ' . $e->getMessage());
        }
    }

    /**
     * Helper for consistent JSON responses
     */
    private function jsonResponse($success, $message, $statusCode = 200, $data = [])
    {
        return response()->json(array_merge([
            'success' => $success,
            'message' => $message
        ], $data), $statusCode);
    }
    
    /**
     * Create a new private conversation.
     */
    public function createConversation(Request $request)
    {
        try {
            $user = Auth::user();
            $rolePrefix = $this->getRoleRoutePrefix();
            
            $request->validate([
                'participant_id' => 'required',
                'participant_type' => 'required|in:cose_staff,beneficiary,family_member',
            ]);
            
            // Check if conversation already exists
            $existingConversation = $this->findExistingPrivateConversation(
                $user->id, 
                'cose_staff',
                $request->participant_id, 
                $request->participant_type
            );
            
            if ($existingConversation) {
                return redirect()->route($rolePrefix . '.messaging.conversation', $existingConversation->conversation_id);
            }
            
            // Create new conversation
            $conversation = Conversation::create([
                'is_group_chat' => false,
            ]);
            
            // Add participants
            ConversationParticipant::create([
                'conversation_id' => $conversation->conversation_id,
                'participant_id' => $user->id,
                'participant_type' => 'cose_staff',
                'joined_at' => now(),
            ]);
            
            ConversationParticipant::create([
                'conversation_id' => $conversation->conversation_id,
                'participant_id' => $request->participant_id,
                'participant_type' => $request->participant_type,
                'joined_at' => now(),
            ]);
            
            return redirect()->route($rolePrefix . '.messaging.conversation', $conversation->conversation_id)
                ->with('success', 'Conversation created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating conversation: ' . $e->getMessage());
            return back()->with('error', 'Could not create conversation. Please try again.');
        }
    }
    
    /**
     * Create a new group conversation.
     */
    public function createGroupConversation(Request $request)
    {
        try {
            $user = Auth::user();
            $rolePrefix = $this->getRoleRoutePrefix();
            
            $request->validate([
                'name' => 'required|string|max:255',
                'participants' => 'required|array|min:1',
                'participants.*.id' => 'required',
                'participants.*.type' => 'required|in:cose_staff,beneficiary,family_member',
            ]);
            
            // Create new conversation
            $conversation = Conversation::create([
                'name' => $request->name,
                'is_group_chat' => true,
            ]);
            
            // Add creator as participant
            ConversationParticipant::create([
                'conversation_id' => $conversation->conversation_id,
                'participant_id' => $user->id,
                'participant_type' => 'cose_staff',
                'joined_at' => now(),
            ]);
            
            // Add other participants
            foreach ($request->participants as $participant) {
                // Skip if participant is the creator
                if ($participant['id'] == $user->id && $participant['type'] == 'cose_staff') {
                    continue;
                }
                
                ConversationParticipant::create([
                    'conversation_id' => $conversation->conversation_id,
                    'participant_id' => $participant['id'],
                    'participant_type' => $participant['type'],
                    'joined_at' => now(),
                ]);
            }

            // Create a system message for group creation
            $creatorName = $user->first_name . ' ' . $user->last_name;
            $userRole = '';
            if ($user->role_id == 1) {
                $userRole = 'Administrator';
            } elseif ($user->role_id == 2) {
                $userRole = 'Care Manager';
            } elseif ($user->role_id == 3) {
                $userRole = 'Care Worker';
            }

            $message = new Message([
                'conversation_id' => $conversation->conversation_id,
                'sender_id' => null,
                'sender_type' => 'system',
                'content' => "Group created by {$creatorName} ({$userRole})",
                'message_timestamp' => now(),
            ]);
            $message->save();

            // Update last message in conversation
            $conversation->last_message_id = $message->message_id;
            $conversation->save();
            
            return redirect()->route($rolePrefix . '.messaging.conversation', $conversation->conversation_id)
                ->with('success', 'Group conversation created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating group conversation: ' . $e->getMessage());
            return back()->with('error', 'Could not create group. Please try again.');
        }
    }
    
    /**
     * Get unread message count for the user.
     */
    public function getUnreadCount()
    {
        try {
            $user = Auth::user();
            
            // Get all conversation IDs user is part of
            $conversationIds = ConversationParticipant::where('participant_id', $user->id)
                ->where('participant_type', 'cose_staff')
                ->whereNull('left_at')
                ->pluck('conversation_id');
                
            // Count unread messages - messages sent by others that you haven't read
            $unreadCount = Message::whereIn('conversation_id', $conversationIds)
                ->where(function($query) use ($user) {
                    $query->where('sender_id', '!=', $user->id)
                        ->orWhere('sender_type', '!=', 'cose_staff');
                })
                ->whereDoesntHave('readStatuses', function($query) use ($user) {
                    $query->where('reader_id', $user->id)
                        ->where('reader_type', 'cose_staff');
                })
                ->count();
                
            Log::debug('Unread message count', ['count' => $unreadCount, 'user_id' => $user->id]);
                
            return response()->json(['count' => $unreadCount]);
        } catch (\Exception $e) {
            Log::error('Error getting unread count: ' . $e->getMessage());
            return response()->json(['count' => 0]); // Fallback to 0 to avoid UI issues
        }
    }
    
    public function getRecentMessages()
    {
        try {
            $user = Auth::user();
            $rolePrefix = $this->getRoleRoutePrefix();
            
            // Get all conversation IDs user is part of
            $conversationIds = ConversationParticipant::where('participant_id', $user->id)
                ->where('participant_type', 'cose_staff')
                ->whereNull('left_at')
                ->pluck('conversation_id');
            
            // Get conversations with all necessary relationships
            $conversations = Conversation::whereIn('conversation_id', $conversationIds)
                ->with([
                    'lastMessage', 
                    'lastMessage.attachments',
                    'participants' => function($query) {
                        $query->whereNull('left_at');
                    }
                ])
                ->get();
            
            // Sort by last message timestamp
            $sortedConversations = $conversations->sortByDesc(function($conversation) {
                return $conversation->lastMessage ? $conversation->lastMessage->message_timestamp : null;
            })->take(10);
            
            $result = [];
            
            foreach ($sortedConversations as $conversation) {
                $lastMessage = $conversation->lastMessage;
                
                // Create data structure for this conversation
                $convoData = [
                    'conversation_id' => $conversation->conversation_id,
                    'is_group_chat' => $conversation->is_group_chat,
                    'name' => $conversation->name ?? '',
                    'has_unread' => false,
                    'unread_count' => 0
                ];
                
                // Set other participant name for private chats
                if (!$conversation->is_group_chat) {
                    $otherParticipant = null;
                    
                    foreach ($conversation->participants as $participant) {
                        if (!($participant->participant_id == $user->id && $participant->participant_type == 'cose_staff')) {
                            $otherParticipant = $participant;
                            break;
                        }
                    }
                    
                    if ($otherParticipant) {
                        $convoData['other_participant_name'] = $this->getParticipantName($otherParticipant);
                    } else {
                        $convoData['other_participant_name'] = 'Unknown User';
                    }
                }
                
                // Add last message data
                if ($lastMessage) {
                    $messageData = [
                        'message_id' => $lastMessage->message_id,
                        'content' => $lastMessage->content,
                        'message_timestamp' => $lastMessage->message_timestamp,
                        'sender_name' => 'Unknown'
                    ];
                    
                    // Resolve sender name
                    if ($lastMessage->sender_type == 'cose_staff') {
                        $staff = User::find($lastMessage->sender_id);
                        $messageData['sender_name'] = $staff ? $staff->first_name . ' ' . $staff->last_name : 'Unknown Staff';
                    } else if ($lastMessage->sender_type == 'beneficiary') {
                        $beneficiary = Beneficiary::find($lastMessage->sender_id);
                        $messageData['sender_name'] = $beneficiary ? $beneficiary->first_name . ' ' . $beneficiary->last_name : 'Unknown Beneficiary';
                    } else if ($lastMessage->sender_type == 'family_member') {
                        $familyMember = FamilyMember::find($lastMessage->sender_id);
                        $messageData['sender_name'] = $familyMember ? $familyMember->first_name . ' ' . $familyMember->last_name : 'Unknown Family Member';
                    } else if ($lastMessage->sender_type == 'system') {
                        // Add this case for system messages
                        $messageData['sender_name'] = 'System';
                    }
                    
                    // Add attachment data if message has attachments but no text
                    if ((!$lastMessage->content || trim($lastMessage->content) === '') && 
                        $lastMessage->attachments && $lastMessage->attachments->count() > 0) {
                        
                        if ($lastMessage->attachments->count() == 1) {
                            $attachment = $lastMessage->attachments->first();
                            $messageData['content'] = "📎 " . $attachment->file_name;
                        } else {
                            $messageData['content'] = "📎 " . $lastMessage->attachments->count() . " attachments";
                        }
                    }
                    
                    $convoData['last_message'] = $messageData;
                    
                    // Check for unread status
                    $hasUnread = false;
                    if ($lastMessage->sender_id != $user->id || $lastMessage->sender_type != 'cose_staff') {
                        $readStatus = MessageReadStatus::where('message_id', $lastMessage->message_id)
                            ->where('reader_id', $user->id)
                            ->where('reader_type', 'cose_staff')
                            ->exists();
                        $hasUnread = !$readStatus;
                    }
                    
                    $convoData['has_unread'] = $hasUnread;
                }
                
                $result[] = $convoData;
            }
            
            return response()->json([
                'conversations' => $result,
                'route_prefix' => $rolePrefix
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting recent messages', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'conversations' => [],
                'route_prefix' => $rolePrefix,
                'error' => 'Failed to load messages'
            ]);
        }
    }

    // Add this helper method to get participant names
    private function getParticipantName($participant)
    {
        try {
            switch ($participant->participant_type) {
                case 'cose_staff':
                    $user = \App\Models\User::find($participant->participant_id);
                    return $user ? $user->first_name . ' ' . $user->last_name : 'Unknown Staff';
                
                case 'beneficiary':
                    $beneficiary = \App\Models\Beneficiary::find($participant->participant_id);
                    return $beneficiary ? $beneficiary->first_name . ' ' . $beneficiary->last_name : 'Unknown Beneficiary';
                    
                case 'family_member':
                    $familyMember = \App\Models\FamilyMember::find($participant->participant_id);
                    return $familyMember ? $familyMember->first_name . ' ' . $familyMember->last_name : 'Unknown Family Member';
                    
                default:
                    return 'Unknown User';
            }
        } catch (\Exception $e) {
            \Log::error('Error getting participant name: ' . $e->getMessage());
            return 'Unknown User';
        }
    }
    
    /**
     * Get user's conversations.
     */
    private function getUserConversations($user)
    {
        $conversations = Conversation::whereHas('participants', function ($query) use ($user) {
            $query->where('participant_id', $user->id)
                ->where('participant_type', 'cose_staff')
                ->whereNull('left_at');
        })
        ->with([
            'lastMessage', 
            'lastMessage.attachments',
            'participants' => function($query) {
                $query->whereNull('left_at');
            }
        ])
        ->orderBy('updated_at', 'desc')
        ->get();

        // Process conversations to add other participant name for private chats
        foreach ($conversations as $conversation) {
            if (!$conversation->is_group_chat) {
                // For private conversations, find the other participant
                $otherParticipant = null;
                
                foreach ($conversation->participants as $participant) {
                    if (!($participant->participant_id == $user->id && 
                        $participant->participant_type == 'cose_staff')) {
                        $otherParticipant = $participant;
                        break;
                    }
                }
                
                if ($otherParticipant) {
                    // Use the helper method to get the name
                    $conversation->other_participant_name = $this->getParticipantName($otherParticipant);
                    $conversation->other_participant_type = $otherParticipant->participant_type;
                } else {
                    $conversation->other_participant_name = 'No Other Participant';
                }
            }
        }

        return $conversations;
    }
    
    /**
     * Find existing private conversation between two users.
     */
    private function findExistingPrivateConversation($userId1, $userType1, $userId2, $userType2)
    {
        // Find private conversations where both users are participants
        return Conversation::where('is_group_chat', false)
            ->whereHas('participants', function ($query) use ($userId1, $userType1) {
                $query->where('participant_id', $userId1)
                    ->where('participant_type', $userType1)
                    ->whereNull('left_at');
            })
            ->whereHas('participants', function ($query) use ($userId2, $userType2) {
                $query->where('participant_id', $userId2)
                    ->where('participant_type', $userType2)
                    ->whereNull('left_at');
            })
            ->first();
    }
    
    /**
     * Mark all messages in a conversation as read
     */
    public function markConversationAsRead(Request $request)
    {
        try {
            $user = Auth::user();
            $conversationId = $request->input('conversation_id');
            
            // Get unread messages in this conversation not sent by the current user
            $messages = Message::where('conversation_id', $conversationId)
                ->where(function($query) use ($user) {
                    $query->where('sender_id', '!=', $user->id)
                        ->orWhere('sender_type', '!=', 'cose_staff');
                })
                ->whereDoesntHave('readStatuses', function($query) use ($user) {
                    $query->where('reader_id', $user->id)
                        ->where('reader_type', 'cose_staff');
                })
                ->get();
            
            // Mark each message as read
            foreach ($messages as $message) {
                MessageReadStatus::firstOrCreate([
                    'message_id' => $message->message_id,
                    'reader_id' => $user->id,
                    'reader_type' => 'cose_staff',
                    'read_at' => now()
                ]);
            }
            
            return response()->json([
                'success' => true,
                'count' => count($messages)
            ]);
        } catch (\Exception $e) {
            Log::error('Error marking conversation as read: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Error marking messages as read'
            ]);
        }
    }
    
    /**
     * Get the route prefix based on user role.
     */
    private function getRoleRoutePrefix()
    {
        $user = Auth::user();
        if ($user->role_id == 1) {
            return 'admin';
        } elseif ($user->role_id == 2) {
            return 'care-manager';
        } elseif ($user->role_id == 3) {
            return 'care-worker';
        }
        return 'admin'; // Default fallback
    }
    
    /**
     * Get the view prefix based on user role.
     */
    private function getRoleViewPrefix()
    {
        $user = Auth::user();
        if ($user->role_id == 1) {
            return 'admin';
        } elseif ($user->role_id == 2) {
            return 'careManager';
        } elseif ($user->role_id == 3) {
            return 'careWorker';
        }
        return 'admin'; // Default fallback
    }

    /**
     * Mark all unread messages for the current user as read.
     */
    public function markAllAsRead()
    {
        try {
            $user = Auth::user();
            
            // Get all conversation IDs user is part of
            $conversationIds = ConversationParticipant::where('participant_id', $user->id)
                ->where('participant_type', 'cose_staff')
                ->whereNull('left_at')
                ->pluck('conversation_id');
                
            // Get all unread messages from these conversations
            $unreadMessages = Message::whereIn('conversation_id', $conversationIds)
                ->where(function($query) use ($user) {
                    $query->where('sender_id', '!=', $user->id)
                        ->orWhere('sender_type', '!=', 'cose_staff');
                })
                ->whereDoesntHave('readStatuses', function($query) use ($user) {
                    $query->where('reader_id', $user->id)
                        ->where('reader_type', 'cose_staff');
                })
                ->get();
                
            // Mark each message as read
            foreach ($unreadMessages as $message) {
                MessageReadStatus::create([
                    'message_id' => $message->message_id,
                    'reader_id' => $user->id,
                    'reader_type' => 'cose_staff',
                    'read_at' => now()
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'All messages marked as read',
                'count' => count($unreadMessages)
            ]);
        } catch (\Exception $e) {
            Log::error('Error marking all messages as read: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark all messages as read'
            ], 500);
        }
    }

    /**
     * Get users for messaging based on type.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUsers(Request $request)
    {
        $type = $request->input('type', 'cose_staff');
        $users = [];
        
        try {
            switch ($type) {
                case 'cose_staff':
                    // Get all staff excluding the current user
                    $users = User::where('id', '!=', Auth::id())
                        ->where('status', 1) 
                        ->select('id', 'first_name', 'last_name')
                        ->get();
                    break;
                    
                case 'beneficiary':
                    // Get all active beneficiaries
                    $users = Beneficiary::where('status', 1)
                        ->select('beneficiary_id as id', 'first_name', 'last_name')
                        ->get();
                    break;
                    
                case 'family_member':
                    // Get all family members
                    $users = FamilyMember::select('family_member_id as id', 'first_name', 'last_name')
                        ->get();
                    break;
                    
                default:
                    return response()->json(['error' => 'Invalid user type'], 400);
            }
            
            return response()->json(['users' => $users]);
        } catch (\Exception $e) {
            Log::error('Error fetching users for messaging: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch users'], 500);
        }
    }

    /**
     * Get conversation content via AJAX
     */
    public function getConversation(Request $request)
    {
        try {
            $conversationId = $request->input('id');
            
            // Get the conversation with participants
            $conversation = Conversation::with([
                'messages.attachments', // This is already correctly loading attachments
                'participants'
            ])->findOrFail($conversationId);
            
            // Add other_participant_name to conversation object
            if (!$conversation->is_group_chat) {
                // Get the participant who isn't the current user
                $otherParticipant = $conversation->participants()
                    ->where(function($query) {
                        $query->where('participant_id', '!=', Auth::id())
                            ->orWhere('participant_type', '!=', 'cose_staff');
                    })
                    ->whereNull('left_at')
                    ->first();
                    
                if ($otherParticipant) {
                    $conversation->other_participant_name = $this->getParticipantName($otherParticipant);
                    $conversation->other_participant_type = $otherParticipant->participant_type;
                } else {
                    $conversation->other_participant_name = 'Unknown User';
                    $conversation->other_participant_type = '';
                }
            }
            
            // Get messages for this conversation
                $messages = Message::with(['attachments', 'readStatuses'])
                ->where('conversation_id', $conversationId)
                ->orderBy('message_timestamp', 'asc')
                ->get();
            
            // Add sender name to each message
            foreach ($messages as $message) {
                if ($message->sender_type === 'cose_staff') {
                    $sender = User::find($message->sender_id);
                    $message->sender_name = $sender ? $sender->first_name . ' ' . $sender->last_name : 'Unknown Staff';
                    $message->sender_role_id = $sender ? $sender->role_id : null;
                } else if ($message->sender_type === 'beneficiary') {
                    $sender = Beneficiary::find($message->sender_id);
                    $message->sender_name = $sender ? $sender->first_name . ' ' . $sender->last_name : 'Unknown Beneficiary';
                } else if ($message->sender_type === 'family_member') {
                    $sender = FamilyMember::find($message->sender_id);
                    $message->sender_name = $sender ? $sender->first_name . ' ' . $sender->last_name : 'Unknown Family Member';
                } else {
                    $message->sender_name = 'Unknown';
                }

                if ($message->attachments && $message->attachments->count() > 0) {
                    Log::debug('Message has attachments', [
                        'message_id' => $message->message_id,
                        'attachments_count' => $message->attachments->count()
                    ]);
                    
                    // Process each attachment to ensure consistent formatting
                    foreach ($message->attachments as $attachment) {
                        // Make sure it has the correct properties
                        $attachment->file_path = str_replace('public/', '', $attachment->file_path);
                        
                        // Ensure is_image is properly set as boolean for JS use
                        if (is_string($attachment->is_image)) {
                            $attachment->is_image = $attachment->is_image === '1' || $attachment->is_image === 'true';
                        }
                        
                        // If file_type is missing, infer it from the file extension
                        if (empty($attachment->file_type)) {
                            $extension = pathinfo($attachment->file_name, PATHINFO_EXTENSION);
                            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                                $attachment->file_type = 'image/' . $extension;
                                $attachment->is_image = true;
                            } else if ($extension === 'pdf') {
                                $attachment->file_type = 'application/pdf';
                            } else if (in_array($extension, ['doc', 'docx'])) {
                                $attachment->file_type = 'application/msword';
                            } else if (in_array($extension, ['xls', 'xlsx'])) {
                                $attachment->file_type = 'application/vnd.ms-excel';
                            } else if ($extension === 'txt') {
                                $attachment->file_type = 'text/plain';
                            } else {
                                $attachment->file_type = 'application/octet-stream';
                            }
                        }
                    }
                }
            }
            
            // Render conversation content HTML
            $html = view('admin.conversation-content', [
                'conversation' => $conversation,
                'messages' => $messages,
                'rolePrefix' => $this->getRoleRoutePrefix()
            ])->render();
            
            return response()->json([
                'success' => true,
                'html' => $html
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading conversation: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Error loading conversation: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Allow a user to leave a group conversation
     */
    public function leaveGroupConversation(Request $request)
    {
        try {
            $user = Auth::user();
            $conversationId = $request->input('conversation_id');
            
            $conversation = Conversation::findOrFail($conversationId);
            
            // Ensure it's a group chat
            if (!$conversation->is_group_chat) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot leave a private conversation'
                ], 400);
            }
            
            // Find the participant record
            $participant = ConversationParticipant::where('conversation_id', $conversationId)
                ->where('participant_id', $user->id)
                ->where('participant_type', 'cose_staff')
                ->whereNull('left_at')
                ->first();
            
            if (!$participant) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not a participant in this conversation'
                ], 404);
            }
            
            // Get user's full name and role
            $userName = $user->first_name . ' ' . $user->last_name;
            $userRole = '';
            if ($user->role_id == 1) {
                $userRole = 'Administrator';
            } elseif ($user->role_id == 2) {
                $userRole = 'Care Manager';
            } elseif ($user->role_id == 3) {
                $userRole = 'Care Worker';
            }
            
            // Create a system message indicating the user left
            $message = new Message([
                'conversation_id' => $conversationId,
                'sender_id' => null,  // System message has no sender
                'sender_type' => 'system',
                'content' => "{$userName} ({$userRole}) left the group",
                'message_timestamp' => now(),
            ]);
            $message->save();
            
            // Update last message in conversation
            $conversation->last_message_id = $message->message_id;
            $conversation->save();
            
            // Update the participant record with left_at timestamp
            $participant->left_at = now();
            $participant->save();
            
            return response()->json([
                'success' => true,
                'message' => 'You have left the group conversation'
            ]);
        } catch (\Exception $e) {
            Log::error('Error leaving group conversation: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Error leaving group: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all participant names for a conversation for search
     */
    private function getAllParticipantNames($conversation)
    {
        $names = [];
        
        foreach ($conversation->participants as $participant) {
            if ($participant->participant_type === 'cose_staff') {
                $user = User::find($participant->participant_id);
                if ($user) {
                    $names[] = $user->first_name . ' ' . $user->last_name;
                }
            } else if ($participant->participant_type === 'beneficiary') {
                $beneficiary = Beneficiary::find($participant->participant_id);
                if ($beneficiary) {
                    $names[] = $beneficiary->first_name . ' ' . $beneficiary->last_name;
                }
            } else if ($participant->participant_type === 'family_member') {
                $familyMember = FamilyMember::find($participant->participant_id);
                if ($familyMember) {
                    $names[] = $familyMember->first_name . ' ' . $familyMember->last_name;
                }
            }
        }
        
        return implode(', ', $names);
    }

    /**
     * Get conversations list via AJAX to refresh the sidebar
     */
    public function getConversationsList(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Get conversations
            $conversations = $this->getUserConversations($user);
            
            // Process participant names for display
            foreach ($conversations as $conversation) {
                if (!$conversation->is_group_chat) {
                    // Find the other participant
                    $otherParticipant = $conversation->participants()
                        ->where(function($query) use ($user) {
                            $query->where('participant_id', '!=', $user->id)
                                ->orWhere('participant_type', '!=', 'cose_staff');
                        })
                        ->whereNull('left_at')
                        ->first();
                    
                    if ($otherParticipant) {
                        $conversation->other_participant_name = $this->getParticipantName($otherParticipant);
                        $conversation->other_participant_type = $otherParticipant->participant_type;
                    } else {
                        $conversation->other_participant_name = 'Unknown User';
                        $conversation->other_participant_type = '';
                    }
                }
            }
            
            // Render conversation list HTML
            $html = view('admin.conversation-list', [
                'conversations' => $conversations
            ])->render();
            
            return response()->json([
                'success' => true,
                'html' => $html
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting conversations list', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading conversations: ' . $e->getMessage()
            ]);
        }
    }

}