<?php
// filepath: c:\xampp\htdocs\sulong_kalinga\app\Http\Controllers\NotificationsController.php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use App\Models\Beneficiary;
use App\Models\FamilyMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class NotificationsController extends Controller
{
    /**
     * Get notifications for the authenticated user
     */
    public function getUserNotifications(Request $request)
    {
        // Get the authenticated user
        $user = Auth::user();
        
        \Log::info('User requesting notifications', [
            'user_id' => $user ? $user->id : 'null',
            'role_id' => $user ? $user->role_id : 'null'
        ]);
        
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        
        // Get notifications
        $query = Notification::where('user_type', 'cose_staff')
                            ->where('user_id', $user->id)
                            ->orderBy('date_created', 'desc');
                            
        \Log::info('Running notification query', [
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings()
        ]);
        
        $notifications = $query->take(10)->get();
        
        \Log::info('Fetched notifications', [
            'count' => $notifications->count(),
            'first_few' => $notifications->take(3)->toArray()
        ]);
        
        return response()->json([
            'success' => true,
            'notifications' => $notifications,
            'unread_count' => $notifications->where('is_read', false)->count()
        ]);
    }
    
    /**
     * Get notifications for the admin dashboard (all users)
     */
    public function getAllNotifications(Request $request)
    {
        // Check if user is admin
        if (Auth::user()->role_id != 1) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // Get all notifications, newest first
        $notifications = Notification::with('notifiable')
            ->orderBy('date_created', 'desc')
            ->paginate(20);
            
        return response()->json([
            'success' => true,
            'notifications' => $notifications
        ]);
    }
    
    /**
     * Mark a notification as read
     */
    public function markAsRead(Request $request, $id)
    {
        $notification = Notification::find($id);
        
        if (!$notification) {
            return response()->json(['error' => 'Notification not found'], 404);
        }
        
        // Check if user has permission to mark this notification
        if (!$this->canAccessNotification($notification)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $notification->is_read = true;
        $notification->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read'
        ]);
    }
    
    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        
        // Get all unread notifications for this user
        $notifications = $this->getNotificationsByUserRole($user, true);
        
        // Mark all as read
        foreach ($notifications as $notification) {
            $notification->markAsRead();
        }
        
        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read',
            'count' => $notifications->count()
        ]);
    }
    
    /**
     * Create a new notification
     */
    public function store(Request $request)
    {
        // Validate request data
        $validated = $request->validate([
            'user_id' => 'required|integer',
            'user_type' => 'required|string|in:beneficiary,family_member,cose_staff',
            'message_title' => 'nullable|string|max:255',
            'message' => 'required|string',
        ]);
        
        // Create the notification
        $notification = Notification::create([
            'user_id' => $validated['user_id'],
            'user_type' => $validated['user_type'],
            'message_title' => $validated['message_title'] ?? null,
            'message' => $validated['message'],
            'date_created' => Carbon::now(),
            'is_read' => false
        ]);
        
        return response()->json([
            'success' => true,
            'notification' => $notification,
            'message' => 'Notification created successfully'
        ]);
    }
    
    /**
     * Get notifications based on user role
     */
    private function getNotificationsByUserRole($user, $onlyUnread = false)
    {
        $query = Notification::query();
        
        if ($onlyUnread) {
            $query->where('is_read', false);
        }
        
        // Staff members check
        if ($user->role_id <= 3) { // Admin, Care Manager, or Care Worker
            $query->where('user_type', 'cose_staff')
                  ->where('user_id', $user->id);
        } 
        // For portal users (future implementation)
        // We would need to check session data or API token to determine
        // if they are beneficiary or family member
        
        return $query->orderBy('date_created', 'desc')->get();
    }
    
    /**
     * Check if the authenticated user can access this notification
     */
    private function canAccessNotification($notification)
    {
        $user = Auth::user();
        
        // Admins can access all notifications
        if ($user->role_id == 1) {
            return true;
        }
        
        // Staff can only access their own notifications
        if ($notification->user_type == 'cose_staff' && $notification->user_id == $user->id) {
            return true;
        }
        
        // For future implementation: check beneficiary and family member access
        
        return false;
    }
}