<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="role-prefix" content="{{ $rolePrefix }}">
<script>const role_base_url = '{{ url("/".$rolePrefix) }}';</script>
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

        <!-- Mobile Toggle Button for Conversation List -->
        <button class="toggle-conversation-list">
            <i class="bi bi-chat-left-text-fill"></i>
        </button>

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
    
    <script>
        // ============= GLOBAL VARIABLES AND CONFIGURATION =============
        const DEBUG = true;
        let currentLeaveGroupId = null;

        // Timing variables to prevent conflicts
        window.lastRefreshTimestamp = 0;
        window.lastMessageSendTimestamp = 0;
        window.messageFormInitialized = false;
        window.isRefreshing = false;
        window.preventRefreshUntil = 0;
        window.lastBlurContent = '';
        window.lastBlurTime = 0;
        window.lastTypingTime = 0;

        // Debug logging function
        function debugLog(...args) {
            if (DEBUG) {
                console.log('[DEBUG]', ...args);
            }
        }

        const rolePrefix = document.querySelector('meta[name="role-prefix"]')?.getAttribute('content') || 'admin';
        window.route_prefix = rolePrefix + '.messaging';

        function getRouteUrl(routeName) {
            // Extract just the endpoint part after the last dot
            const endpoint = routeName.replace(/^.*\./, '');
            
            // Build the full URL
            const baseUrl = `${window.location.origin}/${rolePrefix}/messaging/`;
            const fullUrl = baseUrl + endpoint;
            
            console.log(`Converting route ${routeName} to URL: ${fullUrl}`);
            return fullUrl;
        }

        let lastKnownScrollPosition = 0;
        let scrollTimeoutId = null;

        // Add this helper function right after the loadConversation function
        function logResponseDetails(response) {
            console.log('Response headers:', {
                'content-type': response.headers.get('content-type'),
                'status': response.status
            });
            
            return response.text().then(text => {
                try {
                    const json = JSON.parse(text);
                    console.log('Response parsed as JSON:', json);
                    return json;
                } catch (e) {
                    console.error('Response is not valid JSON:', text.substring(0, 500) + '...');
                    throw new Error('Invalid JSON response');
                }
            });
        }

        // Helper to determine if user is at bottom of container
        function isAtBottom(container) {
            const buffer = Math.min(150, container.clientHeight * 0.2); // 20% of container height or 150px
            return container.scrollHeight - container.scrollTop - container.clientHeight <= buffer;
        }

        // Helper to extract message HTML from content
        function extractMessagesHTML(html) {
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            const messagesContainer = tempDiv.querySelector('.messages-container');
            return messagesContainer ? messagesContainer.innerHTML : '';
        }

        // Helper function to determine file icon class based on file type
        function getFileIconClass(file) {
            const fileName = file.name.toLowerCase();
            
            if (file.type.includes('pdf') || fileName.endsWith('.pdf')) {
                return 'bi-file-earmark-pdf';
            } else if (fileName.endsWith('.doc') || fileName.endsWith('.docx')) {
                return 'bi-file-earmark-word';
            } else if (fileName.endsWith('.xls') || fileName.endsWith('.xlsx')) {
                return 'bi-file-earmark-excel';
            } else if (fileName.endsWith('.txt')) {
                return 'bi-file-earmark-text';
            } else if (file.type.startsWith('image/')) {
                return 'bi-file-earmark-image';
            } else {
                return 'bi-file-earmark';
            }
        }

        function createFilePreview(file, container) {
            const filePreview = document.createElement('div');
            filePreview.className = 'file-preview';
            
            // Loading state
            const loadingContainer = document.createElement('div');
            loadingContainer.className = 'file-loading';
            loadingContainer.innerHTML = '<div class="spinner-border spinner-border-sm text-primary" role="status"></div>';
            filePreview.appendChild(loadingContainer);
            
            // Add file name below loading spinner
            const fileName = document.createElement('div');
            fileName.className = 'file-name';
            fileName.textContent = file.name;
            filePreview.appendChild(fileName);
            
            // Add remove button
            const removeBtn = document.createElement('div');
            removeBtn.className = 'remove-file';
            removeBtn.innerHTML = '&times;';
            removeBtn.addEventListener('click', function() {
                filePreview.remove();
                
                // Create a new FileList without this file
                const dataTransfer = new DataTransfer();
                const fileInput = document.getElementById('fileUpload');
                Array.from(fileInput.files || []).forEach(f => {
                    if (f.name !== file.name || f.size !== file.size) {
                        dataTransfer.items.add(f);
                    }
                });
                if (fileInput) fileInput.files = dataTransfer.files;
            });
            filePreview.appendChild(removeBtn);
            container.appendChild(filePreview);
            
            // Process preview based on file type
            if (file.type.startsWith('image/')) {
                const img = new Image();
                img.onload = function() {
                    loadingContainer.remove();
                    filePreview.insertBefore(img, filePreview.firstChild);
                };
                img.onerror = function() {
                    loadingContainer.innerHTML = '<i class="bi bi-exclamation-triangle text-warning"></i>';
                };
                img.src = URL.createObjectURL(file);
                img.className = 'file-preview-img';
            } else {
                // For non-image files, show icon based on file type
                setTimeout(() => {
                    const iconClass = getFileIconClass(file);
                    loadingContainer.innerHTML = `<i class="bi ${iconClass} fs-2"></i>`;
                }, 500);
            }

            // Store file in global storage
            const conversationId = document.querySelector('input[name="conversation_id"]')?.value;
            if (conversationId) {
                if (!window.savedAttachments) {
                    window.savedAttachments = new Map();
                }
                
                if (!window.savedAttachments.has(conversationId)) {
                    window.savedAttachments.set(conversationId, []);
                }
                
                const files = window.savedAttachments.get(conversationId);
                // Only add if not already there
                if (!files.some(f => f.name === file.name && f.size === file.size)) {
                    files.push(file);
                    console.log(`Stored file in window.savedAttachments: ${file.name}`);
                }
            }
        }

        function syncFilePreviewsWithInput() {
                const filePreviewContainer = document.getElementById('filePreviewContainer');
                const fileInput = document.getElementById('fileUpload');
                
                if (!filePreviewContainer || !fileInput) return;
                
                // If there are previews but no files in the input, fix the input
                const previews = filePreviewContainer.querySelectorAll('.file-preview');
                if (previews.length > 0 && (!fileInput.files || fileInput.files.length === 0)) {
                    console.log('Reconnecting file previews to file input');
                    
                    // Create a new FormData to collect files from previews
                    const dataTransfer = new DataTransfer();
                    
                    // Find saved files in our global storage
                    const conversationId = document.querySelector('input[name="conversation_id"]')?.value;
                    if (conversationId && window.savedAttachments && window.savedAttachments.has(conversationId)) {
                        const files = window.savedAttachments.get(conversationId);
                        if (files && files.length > 0) {
                            files.forEach(file => {
                                dataTransfer.items.add(file);
                            });
                            
                            // Assign the files back to the input
                            fileInput.files = dataTransfer.files;
                            console.log('Restored', dataTransfer.files.length, 'files to input');
                        }
                    }
                }
            }

        function smoothRefreshConversationList() {
            console.log('Refreshing conversation list...');
            
            // INCREASE DELAY - Give server more time to process the message
            setTimeout(() => {
                // Strong cache-busting
                const timestamp = new Date().getTime();
                const url = getRouteUrl(route_prefix + '.get-conversations') + 
                        '?nocache=' + timestamp;
                
                fetch(url, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Cache-Control': 'no-cache, no-store, must-revalidate',
                        'Pragma': 'no-cache',
                        'Expires': '0',
                        'If-Modified-Since': '0'
                    }
                })
                .then(response => {
                    console.log('Conversation list response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`Error fetching conversations: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Received conversation list data:', data.success);
                    
                    if (data.success && data.html) {
                        // Find the container
                        const conversationListItems = document.querySelector('.conversation-list-items');
                        if (!conversationListItems) {
                            console.error('Conversation list items container not found');
                            return;
                        }
                        
                        // Get currently active conversation ID
                        const activeConversationId = document.querySelector('.conversation-item.active')?.dataset.conversationId;
                        
                        // CRITICAL FIX: Parse the new HTML before inserting
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(data.html, 'text/html');
                        const newContent = doc.body.innerHTML;
                        
                        // Force-update the HTML immediately
                        conversationListItems.innerHTML = newContent;
                        console.log('Updated conversation list HTML with fresh content');
                        
                        // Re-add active class to current conversation
                        if (activeConversationId) {
                            const newActiveItem = conversationListItems.querySelector(
                                `.conversation-item[data-conversation-id="${activeConversationId}"]`
                            );
                            if (newActiveItem) {
                                newActiveItem.classList.add('active');
                            }
                        }
                        
                        // Reattach click handlers
                        addConversationClickHandlers();
                    } else {
                        console.error('Invalid conversation list data received:', data);
                    }
                })
                .catch(error => {
                    console.error('Error refreshing conversation list:', error);
                });
            }, 800); // INCREASED DELAY from 300ms to 800ms for server processing
        }

        function handleFileInputChange(event) {
            const fileInput = event.target;
            const files = fileInput.files;
            
            if (files.length === 0) return;
            
            const filePreviewContainer = document.getElementById('filePreviewContainer');
            if (!filePreviewContainer) return;
            
            // Process each selected file
            for (let i = 0; i < files.length; i++) {
                if (isValidFileType(files[i])) {
                    createFilePreview(files[i], filePreviewContainer);
                } else {
                    const fileErrorContainer = document.getElementById('fileErrorContainer') || createErrorContainer();
                    fileErrorContainer.innerHTML = 'Invalid file type. Please select images, PDFs, Word, Excel, or text files.';
                    fileErrorContainer.classList.remove('d-none');
                }
            }
        }

        // ============= INPUT PROTECTION =============
        // Override the textarea value setter to detect and prevent unwanted clearing
        window.intentionalClear = false;

        const originalValueSetter = Object.getOwnPropertyDescriptor(HTMLTextAreaElement.prototype, 'value').set;
        Object.defineProperty(HTMLTextAreaElement.prototype, 'value', {
            set(val) {
                // Allow clearing if it's intentional (after message send)
                if (val === '' && this.value !== '' && !window.intentionalClear && Date.now() - window.lastBlurTime < 3000) {
                    console.warn('Prevented textarea from being cleared unexpectedly');
                    window.preventRefreshUntil = Date.now() + 5000; // Prevent refresh for 5 seconds
                    return;
                }
                
                // Reset the flag after use
                if (window.intentionalClear) {
                    window.intentionalClear = false;
                }
                
                originalValueSetter.call(this, val);
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

        // ============= UTILITY FUNCTIONS =============
        // Function to mark a conversation as read
        function markConversationAsRead(conversationId) {
            if (!conversationId) return;
            
            fetch(getRouteUrl(route_prefix + '.mark-as-read'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ conversation_id: conversationId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the UI to remove unread indicators
                    const conversationItem = document.querySelector(`.conversation-item[data-conversation-id="${conversationId}"]`);
                    if (conversationItem) {
                        conversationItem.classList.remove('unread');
                        const unreadBadge = conversationItem.querySelector('.unread-badge');
                        if (unreadBadge) {
                            unreadBadge.remove();
                        }
                    }
                    // Update navbar count
                    updateNavbarUnreadCount();
                }
            })
            .catch(error => {
                console.error('Error marking conversation as read:', error);
            });
        }

        // Function to update navbar unread count
        function updateNavbarUnreadCount() {
            fetch(getRouteUrl(route_prefix + '.unread-count'))
            .then(response => response.json())
            .then(data => {
                // Update the badge in navbar if it exists
                const messageCount = document.querySelector('.message-count');
                if (messageCount) {
                    if (data.count > 0) {
                        messageCount.textContent = data.count;
                        messageCount.style.display = 'inline-block';
                    } else {
                        messageCount.style.display = 'none';
                    }
                }
            })
            .catch(error => {
                console.error('Error updating unread count:', error);
            });
        }

        // Helper function to extract just the messages HTML
        function extractMessagesHTML(html) {
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            const messagesContainer = tempDiv.querySelector('.messages-container');
            return messagesContainer ? messagesContainer.innerHTML : '';
        }

        // Check file type validity
        function isValidFileType(file) {
            const allowedTypes = [
                'image/jpeg', 'image/png', 'image/gif', 'image/webp',
                'application/pdf', 'application/msword', 
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel', 
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'text/plain'
            ];
            
            // Check by MIME type first
            if (allowedTypes.includes(file.type)) {
                return true;
            }
            
            // Fallback to extension check for certain file types
            const fileName = file.name.toLowerCase();
            if (fileName.endsWith('.jpg') || fileName.endsWith('.jpeg') || 
                fileName.endsWith('.png') || fileName.endsWith('.gif') || 
                fileName.endsWith('.webp') || fileName.endsWith('.pdf') ||
                fileName.endsWith('.doc') || fileName.endsWith('.docx') ||
                fileName.endsWith('.xls') || fileName.endsWith('.xlsx') ||
                fileName.endsWith('.txt')) {
                return true;
            }
            
            return false;
        }

        // ============= CONVERSATION LIST AND LOADING =============
        // Function to add click handlers to conversation items
        let conversationClicksInitialized = false;

        function addConversationClickHandlers() {
            console.log('Adding conversation click handlers');
            
            // Use event delegation instead of attaching to each item
            const conversationListContainer = document.querySelector('.conversation-list-items');
            
            if (!conversationListContainer) {
                console.error('Conversation list container not found');
                return;
            }
            
            // Remove any existing handler
            conversationListContainer.removeEventListener('click', handleConversationListClick);
            
            // Add a single event handler to the container
            conversationListContainer.addEventListener('click', handleConversationListClick);
            
            console.log('Added conversation click handler to container');
        }

        // Add this new function to handle clicks through event delegation
        function handleConversationListClick(e) {
            // Find the closest conversation item
            const conversationItem = e.target.closest('.conversation-item');
            
            if (!conversationItem) return;
            
            console.log('Conversation clicked:', conversationItem.dataset.conversationId);
            
            // Remove active class from all items
            document.querySelectorAll('.conversation-item').forEach(el => {
                el.classList.remove('active');
            });
            
            // Add active class to clicked item
            conversationItem.classList.add('active');
            
            // Get conversation ID and load it
            const conversationId = conversationItem.dataset.conversationId;
            if (!conversationId) {
                console.error('Missing conversation ID on clicked item', conversationItem);
                return;
            }
            
            // Load the conversation
            loadConversation(conversationId);
            
            // Mark conversation as read
            markConversationAsRead(conversationId);
            
            // Hide conversation list on mobile after selecting
            if (window.innerWidth < 768) {
                const conversationList = document.querySelector('.conversation-list');
                if (conversationList) {
                    conversationList.classList.add('hidden');
                }
            }
        }

        // Function to load conversation
        function loadConversation(conversationId) {
            console.log('Loading conversation:', conversationId);
            
            if (!conversationId) {
                console.error('No conversation ID provided');
                return;
            }
            
            // Show loading state
            const messageArea = document.querySelector('.message-area');
            if (!messageArea) {
                console.error('Message area not found');
                return;
            }
            
            messageArea.innerHTML = `
                <div id="conversationContent" class="d-flex justify-content-center align-items-center h-100">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `;
            
            // Generate URL with protection against malformed URLs
            const url = getRouteUrl(route_prefix + '.get-conversation') + '?id=' + conversationId;
            console.log('Fetching conversation from URL:', url);
            
            // Fetch conversation content via AJAX
            fetch(url, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Received conversation data:', data);
                
                if (data.success) {
                    // Update the message area with the conversation
                    messageArea.innerHTML = data.html;
                    
                    // Initialize the message form
                    window.messageFormInitialized = false;
                    initializeMessageForm(conversationId);
                    
                    // Scroll to bottom of messages container
                    const messagesContainer = document.getElementById('messagesContainer');
                    if (messagesContainer) {
                        messagesContainer.scrollTop = messagesContainer.scrollHeight;
                    }
                } else {
                    messageArea.innerHTML = `
                        <div class="empty-state">
                            <i class="bi bi-exclamation-triangle-fill empty-icon"></i>
                            <h4>Error Loading Conversation</h4>
                            <p>${data.message || 'Could not load conversation. Please try again.'}</p>
                        </div>
                    `;
                    console.error('Failed to load conversation:', data.message);
                }
                
                // Mark conversation as read
                markConversationAsRead(conversationId);
            })
            .catch(error => {
                console.error('Error loading conversation:', error);
                messageArea.innerHTML = `
                    <div class="empty-state">
                        <i class="bi bi-exclamation-triangle-fill empty-icon"></i>
                        <h4>Error Loading Conversation</h4>
                        <p>Could not load conversation. Please try again.</p>
                    </div>
                `;
            });
        }

        // ============= MESSAGE FORM HANDLING =============
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
            
            // Create error container if it doesn't exist
            const fileErrorContainer = document.getElementById('fileErrorContainer') || createErrorContainer();

            function createErrorContainer() {
                const fileErrorContainer = document.createElement('div');
                fileErrorContainer.id = 'fileErrorContainer';
                fileErrorContainer.className = 'file-error-container alert alert-danger d-none';
                
                const filePreviewContainer = document.getElementById('filePreviewContainer');
                if (filePreviewContainer) {
                    filePreviewContainer.parentNode.insertBefore(fileErrorContainer, filePreviewContainer);
                }
                
                return fileErrorContainer;
            }

            // Restore previous content if this is first initialization (not refresh)
            if (!isRefresh && textarea && textarea.value.trim() === '' && conversationId) {
                const savedContent = localStorage.getItem('messageContent_' + conversationId);
                if (savedContent) {
                    textarea.value = savedContent;
                    textarea.style.height = 'auto';
                    textarea.style.height = (textarea.scrollHeight) + 'px';
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
                    this.style.height = 'auto';
                    this.style.height = (this.scrollHeight) + 'px';
                    
                    // Save content to local storage
                    if (conversationId) {
                        localStorage.setItem('messageContent_' + conversationId, this.value);
                    }
                    
                    // Update typing timestamp
                    window.lastTypingTime = Date.now();
                });
                
                // Focus/blur protection
                textarea.addEventListener('blur', function() {
                    window.lastBlurTime = Date.now();
                    window.lastBlurContent = this.value;
                });
                
                textarea.addEventListener('focus', function() {
                    // If content was cleared unexpectedly, restore it
                    if (this.value === '' && window.lastBlurContent && 
                        Date.now() - window.lastBlurTime < 3000) {
                        console.warn('Restored textarea content after unexpected clear');
                        this.value = window.lastBlurContent;
                    }
                });
            }

            if (textarea) {
                // Clear error message when typing starts
                textarea.addEventListener('input', function() {
                    const fileErrorContainer = document.getElementById('fileErrorContainer');
                    if (fileErrorContainer && !fileErrorContainer.classList.contains('d-none')) {
                        fileErrorContainer.classList.add('d-none');
                        fileErrorContainer.innerHTML = '';
                    }
                });
            }
            
            // File attachment handling
            if (attachmentBtn && fileInput) {
                // Handle attachment button click
                attachmentBtn.addEventListener('click', function() {
                    fileInput.click();
                    // Clear errors when opening file picker
                    fileErrorContainer.classList.add('d-none');
                    fileErrorContainer.textContent = '';
                });
                
                if (fileInput && filePreviewContainer) {
                    fileInput.addEventListener('change', function() {
                        // Clear previous error messages
                        fileErrorContainer.classList.add('d-none');
                        fileErrorContainer.innerHTML = '';
                        
                        if (this.files.length === 0) return;
                        
                        // Check for maximum of 5 files
                        const existingFiles = filePreviewContainer.querySelectorAll('.file-preview').length;
                        const totalFilesAfterAdd = existingFiles + this.files.length;
                        
                        if (totalFilesAfterAdd > 5) {
                            fileErrorContainer.innerHTML = 'You can upload a maximum of 5 files at once';
                            fileErrorContainer.classList.remove('d-none');
                            return;
                        }
                        
                        const errors = [];
                        const maxFileSize = 10 * 1024 * 1024; // 10MB
                        
                        // Validate each file
                        Array.from(this.files).forEach(file => {
                            // Size validation
                            if (file.size > maxFileSize) {
                                errors.push(`File "${file.name}" exceeds the 10MB limit`);
                                return;
                            }
                            
                            // Type validation
                            if (!isValidFileType(file)) {
                                errors.push(`File "${file.name}" is not an allowed type. Please use JPG, PNG, GIF, WEBP, PDF, DOC, DOCX, XLS, XLSX, or TXT files.`);
                                return;
                            }
                            
                            // Create preview for valid files
                            createFilePreview(file, filePreviewContainer);
                        });
                        
                        // Show errors if any
                        if (errors.length > 0) {
                            fileErrorContainer.innerHTML = errors.map(msg => `<div>${msg}</div>`).join('');
                            fileErrorContainer.classList.remove('d-none');
                        }
                        
                        // Reset the file input value to allow selecting the same file again
                        // but preserve the FileList for form submission
                        const fileList = this.files;
                        this.value = '';  
                        Object.defineProperty(this, 'files', {
                            value: fileList,
                            writable: true
                        });
                    });
                }
            }
            
            // Message form submission
            messageForm.addEventListener('submit', function(e) {
                e.preventDefault();

                // Clear any existing error messages
                const fileErrorContainer = document.getElementById('fileErrorContainer');
                if (fileErrorContainer) {
                    fileErrorContainer.classList.add('d-none');
                    fileErrorContainer.innerHTML = '';
                }
                
                // Get direct references to all required elements
                const textarea = document.getElementById('messageContent');
                const fileInput = document.getElementById('fileUpload');
                const filePreviewContainer = document.getElementById('filePreviewContainer');

                // Ensure global storage exists
                if (!window.savedAttachments) {
                    window.savedAttachments = new Map();
                }
                
                // Store the conversation ID
                const conversationId = document.querySelector('input[name="conversation_id"]').value;

                // Debug the current file input state
                console.log('Before sync - fileInput files:', fileInput?.files?.length || 0);

                // Basic validation
                const hasTextContent = textarea.value.trim() !== '';
                const hasFilePreview = filePreviewContainer && filePreviewContainer.querySelectorAll('.file-preview').length > 0;
                
                if (!hasTextContent && !hasFilePreview) {
                    const errorContainer = document.getElementById('fileErrorContainer') || createErrorContainer();
                    errorContainer.innerHTML = 'Please enter a message or attach a file.';
                    errorContainer.classList.remove('d-none');
                    return;
                }
                
                // Show "sending" state
                const sendBtn = document.getElementById('sendMessageBtn');
                const originalBtnContent = sendBtn.innerHTML;
                sendBtn.disabled = true;
                sendBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
                
                // Create a completely fresh FormData object
                const formData = new FormData();
                
                // Add form fields manually
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                formData.append('conversation_id', document.querySelector('input[name="conversation_id"]').value);
                formData.append('content', textarea.value);
                
                // Add files directly from the input element without any manipulation
                if (fileInput && fileInput.files && fileInput.files.length > 0) {
                    console.log(`DIRECT UPLOAD: Found ${fileInput.files.length} files`);
                    
                    for (let i = 0; i < fileInput.files.length; i++) {
                        // Log file details before appending
                        console.log(`File ${i+1}: ${fileInput.files[i].name} (${fileInput.files[i].size} bytes)`);
                        
                        // Just append with standard array notation
                        formData.append('attachments[]', fileInput.files[i]);
                    }
                }

                // Check if we have previews but no files
                if (filePreviewContainer && filePreviewContainer.querySelectorAll('.file-preview').length > 0) {
                    if (!fileInput.files || fileInput.files.length === 0) {
                        console.log('Detected file previews without fileInput.files - fixing');
                        
                        // Create new Files list
                        const dataTransfer = new DataTransfer();
                        
                        // Get file objects from preview elements
                        filePreviewContainer.querySelectorAll('.file-preview').forEach(preview => {
                            const fileName = preview.querySelector('.file-name')?.textContent;
                            if (fileName) {
                                // Try to find the file in savedAttachments or recreate it
                                let foundFile = null;
                                
                                // Look in global storage
                                if (window.savedAttachments && window.savedAttachments.has(conversationId)) {
                                    const savedFiles = window.savedAttachments.get(conversationId);
                                    if (savedFiles && savedFiles.length > 0) {
                                        console.log(`Using ${savedFiles.length} files from savedAttachments`);
                                        
                                        for (let i = 0; i < savedFiles.length; i++) {
                                            // IMPORTANT: Use the exact name format Laravel expects
                                            formData.append('attachments[]', savedFiles[i]);
                                            console.log(`Directly added file to FormData: ${savedFiles[i].name} (${Math.round(savedFiles[i].size/1024)}KB)`);
                                        }
                                    }
                                } else if (fileInput && fileInput.files && fileInput.files.length > 0) {
                                    // Fall back to fileInput.files if savedAttachments doesn't have entries
                                    console.log(`Using ${fileInput.files.length} files from fileInput`);
                                    
                                    for (let i = 0; i < fileInput.files.length; i++) {
                                        formData.append('attachments[]', fileInput.files[i]);
                                        console.log(`Added file from input to FormData: ${fileInput.files[i].name} (${Math.round(fileInput.files[i].size/1024)}KB)`);
                                    }
                                }
                                
                                if (foundFile) {
                                    dataTransfer.items.add(foundFile);
                                    console.log(`Retrieved file from storage: ${foundFile.name}`);
                                }
                            }
                        });
                        
                        // If we found any files, set them to the input
                        if (dataTransfer.files.length > 0) {
                            fileInput.files = dataTransfer.files;
                            console.log(`Restored ${dataTransfer.files.length} files to input`);
                        }
                    }
                }

                // Log what we have after sync attempt
                console.log('After sync - fileInput files:', fileInput?.files?.length || 0);
                if (fileInput?.files?.length > 0) {
                    for (let i = 0; i < fileInput.files.length; i++) {
                        console.log(`File ${i+1}:`, fileInput.files[i].name, fileInput.files[i].size);
                    }
                }
                
                // Log all form data to verify contents
                console.log('FINAL FORM DATA:');
                for (let pair of formData.entries()) {
                    console.log(`${pair[0]}: ${pair[1] instanceof File ? 
                        `File: ${pair[1].name} (${Math.round(pair[1].size/1024)}KB)` : 
                        pair[1].toString().substring(0, 30)}`);
                }
                
                // Make a direct fetch request with proper AJAX headers
                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                        // DO NOT SET 'Content-Type' here - it will be set automatically with proper boundary
                    }
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    
                    // Check content type before parsing
                    const contentType = response.headers.get('content-type');
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    
                    // Only parse as JSON if it's actually JSON
                    if (contentType && contentType.includes('application/json')) {
                        return response.json();
                    } else {
                        // Handle HTML or other non-JSON responses
                        return response.text().then(text => {
                            console.error('Received non-JSON response:', text.substring(0, 500) + '...');
                            throw new Error('Server returned non-JSON response');
                        });
                    }
                })
                .then(data => {
                    console.log('Message sent successfully:', data);
                    
                    if (data.success) {
                        // CRITICAL: Reset UI elements properly
                        // 1. Clear textarea and reset height
                        window.intentionalClear = true;
                        textarea.value = '';

                        if (textarea.style) {
                            textarea.style.height = 'auto';
                        }
                        
                        // 2. Clear file previews if they exist
                        if (filePreviewContainer) {
                            filePreviewContainer.innerHTML = '';
                        }
                        
                        // 3. Clear file input by resetting value
                        // After sending a message and clearing the input, we need to reattach handlers
                        if (fileInput) {
                            // First, clear the FileList by resetting value
                            fileInput.value = '';

                            // Clone and replace the file input to completely reset it
                            const newFileInput = fileInput.cloneNode(false);
                            fileInput.parentNode.replaceChild(newFileInput, fileInput);
                            newFileInput.addEventListener('change', handleFileInputChange);

                            const attachmentBtn = document.getElementById('attachmentBtn');
                            if (attachmentBtn) {
                                // Remove any existing click handlers
                                attachmentBtn.replaceWith(attachmentBtn.cloneNode(true));
                                
                                // Get the fresh reference
                                const newAttachmentBtn = document.getElementById('attachmentBtn');
                                
                                // Add the click handler to the button
                                newAttachmentBtn.addEventListener('click', function() {
                                    document.getElementById('fileUpload').click();
                                });
                                
                                console.log('Attachment button reconnected to new file input');
                            }
                            
                            console.log('File input completely reset with new event handlers');
                        }

                        // Clear window.savedAttachments for this conversation
                        if (window.savedAttachments && window.savedAttachments.has(conversationId)) {
                            window.savedAttachments.set(conversationId, []);
                            console.log('Cleared saved attachments for conversation', conversationId);
                        }
                        
                        // 4. Reset the sending state IMMEDIATELY
                        sendBtn.disabled = false;
                        sendBtn.innerHTML = originalBtnContent;
                        
                        // 5. Trigger conversation refresh with delay to ensure server processing
                        setTimeout(function() {
                            const conversationId = document.querySelector('input[name="conversation_id"]').value;
                            console.log("Refreshing conversation:", conversationId);
                            forceRefreshConversation(conversationId);
                            
                            // Also refresh the conversation list
                            setTimeout(() => {
                                smoothRefreshConversationList();
                            }, 300);
                        }, 500);
                    } else {
                        // Handle failure
                        console.error('Failed to send message:', data.error || 'Unknown error');
                        sendBtn.disabled = false;
                        sendBtn.innerHTML = originalBtnContent;
                        
                        const errorContainer = document.getElementById('fileErrorContainer') || createErrorContainer();
                        errorContainer.innerHTML = 'Failed to send message. Please try again.';
                        errorContainer.classList.remove('d-none');
                    }
                })
                .catch(error => {
                    console.error('Error sending message:', error);
                    sendBtn.disabled = false;
                    sendBtn.innerHTML = originalBtnContent;
                    
                    const errorContainer = document.getElementById('fileErrorContainer') || createErrorContainer();
                    errorContainer.innerHTML = 'An error occurred while sending your message. Please try again.';
                    errorContainer.classList.remove('d-none');
                });
            });
        }

        // ============= REFRESH FUNCTIONALITY - FIXED =============
        // Function to refresh active conversation with protection for attachments and scroll
        window.refreshActiveConversation = function() {
            // Skip if conditions prevent refresh
            if (Date.now() < window.preventRefreshUntil) {
                debugLog('Refresh prevented by time lock');
                return;
            }

            // IMPROVED CHECK: Check for file previews more reliably
            const filePreviewContainer = document.getElementById('filePreviewContainer');
            if (filePreviewContainer && filePreviewContainer.querySelectorAll('.file-preview').length > 0) {
                debugLog('Refresh prevented - attachments present');
                return;
            }

            // Don't refresh if the user is actively typing
            const textarea = document.getElementById('messageContent');
            if (textarea && textarea.value.trim() && Date.now() - window.lastTypingTime < 5000) {
                debugLog('Refresh prevented - user is typing');
                return;
            }

            // Don't refresh if we just sent a message in the last 3 seconds
            if (Date.now() - window.lastMessageSendTimestamp < 3000) {
                debugLog('Refresh prevented - message recently sent');
                return;
            }

            // Don't refresh if already refreshing
            if (window.isRefreshing) {
                debugLog('Refresh prevented - already refreshing');
                return;
            }
            
            // Get active conversation
            const activeConversationItem = document.querySelector('.conversation-item.active');
            if (!activeConversationItem) return;
            
            // Set flag to prevent concurrent refreshes
            window.isRefreshing = true;
            debugLog('Starting refresh');
            
            const conversationId = activeConversationItem.dataset.conversationId;
            const messagesContainer = document.getElementById('messagesContainer');
            if (!messagesContainer) {
                window.isRefreshing = false;
                return;
            }
            
            // Save scroll state BEFORE any DOM changes
            const wasAtBottom = isAtBottom(messagesContainer);
            
            // Keep track of a message in the middle of the visible area for better anchoring
            let anchorMessage = null;
            let anchorOffsetRatio = 0;
            
            if (!wasAtBottom) {
                // Find a message to use as anchor point
                const visibleMessages = Array.from(messagesContainer.querySelectorAll('.message[data-message-id]'))
                    .filter(msg => {
                        const rect = msg.getBoundingClientRect();
                        const containerRect = messagesContainer.getBoundingClientRect();
                        return (rect.top >= containerRect.top && rect.top <= containerRect.bottom) ||
                            (rect.bottom >= containerRect.top && rect.bottom <= containerRect.bottom);
                    });
                
                if (visibleMessages.length > 0) {
                    // Use the message closest to the middle of the viewport as anchor
                    const containerMiddle = messagesContainer.getBoundingClientRect().top + messagesContainer.clientHeight / 2;
                    let closestDistance = Infinity;
                    
                    visibleMessages.forEach(msg => {
                        const msgMiddle = msg.getBoundingClientRect().top + msg.offsetHeight / 2;
                        const distance = Math.abs(msgMiddle - containerMiddle);
                        
                        if (distance < closestDistance) {
                            closestDistance = distance;
                            anchorMessage = msg;
                            
                            // Calculate ratio of message's position in viewport (0 = top, 1 = bottom)
                            const msgTop = msg.getBoundingClientRect().top - messagesContainer.getBoundingClientRect().top;
                            anchorOffsetRatio = msgTop / messagesContainer.clientHeight;
                        }
                    });
                }
            }
            
            // Save form state
            const textareaContent = textarea?.value || '';
            const fileInput = document.getElementById('fileUpload');
            const savedFiles = fileInput?.files || null;
            const filePreviewHTML = filePreviewContainer?.innerHTML || '';
            
            // Fetch updated content
            fetch(getRouteUrl(route_prefix + '.get-conversation') + '?id=' + conversationId, {
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
                    const contentDiv = document.getElementById('conversationContent');
                    if (!contentDiv) {
                        window.isRefreshing = false;
                        return;
                    }
                    
                    // Extract and compare just the messages part
                    const currentContent = contentDiv.innerHTML;
                    const currentMessagesHTML = extractMessagesHTML(currentContent);
                    const newMessagesHTML = extractMessagesHTML(data.html);
                    
                    // Only update if content has changed
                    if (newMessagesHTML !== currentMessagesHTML) {
                        // Create a hidden clone to preload images without affecting the visible DOM
                        const tempContainer = document.createElement('div');
                        tempContainer.style.position = 'absolute';
                        tempContainer.style.left = '-9999px';
                        tempContainer.style.visibility = 'hidden';
                        tempContainer.innerHTML = data.html;
                        document.body.appendChild(tempContainer);
                        
                        // Store anchorMessage ID before DOM update
                        const anchorMessageId = anchorMessage ? anchorMessage.dataset.messageId : null;
                        
                        // Preload all images in the temp container
                        const imagesToPreload = tempContainer.querySelectorAll('img');
                        let loadedImages = 0;
                        const totalImages = imagesToPreload.length;
                        
                        const preloadComplete = function() {
                            debugLog('Preload complete, updating DOM');
                            
                            // Replace content with the preloaded version
                            contentDiv.innerHTML = tempContainer.innerHTML;
                            document.body.removeChild(tempContainer);
                            
                            // Restore form state
                            if (textarea && textareaContent) {
                                textarea.value = textareaContent;
                                textarea.style.height = 'auto';
                                textarea.style.height = (textarea.scrollHeight) + 'px';
                            }
                            
                            // Restore file input
                            if (savedFiles && savedFiles.length > 0) {
                                const fileInput = document.getElementById('fileUpload');
                                if (fileInput) {
                                    Object.defineProperty(fileInput, 'files', {
                                        value: savedFiles,
                                        writable: true
                                    });
                                }
                            }
                            
                            // Restore preview container
                            const filePreviewContainer = document.getElementById('filePreviewContainer');
                            if (filePreviewContainer && filePreviewHTML) {
                                filePreviewContainer.innerHTML = filePreviewHTML;
                                
                                // Reattach event listeners
                                filePreviewContainer.querySelectorAll('.remove-file').forEach(button => {
                                    button.addEventListener('click', function() {
                                        button.closest('.file-preview').remove();
                                    });
                                });
                            }
                            
                            // Reset message form initialization
                            window.messageFormInitialized = false;
                            initializeMessageForm(conversationId, true);
                            
                            // Reset scroll position AFTER DOM update
                            const newMessagesContainer = document.getElementById('messagesContainer');
                            if (newMessagesContainer) {
                                if (wasAtBottom) {
                                    // If user was at bottom, scroll to bottom
                                    newMessagesContainer.scrollTop = newMessagesContainer.scrollHeight;
                                    debugLog('Restoring scroll: to bottom');
                                } else if (anchorMessageId) {
                                    // Find the anchor message in the new DOM
                                    const newAnchorMessage = newMessagesContainer.querySelector(`.message[data-message-id="${anchorMessageId}"]`);
                                    if (newAnchorMessage) {
                                        // Calculate the new position to maintain the same relative view
                                        const newScrollTop = newAnchorMessage.offsetTop - (anchorOffsetRatio * newMessagesContainer.clientHeight);
                                        newMessagesContainer.scrollTop = newScrollTop;
                                        debugLog('Restoring scroll: to anchor message');
                                    }
                                }
                            }
                            
                            markConversationAsRead(conversationId);
                            updateNavbarUnreadCount();
                            window.isRefreshing = false;
                        };
                        
                        // If no images, update immediately
                        if (totalImages === 0) {
                            preloadComplete();
                        } else {
                            // Preload images with timeout
                            const imageTimeout = setTimeout(() => {
                                if (window.isRefreshing) {
                                    debugLog('Image preload timed out');
                                    preloadComplete();
                                }
                            }, 2000);
                            
                            // Preload each image
                            imagesToPreload.forEach(img => {
                                // Show images in the temp container
                                if (img.style.display === 'none') {
                                    img.style.display = 'block';
                                }
                                
                                const tempImg = new Image();
                                tempImg.onload = tempImg.onerror = function() {
                                    loadedImages++;
                                    if (loadedImages >= totalImages && window.isRefreshing) {
                                        clearTimeout(imageTimeout);
                                        preloadComplete();
                                    }
                                };
                                tempImg.src = img.src;
                            });
                        }
                    } else {
                        // No changes in content
                        debugLog('No changes in content, skipping update');
                        window.isRefreshing = false;
                    }
                } else {
                    window.isRefreshing = false;
                }
            })
            .catch(error => {
                console.error('Error refreshing conversation:', error);
                window.isRefreshing = false;
            });
        };

        // Add these helper functions
        function isAtBottom(container) {
            return container.scrollHeight - container.scrollTop <= container.clientHeight + 150;
        }

        function getVisibleMessages(container) {
            const messages = container.querySelectorAll('.message');
            const result = [];
            
            // Get container bounds
            const containerTop = container.scrollTop;
            const containerBottom = containerTop + container.clientHeight;
            
            // Find messages that are fully or partially visible
            messages.forEach(message => {
                const rect = message.getBoundingClientRect();
                const containerRect = container.getBoundingClientRect();
                const messageTop = rect.top - containerRect.top + container.scrollTop;
                const messageBottom = messageTop + rect.height;
                
                // If message is visible
                if ((messageTop >= containerTop && messageTop <= containerBottom) ||
                    (messageBottom >= containerTop && messageBottom <= containerBottom)) {
                    
                    // Store message ID and its position relative to viewport
                    result.push({
                        id: message.dataset.messageId,
                        position: (messageTop - containerTop) / container.clientHeight
                    });
                }
            });
            
            return result;
        }

        function restoreScrollToMessage(container, visibleMessages) {
            if (visibleMessages.length === 0) return;
            
            // Try to find the anchor message with the most centered position
            let bestMatch = null;
            let closestPosition = 1;
            
            visibleMessages.forEach(msg => {
                // Find the closest message to the center of the previous view
                const distance = Math.abs(msg.position - 0.5);
                if (distance < closestPosition) {
                    closestPosition = distance;
                    bestMatch = msg;
                }
            });
            
            if (!bestMatch) return;
            
            // Find the message element
            const messageElement = container.querySelector(`.message[data-message-id="${bestMatch.id}"]`);
            if (!messageElement) return;
            
            // Calculate where to scroll
            const rect = messageElement.getBoundingClientRect();
            const containerRect = container.getBoundingClientRect();
            const messageTop = rect.top - containerRect.top + container.scrollTop;
            
            // Set scroll position to align same message at same relative position
            const newScrollTop = messageTop - (bestMatch.position * container.clientHeight);
            container.scrollTop = newScrollTop;
        }

        function restoreFormState(textareaContent, savedFiles, filePreviewHTML) {
            // Restore textarea
            const textarea = document.getElementById('messageContent');
            if (textarea && textareaContent) {
                textarea.value = textareaContent;
                textarea.style.height = 'auto';
                textarea.style.height = (textarea.scrollHeight) + 'px';
            }
            
            // Restore file input
            if (savedFiles && savedFiles.length > 0) {
                const fileInput = document.getElementById('fileUpload');
                if (fileInput) {
                    Object.defineProperty(fileInput, 'files', {
                        value: savedFiles,
                        writable: true
                    });
                }
            }
            
            // Restore preview container
            const filePreviewContainer = document.getElementById('filePreviewContainer');
            if (filePreviewContainer && filePreviewHTML) {
                filePreviewContainer.innerHTML = filePreviewHTML;
                
                // Reattach event listeners
                filePreviewContainer.querySelectorAll('.remove-file').forEach(button => {
                    button.addEventListener('click', function() {
                        button.closest('.file-preview').remove();
                    });
                });
            }
        }

        // CRITICAL FIX: Preload all images to prevent layout shifts
        function preloadImages(container) {
            return new Promise(resolve => {
                const images = container.querySelectorAll('img[style="display: none;"]');
                if (images.length === 0) {
                    resolve();
                    return;
                }
                
                let loadedCount = 0;
                let resolvedAlready = false;
                
                // Set a timeout to resolve anyway after 2 seconds
                const timeout = setTimeout(() => {
                    if (!resolvedAlready) {
                        resolvedAlready = true;
                        resolve();
                    }
                }, 2000);
                
                // Track image loading
                images.forEach(img => {
                    // Create a temporary image to load in background
                    const tempImg = new Image();
                    tempImg.onload = function() {
                        loadedCount++;
                        
                        // When this image loads, update the actual image
                        const actualImg = document.getElementById(img.id);
                        if (actualImg) {
                            actualImg.style.display = 'block';
                            const loadingId = actualImg.id.replace('img-', 'loading-');
                            const loadingEl = document.getElementById(loadingId);
                            if (loadingEl) {
                                loadingEl.style.display = 'none';
                            }
                        }
                        
                        // If all images loaded, resolve
                        if (loadedCount === images.length && !resolvedAlready) {
                            clearTimeout(timeout);
                            resolvedAlready = true;
                            resolve();
                        }
                    };
                    
                    tempImg.onerror = function() {
                        loadedCount++;
                        if (loadedCount === images.length && !resolvedAlready) {
                            clearTimeout(timeout);
                            resolvedAlready = true;
                            resolve();
                        }
                    };
                    
                    // Start loading
                    if (img.src) {
                        tempImg.src = img.src;
                    } else {
                        loadedCount++;
                    }
                });
            });
        }



        // Add scroll event listener to detect user interaction
        document.addEventListener('DOMContentLoaded', function() {
            document.body.addEventListener('scroll', function(e) {
                if (e.target.id === 'messagesContainer') {
                    lastKnownScrollPosition = e.target.scrollTop;
                    if (scrollTimeoutId) {
                        clearTimeout(scrollTimeoutId);
                    }
                    scrollTimeoutId = setTimeout(() => {
                        scrollTimeoutId = null;
                    }, 1000);
                }
            }, true);
        });

        // ============= DOCUMENT READY INITIALIZATION =============
        document.addEventListener('DOMContentLoaded', function() {
            debugLog('DOM content loaded, initializing messaging...');
            
            // Set up minimized sidebar
            function setupMinimizedSidebar() {
                const sidebar = document.querySelector('.sidebar');
                if (sidebar && !sidebar.classList.contains('close')) {
                    sidebar.classList.add('close');
                }
            }
            
            // Mobile view setup - CRITICAL FIX
            function setupMobileView() {
                const toggleBtn = document.querySelector('.toggle-conversation-list');
                const conversationList = document.querySelector('.conversation-list');
                
                if (toggleBtn && conversationList) {
                    toggleBtn.addEventListener('click', function() {
                        conversationList.classList.toggle('hidden');
                    });
                    
                    // Initialize hidden state on mobile
                    if (window.innerWidth < 768) {
                        conversationList.classList.add('hidden');
                        toggleBtn.style.display = 'flex';
                    } else {
                        toggleBtn.style.display = 'none';
                    }
                    
                    // Update on resize
                    window.addEventListener('resize', function() {
                        if (window.innerWidth < 768) {
                            toggleBtn.style.display = 'flex';
                        } else {
                            toggleBtn.style.display = 'none';
                            conversationList.classList.remove('hidden');
                        }
                    });
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
                
                fetch(getRouteUrl(route_prefix + '.leave-group'), {
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
                        window.location.href = role_base_url + '/messaging';
                    } else {
                        alert(data.message || 'An error occurred while leaving the group.');
                    }
                })
                .catch(error => {
                    console.error('Error leaving group:', error);
                    alert('An error occurred while leaving the group.');
                });
            });
            
            // New conversation form handler
            document.getElementById('newConversationForm')?.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                fetch(getRouteUrl(route_prefix + '.create-conversation'), {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = role_base_url + '/messaging?conversation=' + data.conversation_id;
                    }
                })
                .catch(error => {
                    console.error('Error creating conversation:', error);
                });
            });
            
            // New group form handler
            document.getElementById('newGroupForm')?.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                fetch(getRouteUrl(route_prefix + '.create-group'), {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = role_base_url + '/messaging?conversation=' + data.conversation_id;
                    }
                })
                .catch(error => {
                    console.error('Error creating group:', error);
                });
            });
            
            // Setup conversation list search
            const searchInput = document.getElementById('conversationSearch');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();
                    const conversationItems = document.querySelectorAll('.conversation-item');
                    let found = false;
                    
                    conversationItems.forEach(item => {
                        const participantNames = item.dataset.participantNames?.toLowerCase() || '';
                        const groupParticipants = item.dataset.groupParticipants?.toLowerCase() || '';
                        const previewText = item.querySelector('.conversation-preview')?.textContent.toLowerCase() || '';
                        
                        if (participantNames.includes(searchTerm) || 
                            groupParticipants.includes(searchTerm) || 
                            previewText.includes(searchTerm)) {
                            item.style.display = '';
                            found = true;
                        } else {
                            item.style.display = 'none';
                        }
                    });
                    
                    // Show/hide no results message
                    const noResults = document.getElementById('noSearchResults');
                    if (noResults) {
                        if (!found && searchTerm.length > 0) {
                            noResults.style.display = 'block';
                        } else {
                            noResults.style.display = 'none';
                        }
                    }
                });
            }
            
            // Set up minimized sidebar
            setupMinimizedSidebar();
            
            // Set up mobile view
            setupMobileView();
            
            // Add click handlers to conversation items
            addConversationClickHandlers();
            
            // Initial update of unread count
            updateNavbarUnreadCount();
            
            // Handle selected conversation from dropdown if present
            const urlParams = new URLSearchParams(window.location.search);
            const selectedConversationId = urlParams.get('conversation');
            
            if (selectedConversationId) {
                const conversationItem = document.querySelector(`.conversation-item[data-conversation-id="${selectedConversationId}"]`);
                if (conversationItem) {
                    conversationItem.click();
                    
                    // Clean URL without reloading page
                    window.history.replaceState({}, document.title, window.location.pathname);
                }
            }
            
            // Track typing activity
            document.body.addEventListener('keydown', function() {
                window.lastTypingTime = Date.now();
            });
            
            // Set up automatic refresh
            setInterval(function() {
                window.refreshActiveConversation();
            }, 8000); // Every 8 seconds
        });

        // Add this CSS to fix the spinner animation
        document.addEventListener('DOMContentLoaded', function() {
            // Add animation styles if not already present
            if (!document.getElementById('spinner-animation-styles')) {
                const styleEl = document.createElement('style');
                styleEl.id = 'spinner-animation-styles';
                styleEl.textContent = `
                    @keyframes spinner-border {
                        to { transform: rotate(360deg); }
                    }
                    
                    .spinner-border {
                        display: inline-block;
                        width: 2rem;
                        height: 2rem;
                        vertical-align: text-bottom;
                        border: 0.25em solid currentColor;
                        border-right-color: transparent;
                        border-radius: 50%;
                        animation: spinner-border 0.75s linear infinite;
                    }
                    
                    .spinner-border-sm {
                        width: 1rem;
                        height: 1rem;
                        border-width: 0.2em;
                    }
                    
                    @keyframes pulse {
                        0% { opacity: 0.6; }
                        50% { opacity: 1; }
                        100% { opacity: 0.6; }
                    }
                    
                    .loading-pulse {
                        animation: pulse 1.5s infinite ease-in-out;
                    }

                    /* Add transition for smooth opacity changes */
                    .messages-container {
                        transition: opacity 0.05s ease;
                    }
                    
                    /* Class for hiding during refresh */
                    .updating-content {
                        opacity: 0;
                    }
                `;
                document.head.appendChild(styleEl);
            }
        });

        // Add this function after your message form initialization
        function appendNewMessage(message) {
            const messagesContainer = document.getElementById('messagesContainer');
            if (!messagesContainer) return;
            
            // Only append if container exists
            const isCurrentUserSender = message.sender_id == {{ auth()->id() }} && 
                                        message.sender_type == 'cose_staff';
            
            // Create a temporary wrapper to hold the HTML
            const temp = document.createElement('div');
            
            // Format the timestamp
            const timestamp = new Date(message.message_timestamp);
            const timeString = timestamp.toLocaleTimeString([], {hour: 'numeric', minute:'2-digit'});
            
            // Create the message HTML
            temp.innerHTML = `
                <div class="message ${isCurrentUserSender ? 'outgoing' : 'incoming'}" data-message-id="${message.message_id}">
                    <div class="message-content">
                        ${message.content || ''}
                        ${message.attachments && message.attachments.length > 0 ? 
                            '<div class="message-attachments"><small>Attachments will appear after refresh...</small></div>' : ''}
                    </div>
                    <div class="message-time">
                        <small>${timeString}</small>
                    </div>
                </div>
            `;
            
            // Append the new message
            messagesContainer.appendChild(temp.firstChild);
            
            // Scroll to bottom to show the new message
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        // Add this function to force a clean, non-flashing refresh
        function forceRefreshConversation(conversationId) {
            if (!conversationId) return;
            
            const activeConversationId = document.querySelector('.conversation-item.active')?.dataset.conversationId;
            if (activeConversationId !== conversationId) return;
            
            // Add a timestamp parameter to prevent caching
            const cacheBuster = '&_=' + new Date().getTime();
            fetch(getRouteUrl(route_prefix + '.get-conversation') + '?id=' + conversationId + cacheBuster, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Create an off-screen div to prepare the content
                    const tempDiv = document.createElement('div');
                    tempDiv.style.position = 'absolute';
                    tempDiv.style.left = '-9999px';
                    tempDiv.style.visibility = 'hidden';
                    tempDiv.innerHTML = data.html;
                    document.body.appendChild(tempDiv);
                    
                    // Get references to current containers
                    const currentMessagesContainer = document.getElementById('messagesContainer');
                    if (!currentMessagesContainer) {
                        document.body.removeChild(tempDiv);
                        return;
                    }
                    
                    // Keep scroll position at bottom
                    const wasAtBottom = isAtBottom(currentMessagesContainer);
                    
                    // Preload all images in the temp container
                    const images = tempDiv.querySelectorAll('img');
                    let loadedImages = 0;
                    const totalImages = images.length;
                                        
                    const updateContent = function() {
                        const newMessagesContainer = tempDiv.querySelector('.messages-container');
                        if (newMessagesContainer) {
                            // Instead of replacing the whole container, just update its content
                            const currentMessagesContainer = document.getElementById('messagesContainer');
                            if (currentMessagesContainer) {
                                // Keep scroll position at bottom
                                const wasAtBottom = isAtBottom(currentMessagesContainer);
                                
                                // Update content
                                currentMessagesContainer.innerHTML = newMessagesContainer.innerHTML;
                                
                                // Restore scroll position
                                if (wasAtBottom) {
                                    currentMessagesContainer.scrollTop = currentMessagesContainer.scrollHeight;
                                }
                            }
                        }
                        
                        // Clean up
                        document.body.removeChild(tempDiv);
                    };
                    
                    // If no images, update immediately
                    if (totalImages === 0) {
                        updateContent();
                    } else {
                        // Set a timeout to ensure we don't wait forever
                        const timeout = setTimeout(() => {
                            updateContent();
                        }, 2000);
                        
                        // Preload each image
                        images.forEach(img => {
                            const tempImg = new Image();
                            tempImg.onload = tempImg.onerror = function() {
                                loadedImages++;
                                if (loadedImages >= totalImages) {
                                    clearTimeout(timeout);
                                    updateContent();
                                }
                            };
                            tempImg.src = img.src;
                        });
                    }
                }
            })
            .catch(error => {
                console.error('Error refreshing conversation after sending:', error);
            });
        }
    </script>
</body>
</html>