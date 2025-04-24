<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Messaging - SulongKalinga</title>
    
    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/userNavbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/messaging.css') }}">
</head>
<body class="messaging-page">

    <!-- Navigation based on user role -->
    @if(auth()->user()->role_id == 1)
        @include('components.adminNavbar')
        @include('components.adminSidebar')
    @elseif(auth()->user()->role_id == 2)
        @include('components.careManagerNavbar')
        @include('components.careManagerSidebar')
    @elseif(auth()->user()->role_id == 3)
        @include('components.careWorkerNavbar')
        @include('components.careWorkerSidebar')
    @endif

    <!-- Main Content -->
    <main class="main-content">
        <div class="messaging-container">
            <!-- Conversation List (Left Sidebar) -->
            <div class="conversation-list">
                <!-- Search and New Chat Button -->
                <div class="conversation-search">
                    <div class="search-container">
                        <div class="search-input-group">
                            <span class="input-group-text">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" class="form-control" id="conversationSearch" placeholder="Search conversations...">
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="newChatDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-plus-lg"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="newChatDropdown">
                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#newConversationModal">Private Message</a></li>
                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#newGroupModal">New Group</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- Conversation List Items -->
                <div class="conversation-list-items">
                    @include('admin.conversation-list', ['conversations' => $conversations])
                </div>
            </div>
            
            <!-- Message Area (Right Side) -->
            <div class="message-area">
                <div id="conversationContent">
                    <!-- Empty State / Select Conversation -->
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="bi bi-chat-dots"></i>
                        </div>
                        <h4>Select a conversation</h4>
                        <p class="mb-4">Choose a conversation from the list or start a new one.</p>
                        <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle" type="button" id="newChatDropdownEmpty" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-plus-lg"></i> New Chat
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="newChatDropdownEmpty">
                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#newConversationModal">Private Message</a></li>
                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#newGroupModal">New Group</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- New Private Conversation Modal -->
        <div class="modal fade" id="newConversationModal" tabindex="-1" aria-labelledby="newConversationModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="newConversationModalLabel">New Conversation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="newConversationForm">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="userType" class="form-label">User Type</label>
                                <select class="form-select" id="userType" name="participant_type" required>
                                    <option value="cose_staff">Staff</option>
                                    <option value="beneficiary">Beneficiary</option>
                                    <option value="family_member">Family Member</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="userSearch" class="form-label">Search for a user</label>
                                <input type="text" class="form-control" id="userSearch" placeholder="Type a name...">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Select User</label>
                                <select class="form-select" name="participant_id" id="participantSelect" required>
                                    <option value="">Select a user</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Start Conversation</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- New Group Conversation Modal -->
        <div class="modal fade" id="newGroupModal" tabindex="-1" aria-labelledby="newGroupModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="newGroupModalLabel">Create Group Chat</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="newGroupForm">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="groupName" class="form-label">Group Name</label>
                                <input type="text" class="form-control" id="groupName" name="name" placeholder="Enter group name" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Select Participants</label>
                                <div class="input-group mb-2">
                                    <span class="input-group-text bg-transparent border-end-0">
                                        <i class="bi bi-search"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0" id="participantSearch" placeholder="Search...">
                                </div>
                                
                                <div id="selectedParticipantsContainer" class="selected-participants mb-2">
                                    <!-- Selected participants will appear here as tags -->
                                </div>
                                
                                <div id="participantsList" class="list-group mt-2" style="max-height: 200px; overflow-y: auto;">
                                    <!-- Participants will be loaded here -->
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Create Group</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Leave Group Modal -->
    <div class="modal fade" id="leaveGroupModal" tabindex="-1" aria-labelledby="leaveGroupModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="leaveGroupModalLabel">Leave Group</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to leave this group? You won't receive any more messages from this conversation.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmLeaveGroup">Leave Group</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    
    <!-- CONSOLIDATED JAVASCRIPT - CLEAN VERSION -->
    <script>
        // ------------------- GLOBAL VARIABLES -------------------
        // Timing variables to prevent conflicts
        window.lastRefreshTimestamp = 0;
        window.lastMessageSendTimestamp = 0;
        window.messageFormInitialized = false;
        window.isRefreshing = false;
        window.conversationFirstMessageSent = {};
        window.preventRefreshUntil = 0;
        window.lastBlurContent = '';
        window.lastBlurTime = 0;
        let currentLeaveGroupId = null;
        
        // ------------------- INPUT PROTECTION -------------------
        // Override the textarea value setter to detect and prevent unwanted clearing
        const originalValueSetter = Object.getOwnPropertyDescriptor(HTMLTextAreaElement.prototype, 'value').set;
        Object.defineProperty(HTMLTextAreaElement.prototype, 'value', {
            set(val) {
                if (this.id === 'messageContent' && val === '' && this.value !== '') {
                    console.log('Textarea being cleared from:', new Error().stack);
                    
                    // Only allow clearing if we just sent a message
                    if (Date.now() - window.lastMessageSendTimestamp > 1000) {
                        // Save the content in case we need to restore it
                        window.lastBlurContent = this.value;
                        console.log("Preventing automatic textarea clearing - saving content");
                        // Let the original setter run, but we'll restore later if needed
                    }
                }
                return originalValueSetter.call(this, val);
            }
        });
        
        // Save content before page unload
        window.addEventListener('beforeunload', function() {
            const textarea = document.getElementById('messageContent');
            const conversationId = document.querySelector('input[name="conversation_id"]')?.value;
            if (textarea && textarea.value.trim() !== '' && conversationId) {
                localStorage.setItem('messageContent_' + conversationId, textarea.value);
            }
        });
        
        // ------------------- UTILITY FUNCTIONS -------------------
        // Function to mark a conversation as read
        function markConversationAsRead(conversationId) {
            if (!conversationId) return;
            
            fetch('{{ route($rolePrefix.".messaging.mark-as-read") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    conversation_id: conversationId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove unread indicators from this conversation
                    const unreadBadge = document.querySelector(`.conversation-item[data-conversation-id="${conversationId}"] .unread-badge`);
                    if (unreadBadge) {
                        unreadBadge.remove();
                    }
                    
                    // Remove unread class from conversation item
                    const conversationItem = document.querySelector(`.conversation-item[data-conversation-id="${conversationId}"]`);
                    if (conversationItem) {
                        conversationItem.classList.remove('unread');
                    }
                    
                    // Update navbar badge count
                    updateNavbarUnreadCount();
                }
            })
            .catch(error => {
                console.error('Error marking conversation as read:', error);
            });
        }
        
        // Function to update navbar unread count
        function updateNavbarUnreadCount() {
            fetch('{{ route($rolePrefix.".messaging.unread-count") }}')
                .then(response => response.json())
                .then(data => {
                    const messageCount = document.querySelector('.message-count');
                    if (messageCount) {
                        if (data.count > 0) {
                            messageCount.textContent = data.count;
                            messageCount.style.display = 'block';
                        } else {
                            messageCount.style.display = 'none';
                        }
                    }
                })
                .catch(error => console.error('Error updating message count:', error));
        }
        
        // Function to add a message to the UI
        window.addMessageToDisplay = function(messageData, isOutgoing = true) {
            const messagesContainer = document.getElementById('messagesContainer');
            if (!messagesContainer) {
                console.error('Message container not found!');
                return;
            }
            
            // Create message element
            const messageEl = document.createElement('div');
            messageEl.className = isOutgoing ? 'message outgoing' : 'message incoming';
            
            // Add message content
            const contentEl = document.createElement('div');
            contentEl.className = 'message-content';
            
            // Make sure we have content, with fallbacks
            let messageContent = '';
            if (typeof messageData.content === 'string' && messageData.content) {
                messageContent = messageData.content;
            } else if (messageData.formContent) {
                messageContent = messageData.formContent;
            }
            
            contentEl.textContent = messageContent;
            messageEl.appendChild(contentEl);
            
            // Add time indicator
            const timeEl = document.createElement('div');
            timeEl.className = 'message-time';
            const timeString = messageData.time || new Date().toLocaleTimeString([], {hour: 'numeric', minute:'2-digit'});
            timeEl.innerHTML = `<small>${timeString}</small>`;
            messageEl.appendChild(timeEl);
            
            // Add to container
            messagesContainer.appendChild(messageEl);
            
            // Scroll to the bottom
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        };
        
        // Function to update conversation list
        window.updateConversationList = function() {
            const activeConversationId = document.querySelector('.conversation-item.active')?.dataset.conversationId;
            
            // Fetch updated conversation list
            fetch('{{ route($rolePrefix.".messaging.get-conversations") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update the conversation list content
                        const listContainer = document.querySelector('.conversation-list-items');
                        if (listContainer) {
                            listContainer.innerHTML = data.html;
                            
                            // Re-add click handlers
                            addConversationClickHandlers();
                            
                            // Restore active state to the current conversation
                            if (activeConversationId) {
                                const activeItem = document.querySelector(`.conversation-item[data-conversation-id="${activeConversationId}"]`);
                                if (activeItem) {
                                    activeItem.classList.add('active');
                                }
                            }
                        }
                    }
                })
                .catch(error => {
                    console.error('Error updating conversation list:', error);
                });
        };
        
        // Function to load conversation
        function loadConversation(conversationId) {
            if (!conversationId) return;
            
            // Show loading state
            const messageArea = document.querySelector('.message-area');
            if (!messageArea) return;
            
            messageArea.innerHTML = `
                <div id="conversationContent">
                    <div class="loading-container d-flex justify-content-center align-items-center h-100">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            `;
            
            // Fetch conversation content via AJAX
            fetch("{{ route($rolePrefix.'.messaging.get-conversation') }}?id=" + conversationId, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the conversation content
                    document.getElementById('conversationContent').innerHTML = data.html;
                    
                    // Reset message form initialization flag
                    window.messageFormInitialized = false;
                    
                    // Initialize the message form
                    setTimeout(() => initializeMessageForm(conversationId), 200);
                    
                    // Scroll to bottom of messages
                    const messagesContainer = document.getElementById('messagesContainer');
                    if (messagesContainer) {
                        messagesContainer.scrollTop = messagesContainer.scrollHeight;
                    }
                    
                    // Mark conversation as read
                    markConversationAsRead(conversationId);
                } else {
                    document.getElementById('conversationContent').innerHTML = `
                        <div class="empty-state">
                            <div class="empty-icon text-danger">
                                <i class="bi bi-exclamation-triangle"></i>
                            </div>
                            <h4>Error Loading Conversation</h4>
                            <p class="mb-4">${data.message || 'Could not load the conversation.'}</p>
                            <button class="btn btn-primary" onclick="window.location.reload()">Reload Page</button>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error loading conversation:', error);
                document.getElementById('conversationContent').innerHTML = `
                    <div class="empty-state">
                        <div class="empty-icon text-danger">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                        <h4>Error Loading Conversation</h4>
                        <p class="mb-4">Could not load the conversation. Please try again.</p>
                        <button class="btn btn-primary" onclick="window.location.reload()">Reload Page</button>
                    </div>
                `;
            });
        }
        
        // Function to add click handlers to conversation items
        function addConversationClickHandlers() {
            document.querySelectorAll('.conversation-item').forEach(item => {
                item.addEventListener('click', function() {
                    // Remove active class from all items
                    document.querySelectorAll('.conversation-item').forEach(i => {
                        i.classList.remove('active');
                    });
                    
                    // Add active class to clicked item
                    this.classList.add('active');
                    
                    // Get conversation ID
                    const conversationId = this.dataset.conversationId;
                    
                    // IMPORTANT: Clear any lingering message content state when switching conversations
                    window.lastBlurContent = '';
                    
                    // Update URL without page reload
                    const newUrl = "{{ url('/" . $rolePrefix . "/messaging') }}?conversation=" + conversationId;
                    window.history.pushState({ conversationId: conversationId }, '', newUrl);
                    
                    // Load conversation content
                    loadConversation(conversationId);
                });
            });
        }
        
        // Function to refresh active conversation
        window.refreshActiveConversation = function() {
            // Check if refresh is explicitly prevented
            if (Date.now() < window.preventRefreshUntil) {
                console.log('Refresh prevented by time lock - ' + 
                    Math.round((window.preventRefreshUntil - Date.now())/1000) + ' seconds remaining');
                return;
            }
            
            // Check if there's an active conversation
            const activeConversationItem = document.querySelector('.conversation-item.active');
            if (!activeConversationItem || window.isRefreshing) return;
            
            // Don't refresh if we just sent a message in the last 3 seconds
            const now = Date.now();
            if (now - window.lastMessageSendTimestamp < 3000) {
                console.log('Refresh prevented - message recently sent');
                return;
            }
            
            window.isRefreshing = true;
            window.lastRefreshTimestamp = now;
            
            const conversationId = activeConversationItem.dataset.conversationId;
            const messagesContainer = document.getElementById('messagesContainer');
            if (!messagesContainer) {
                window.isRefreshing = false;
                return;
            }
            
            // Save textarea content before refresh
            const textarea = document.getElementById('messageContent');
            let textareaContent = '';
            if (textarea && textarea.value.trim()) {
                textareaContent = textarea.value;
            }
            
            // Get the current scroll position
            const wasAtBottom = messagesContainer.scrollHeight - messagesContainer.scrollTop <= messagesContainer.clientHeight + 50;
            const scrollTop = messagesContainer.scrollTop;
            
            // Get the latest messages for this conversation
            fetch('{{ route($rolePrefix.".messaging.get-conversation") }}?id=' + conversationId, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Check if there's new content by comparing HTML
                    const contentDiv = document.getElementById('conversationContent');
                    if (contentDiv) {
                        const currentContent = contentDiv.innerHTML;
                        
                        // Only update if content has changed and there's no ongoing typing
                        if (data.html !== currentContent && (!textarea || !textarea.value.trim() || textarea.value === textareaContent)) {
                            contentDiv.innerHTML = data.html;
                            
                            // Restore textarea content
                            const newTextarea = document.getElementById('messageContent');
                            if (newTextarea && textareaContent) {
                                newTextarea.value = textareaContent;
                            }
                            
                            // Init form but respect existing content
                            window.messageFormInitialized = false;
                            setTimeout(() => initializeMessageForm(conversationId, true), 200);
                            
                            // If we were at the bottom, scroll to the bottom again
                            const newMessagesContainer = document.getElementById('messagesContainer');
                            if (newMessagesContainer) {
                                if (wasAtBottom) {
                                    newMessagesContainer.scrollTop = newMessagesContainer.scrollHeight;
                                } else {
                                    newMessagesContainer.scrollTop = scrollTop;
                                }
                            }
                            
                            // Mark messages as read
                            markConversationAsRead(conversationId);
                        }
                    }
                    
                    updateNavbarUnreadCount();
                }
                window.isRefreshing = false;
            })
            .catch(error => {
                console.error('Error refreshing conversation:', error);
                window.isRefreshing = false;
            });
        };
        
        // ------------------- MESSAGE FORM HANDLING -------------------
        // Initialize message form
        function initializeMessageForm(conversationId, isRefresh = false) {
            const messageForm = document.getElementById('messageForm');
            if (!messageForm) return;
            
            // If already initialized and not a refresh, skip
            if (window.messageFormInitialized && !isRefresh) return;
            
            // Set flag to prevent duplicate initialization
            window.messageFormInitialized = true;
            
            // Get form elements
            const textarea = document.getElementById('messageContent');
            const fileInput = document.getElementById('fileUpload');
            const filePreviewContainer = document.getElementById('filePreviewContainer');
            const attachmentBtn = document.getElementById('attachmentBtn');
            
            // Restore previous content if this is first initialization (not refresh)
            if (!isRefresh && textarea && textarea.value.trim() === '' && conversationId) {
                const savedContent = localStorage.getItem('messageContent_' + conversationId);
                if (savedContent) {
                    textarea.value = savedContent;
                    console.log('Restored saved content for conversation:', conversationId);
                }
            }
            
            // Auto-resize textarea
            if (textarea) {
                // Initial resize
                if (textarea.value) {
                    textarea.style.height = 'auto';
                    textarea.style.height = (textarea.scrollHeight) + 'px';
                }
                
                textarea.addEventListener('input', function() {
                    // Auto-resize as user types
                    this.style.height = 'auto';
                    this.style.height = (this.scrollHeight) + 'px';
                    
                    if (this.scrollHeight > 200) {
                        this.style.overflowY = 'auto';
                        this.style.height = '200px';
                    } else {
                        this.style.overflowY = 'hidden';
                    }
                    
                    // Save to localStorage as typing
                    const currentConversationId = document.querySelector('input[name="conversation_id"]').value;
                    localStorage.setItem('messageContent_' + currentConversationId, this.value);
                });
                
                // Focus/blur protection
                textarea.addEventListener('blur', function() {
                    window.lastBlurTime = Date.now();
                    window.lastBlurContent = this.value;
                });
                
                textarea.addEventListener('focus', function() {
                    // If content was cleared unexpectedly, restore it
                    if (this.value === '' && window.lastBlurContent && 
                        (Date.now() - window.lastMessageSendTimestamp > 2000)) {
                        console.log('Restoring lost content after focus change');
                        this.value = window.lastBlurContent;
                    }
                });
            }
            
            // File attachment handling
            if (attachmentBtn && fileInput) {
                attachmentBtn.addEventListener('click', function() {
                    fileInput.click();
                });
                
                if (fileInput && filePreviewContainer) {
                    fileInput.addEventListener('change', function() {
                        filePreviewContainer.innerHTML = '';
                        
                        if (this.files.length > 0) {
                            for (let i = 0; i < this.files.length; i++) {
                                const file = this.files[i];
                                const filePreview = document.createElement('div');
                                filePreview.className = 'file-preview';
                                
                                if (file.type.startsWith('image/')) {
                                    const img = document.createElement('img');
                                    img.src = URL.createObjectURL(file);
                                    filePreview.appendChild(img);
                                } else {
                                    const fileIcon = document.createElement('div');
                                    fileIcon.className = 'file-icon';
                                    fileIcon.innerHTML = '<i class="bi bi-file-earmark"></i>';
                                    filePreview.appendChild(fileIcon);
                                }
                                
                                const fileName = document.createElement('div');
                                fileName.className = 'file-name';
                                fileName.textContent = file.name;
                                filePreview.appendChild(fileName);
                                
                                const removeBtn = document.createElement('div');
                                removeBtn.className = 'remove-file';
                                removeBtn.innerHTML = '&times;';
                                removeBtn.addEventListener('click', function() {
                                    filePreview.remove();
                                });
                                
                                filePreview.appendChild(removeBtn);
                                filePreviewContainer.appendChild(filePreview);
                            }
                        }
                    });
                }
            }
            
            // Message form submission
            messageForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Validation
                if (textarea.value.trim() === '' && (!fileInput.files || fileInput.files.length === 0)) {
                    alert('Please enter a message or attach a file');
                    return;
                }
                
                // Record timestamp before any async operations
                window.lastMessageSendTimestamp = Date.now();
                
                // Get the message content before clearing
                const messageContent = textarea.value.trim();
                const currentConversationId = document.querySelector('input[name="conversation_id"]').value;
                
                // Create FormData object
                const formData = new FormData(this);
                
                // Disable send button and show loading state
                const sendBtn = document.querySelector('.send-btn');
                const originalBtnContent = sendBtn.innerHTML;
                sendBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
                sendBtn.disabled = true;
                
                // Prevent refreshes for 10 seconds for first message
                const isFirstMessage = !window.conversationFirstMessageSent[currentConversationId];
                if (isFirstMessage) {
                    window.preventRefreshUntil = Date.now() + 10000;
                    window.conversationFirstMessageSent[currentConversationId] = true;
                }
                
                // Send the message
                fetch('{{ route($rolePrefix.".messaging.send") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Clear the input after successful sending
                        textarea.value = '';
                        fileInput.value = '';
                        if (filePreviewContainer) {
                            filePreviewContainer.innerHTML = '';
                        }
                        
                        // Remove from localStorage
                        localStorage.removeItem('messageContent_' + currentConversationId);
                        
                        // Add message to display immediately without refresh
                        window.addMessageToDisplay({
                            content: messageContent,
                            id: data.message_id,
                            time: new Date().toLocaleTimeString([], {hour: 'numeric', minute:'2-digit'})
                        }, true);
                        
                        // Update the conversation list
                        window.updateConversationList();
                    } else {
                        throw new Error(data.message || 'Failed to send message');
                    }
                })
                .catch(error => {
                    console.error('Error sending message:', error);
                    alert('Failed to send message: ' + error.message);
                    
                    // Restore the message content
                    textarea.value = messageContent;
                })
                .finally(() => {
                    // Reset button state
                    sendBtn.innerHTML = originalBtnContent;
                    sendBtn.disabled = false;
                });
            });
        }
        
        // ------------------- DOCUMENT READY INITIALIZATION -------------------
        document.addEventListener('DOMContentLoaded', function() {
            // Set up minimized sidebar
            function setupMinimizedSidebar() {
                const sidebar = document.querySelector(".sidebar");
                if (!sidebar) return;
                
                // Ensure sidebar is in minimized state
                sidebar.classList.add('close');
                sidebar.style.width = '78px';
                sidebar.style.minWidth = '78px';
                sidebar.style.maxWidth = '78px';
                
                // Disable toggle buttons
                const toggles = sidebar.querySelectorAll('.bx-menu, .logo_name');
                toggles.forEach(toggle => {
                    const clone = toggle.cloneNode(true);
                    toggle.parentNode.replaceChild(clone, toggle);
                });
                
                // Style nav links for minimized sidebar
                const navLinks = sidebar.querySelectorAll('.nav-links li');
                navLinks.forEach(item => {
                    item.style.width = '78px';
                    item.style.overflow = 'hidden';
                    
                    const linkName = item.querySelector('.link_name');
                    if (linkName) {
                        linkName.style.opacity = '0';
                        linkName.style.pointerEvents = 'none';
                    }
                    
                    const subMenu = item.querySelector('.sub-menu');
                    if (subMenu) {
                        subMenu.style.position = 'absolute';
                        subMenu.style.left = '100%';
                        subMenu.style.top = '0';
                        subMenu.style.zIndex = '1000';
                        
                        const tooltipLinkName = subMenu.querySelector('.link_name');
                        if (tooltipLinkName) {
                            tooltipLinkName.style.display = 'block';
                            tooltipLinkName.style.opacity = '1';
                            tooltipLinkName.style.pointerEvents = 'auto';
                        }
                    }
                });
            }
            
            // Mobile view setup
            function setupMobileView() {
                const conversationList = document.querySelector('.conversation-list');
                
                if (window.innerWidth <= 768) {
                    if (!document.querySelector('.toggle-conversation-list')) {
                        const toggleButton = document.createElement('button');
                        toggleButton.className = 'toggle-conversation-list';
                        toggleButton.innerHTML = '<i class="bi bi-chat-left-text-fill"></i>';
                        document.body.appendChild(toggleButton);
                        
                        toggleButton.addEventListener('click', function() {
                            conversationList.classList.toggle('hidden');
                        });
                    }
                    
                    if (document.querySelector('.conversation-item.active')) {
                        conversationList.classList.add('hidden');
                    }
                } else {
                    const toggleButton = document.querySelector('.toggle-conversation-list');
                    if (toggleButton) {
                        toggleButton.remove();
                    }
                    
                    conversationList.classList.remove('hidden');
                }
            }
            
            // Initialize leave group functionality
            document.addEventListener('click', function(e) {
                if (e.target.closest('.leave-group-btn')) {
                    const btn = e.target.closest('.leave-group-btn');
                    currentLeaveGroupId = btn.dataset.conversationId;
                }
            });
            
            document.getElementById('confirmLeaveGroup')?.addEventListener('click', function() {
                if (!currentLeaveGroupId) return;
                
                fetch('{{ route($rolePrefix.'.messaging.leave-group') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ conversation_id: currentLeaveGroupId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        bootstrap.Modal.getInstance(document.getElementById('leaveGroupModal')).hide();
                        window.location.href = "{{ route($rolePrefix.'.messaging.index') }}";
                    } else {
                        alert(data.message || 'Failed to leave group');
                    }
                })
                .catch(error => {
                    console.error('Error leaving group:', error);
                    alert('Failed to leave group. Please try again.');
                });
            });
            
            // New conversation form handler
            document.getElementById('newConversationForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                fetch('{{ route($rolePrefix.'.messaging.create') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        bootstrap.Modal.getInstance(document.getElementById('newConversationModal')).hide();
                        window.location.href = "{{ url('/" . $rolePrefix . "/messaging') }}?conversation=" + data.conversation_id;
                    } else {
                        alert(data.message || 'Failed to create conversation');
                    }
                })
                .catch(error => {
                    console.error('Error creating conversation:', error);
                    alert('Failed to create conversation. Please try again.');
                });
            });
            
            // New group form handler
            document.getElementById('newGroupForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                fetch('{{ route($rolePrefix.'.messaging.create-group') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        bootstrap.Modal.getInstance(document.getElementById('newGroupModal')).hide();
                        window.location.href = "{{ url('/" . $rolePrefix . "/messaging') }}?conversation=" + data.conversation_id;
                    } else {
                        alert(data.message || 'Failed to create group');
                    }
                })
                .catch(error => {
                    console.error('Error creating group:', error);
                    alert('Failed to create group. Please try again.');
                });
            });
            
            // User type change handler
            document.getElementById('userType').addEventListener('change', function() {
                const userType = this.value;
                const participantSelect = document.getElementById('participantSelect');
                
                participantSelect.innerHTML = '<option value="">Loading users...</option>';
                
                fetch("{{ route($rolePrefix.'.messaging.get-users') }}?type=" + userType, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    participantSelect.innerHTML = '<option value="">Select a user</option>';
                    
                    if (data.users && data.users.length > 0) {
                        data.users.forEach(user => {
                            const name = user.first_name + ' ' + (user.last_name || '');
                            participantSelect.innerHTML += `<option value="${user.id}">${name}</option>`;
                        });
                    } else {
                        participantSelect.innerHTML = '<option value="">No users found</option>';
                    }
                })
                .catch(error => {
                    console.error('Error fetching users:', error);
                    participantSelect.innerHTML = '<option value="">Error loading users</option>';
                });
            });
            
            // User search functionality
            document.getElementById('userSearch').addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const participantSelect = document.getElementById('participantSelect');
                
                Array.from(participantSelect.options).forEach(option => {
                    const optionText = option.text.toLowerCase();
                    option.style.display = optionText.includes(searchTerm) ? '' : 'none';
                });
            });
            
            // Conversation search
            const searchInput = document.getElementById('conversationSearch');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();
                    const conversationItems = document.querySelectorAll('.conversation-item');
                    let foundResults = false;
                    
                    conversationItems.forEach(function(item) {
                        const conversationName = item.querySelector('.conversation-title span')?.textContent.toLowerCase() || '';
                        const participantNames = item.dataset.participantNames ? 
                            item.dataset.participantNames.toLowerCase() : '';
                        const groupParticipants = item.dataset.groupParticipants ? 
                            item.dataset.groupParticipants.toLowerCase() : '';
                        
                        if (searchTerm === '' || 
                            conversationName.includes(searchTerm) || 
                            participantNames.includes(searchTerm) ||
                            groupParticipants.includes(searchTerm)) {
                            item.style.display = '';
                            foundResults = true;
                        } else {
                            item.style.display = 'none';
                        }
                    });
                    
                    const noResultsMessage = document.getElementById('noSearchResults');
                    if (!foundResults && searchTerm !== '') {
                        if (!noResultsMessage) {
                            const message = document.createElement('div');
                            message.id = 'noSearchResults';
                            message.className = 'text-center py-3 text-muted';
                            message.textContent = 'No conversations found matching "' + searchTerm + '"';
                            document.querySelector('.conversation-list-items').appendChild(message);
                        }
                    } else if (noResultsMessage) {
                        noResultsMessage.remove();
                    }
                });
            }
            
            // Fix message scrolling
            function fixMessageScroll() {
                const messagesContainer = document.getElementById('messagesContainer');
                if (!messagesContainer) return;
                
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
                
                const images = messagesContainer.querySelectorAll('img');
                images.forEach(img => {
                    if (img.complete) {
                        messagesContainer.scrollTop = messagesContainer.scrollHeight;
                    } else {
                        img.addEventListener('load', function() {
                            messagesContainer.scrollTop = messagesContainer.scrollHeight;
                        });
                    }
                });
            }
            
            // Run initializations
            setupMinimizedSidebar();
            setupMobileView();
            addConversationClickHandlers();
            
            // Set up polling for new messages every 20 seconds
            const refreshInterval = setInterval(function() {
                // Don't refresh if explicitly prevented
                if (Date.now() < window.preventRefreshUntil) {
                    return;
                }
                
                refreshActiveConversation();
            }, 20000);
            
            // Clean up on page unload
            window.addEventListener('beforeunload', function() {
                clearInterval(refreshInterval);
            });
            
            // Check for conversation in URL
            const urlParams = new URLSearchParams(window.location.search);
            const conversationId = urlParams.get('conversation');
            if (conversationId) {
                const conversationItem = document.querySelector(`.conversation-item[data-conversation-id="${conversationId}"]`);
                if (conversationItem) {
                    conversationItem.click();
                }
            }
            
            // Setup MutationObserver for conversation content changes
            const conversationContent = document.getElementById('conversationContent');
            if (conversationContent) {
                const observer = new MutationObserver(function() {
                    fixMessageScroll();
                });
                
                observer.observe(conversationContent, {
                    childList: true,
                    subtree: true
                });
            }
            
            // Update unread count on load
            updateNavbarUnreadCount();
        });
    </script>
</body>
</html>