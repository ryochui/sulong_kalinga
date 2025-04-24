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

    <script>
        // Define initializeMessageForm in the global scope
        window.initializeMessageForm = function() {
            
            const messageForm = document.getElementById('messageForm');
            if (!messageForm || window.messageFormInitialized) return;
            
            // Set flag to prevent duplicate initialization
            window.messageFormInitialized = true;
            console.log('Message form initialized globally');
            
            // Get references to form elements
            const textarea = document.getElementById('messageContent');
            const fileInput = document.getElementById('fileUpload');
            const filePreviewContainer = document.getElementById('filePreviewContainer');
            const attachmentBtn = document.getElementById('attachmentBtn');
            
            // Handle file attachment button click
            if (attachmentBtn && fileInput) {
                attachmentBtn.addEventListener('click', function() {
                    fileInput.click();
                });
            }
            
            // Show file previews when files are selected
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
            
            // Auto-resize textarea
            if (textarea) {
                textarea.addEventListener('input', function() {
                    this.style.height = 'auto';
                    this.style.height = (this.scrollHeight) + 'px';
                    
                    if (this.scrollHeight > 200) {
                        this.style.overflowY = 'auto';
                        this.style.height = '200px';
                    } else {
                        this.style.overflowY = 'hidden';
                    }
                });
            }
            
            // Handle form submission with AJAX
            messageForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Validation
                if (textarea.value.trim() === '' && (!fileInput.files || fileInput.files.length === 0)) {
                    alert('Please enter a message or attach a file');
                    return;
                }
                
                // Create FormData object
                const formData = new FormData(this);
                
                // Disable send button and show loading state
                const sendBtn = document.querySelector('.send-btn');
                const originalBtnContent = sendBtn.innerHTML;
                sendBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
                sendBtn.disabled = true;
                
                // Send the form using the form's action URL
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
                        // Record that we just sent a message
                        window.lastMessageSendTimestamp = Date.now();

                        // Get the message content directly from the textarea before clearing it
                        const messageContent = textarea.value.trim();

                        // Only clear form after successful submission
                        textarea.value = '';
                        fileInput.value = '';
                        if (filePreviewContainer) {
                            filePreviewContainer.innerHTML = '';
                        }
                        
                        // Use the improved addMessageToDisplay function with explicit content
                        window.addMessageToDisplay({
                            content: data.message ? data.message.content : null,
                            formContent: messageContent, // Pass the content directly
                            id: data.message_id,
                            time: new Date().toLocaleTimeString([], {hour: 'numeric', minute:'2-digit'})
                        }, true);
                        
                        // Update the conversation list without full refresh
                        if (window.updateConversationList) {
                            window.updateConversationList();
                        }
                    } else {
                        throw new Error(data.message || 'Failed to send message');
                    }
                })
                .catch(error => {
                    console.error('Error sending message:', error);
                    alert('Failed to send message: ' + error.message);
                })
                .finally(() => {
                    // Reset button state
                    sendBtn.innerHTML = originalBtnContent;
                    sendBtn.disabled = false;
                });
            });
        };
        
        // Also make messageFormInitialized global
        window.messageFormInitialized = false;

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
                                    
                                    // Update URL without page reload
                                    const newUrl = "{{ url('/" . $rolePrefix . "/messaging') }}?conversation=" + conversationId;
                                    window.history.pushState({ conversationId: conversationId }, '', newUrl);
                                    
                                    // Load conversation content
                                    loadConversation(conversationId);
                                });
                            });
                            
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

        window.addMessageToDisplay = function(messageData, isOutgoing = true) {
            const messagesContainer = document.getElementById('messagesContainer');
            if (!messagesContainer) {
                console.error('Message container not found!');
                return;
            }
            
            console.log('Adding message:', messageData); // Debug output
            
            // Create message element
            const messageEl = document.createElement('div');
            messageEl.className = isOutgoing ? 'message outgoing' : 'message incoming';
            
            // Add message content
            const contentEl = document.createElement('div');
            contentEl.className = 'message-content';
            
            // Make sure we have content, with fallbacks
            let messageContent = '';
            if (typeof messageData.content === 'string') {
                messageContent = messageData.content;
            } else if (messageData.formContent) {
                // Fallback to explicitly passed textarea value
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
    </script>

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
    
    <!-- Scripts -->
    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    
    <!-- Unified script for sidebar, tooltips and messaging functionality -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Force sidebar minimized while preserving tooltip functionality
            function setupMinimizedSidebar() {
                const sidebar = document.querySelector(".sidebar");
                if (!sidebar) return;
                
                // Ensure sidebar is in minimized state
                sidebar.classList.add('close');
                
                // Ensure the width is fixed
                sidebar.style.width = '78px';
                sidebar.style.minWidth = '78px';
                sidebar.style.maxWidth = '78px';
                
                // Disable the toggle buttons
                const toggles = sidebar.querySelectorAll('.bx-menu, .logo_name');
                toggles.forEach(toggle => {
                    // Clone to remove event listeners
                    const clone = toggle.cloneNode(true);
                    toggle.parentNode.replaceChild(clone, toggle);
                });
                
                // Hide the menu text but ensure tooltips work
                const navLinks = sidebar.querySelectorAll('.nav-links li');
                navLinks.forEach(item => {
                    // Fix width to prevent overflow
                    item.style.width = '78px';
                    item.style.overflow = 'hidden';
                    
                    // Make sure the link text is hidden but preserved for tooltips
                    const linkName = item.querySelector('.link_name');
                    if (linkName) {
                        linkName.style.opacity = '0';
                        linkName.style.pointerEvents = 'none';
                    }
                    
                    // Make sure the sub-menu (tooltip) is styled correctly
                    const subMenu = item.querySelector('.sub-menu');
                    if (subMenu) {
                        // Position the tooltip correctly
                        subMenu.style.position = 'absolute';
                        subMenu.style.left = '100%';
                        subMenu.style.top = '0';
                        subMenu.style.zIndex = '1000';
                        
                        // Make sure the tooltip text is visible
                        const tooltipLinkName = subMenu.querySelector('.link_name');
                        if (tooltipLinkName) {
                            tooltipLinkName.style.display = 'block';
                            tooltipLinkName.style.opacity = '1';
                            tooltipLinkName.style.pointerEvents = 'auto';
                        }
                    }
                });
            }
            
            // Setup Mobile View with Toggle Functionality
            function setupMobileView() {
                const conversationList = document.querySelector('.conversation-list');
                const messageArea = document.querySelector('.message-area');
                
                // Only add these elements on small screens
                if (window.innerWidth <= 768) {
                    // Create toggle button if it doesn't exist
                    if (!document.querySelector('.toggle-conversation-list')) {
                        const toggleButton = document.createElement('button');
                        toggleButton.className = 'toggle-conversation-list';
                        toggleButton.innerHTML = '<i class="bi bi-chat-left-text-fill"></i>';
                        document.body.appendChild(toggleButton);
                        
                        // Add click handler
                        toggleButton.addEventListener('click', function() {
                            conversationList.classList.toggle('hidden');
                        });
                    }
                    
                    // When a conversation is clicked, hide the list on mobile
                    document.querySelectorAll('.conversation-item').forEach(item => {
                        item.addEventListener('click', function() {
                            if (window.innerWidth <= 768) {
                                conversationList.classList.add('hidden');
                            }
                        });
                    });
                    
                    // Initially hide conversation list if a conversation is active
                    if (document.querySelector('.conversation-item.active')) {
                        conversationList.classList.add('hidden');
                    }
                } else {
                    // Remove toggle button on larger screens
                    const toggleButton = document.querySelector('.toggle-conversation-list');
                    if (toggleButton) {
                        toggleButton.remove();
                    }
                    
                    // Make sure conversation list is visible on larger screens
                    conversationList.classList.remove('hidden');
                }
            }
            
            // Run setup multiple times to ensure it applies
            setupMinimizedSidebar();
            setTimeout(setupMinimizedSidebar, 200);
            setTimeout(setupMinimizedSidebar, 500);
            
            // Initialize mobile view
            setupMobileView();
            
            // Update on window resize
            window.addEventListener('resize', function() {
                setupMinimizedSidebar();
                setupMobileView();
            });
            
            // 2. Set up AJAX for conversation loading
            document.querySelectorAll('.conversation-item').forEach(function(item) {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Remove active class from all items
                    document.querySelectorAll('.conversation-item').forEach(function(el) {
                        el.classList.remove('active');
                    });
                    
                    // Add active class to clicked item
                    this.classList.add('active');
                    
                    // Get conversation ID
                    const conversationId = this.getAttribute('data-conversation-id');
                    
                    // Update URL without page reload (for proper browser history)
                    const newUrl = "{{ url('/" . $rolePrefix . "/messaging') }}?conversation=" + conversationId;
                    window.history.pushState({ conversationId: conversationId }, '', newUrl);
                    
                    // Show loading state
                    document.getElementById('conversationContent').innerHTML = `
                        <div class="loading-container d-flex justify-content-center align-items-center h-100">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
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
                            
                            messageFormInitialized = false;
                            setTimeout(initializeMessageForm, 200);
                            
                            // Initialize tooltips
                            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                            tooltipTriggerList.forEach(function (tooltipTriggerEl) {
                                new bootstrap.Tooltip(tooltipTriggerEl);
                            });
                            
                            // Scroll to bottom of messages
                            const messagesContainer = document.getElementById('messagesContainer');
                            if (messagesContainer) {
                                messagesContainer.scrollTop = messagesContainer.scrollHeight;
                            }
                            
                            // Mark conversation as read
                            markConversationAsRead(conversationId);
                            
                            // Remove unread indicator from this conversation
                            const unreadBadge = this.querySelector('.unread-badge');
                            if (unreadBadge) {
                                unreadBadge.style.display = 'none';
                            }
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
                });
            });
            
            // 3. Initialize conversation search
            const searchInput = document.getElementById('conversationSearch');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();
                    const conversationItems = document.querySelectorAll('.conversation-item');
                    let foundResults = false;
                    
                    conversationItems.forEach(function(item) {
                        // Get conversation name
                        const conversationName = item.querySelector('.conversation-title span')?.textContent.toLowerCase() || '';
                        
                        // Get participant names from data attribute (we'll add this data attribute)
                        const participantNames = item.dataset.participantNames ? 
                            item.dataset.participantNames.toLowerCase() : '';
                        
                        // Get group participants from data attribute (we'll add this data attribute)
                        const groupParticipants = item.dataset.groupParticipants ? 
                            item.dataset.groupParticipants.toLowerCase() : '';
                        
                        // Check if the conversation matches the search
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
                    
                    // Show no results message if needed
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
            
            // 4. Auto-load conversation from URL parameter
            const urlParams = new URLSearchParams(window.location.search);
            const conversationId = urlParams.get('conversation');
            if (conversationId) {
                const conversationItem = document.querySelector(`.conversation-item[data-conversation-id="${conversationId}"]`);
                if (conversationItem) {
                    conversationItem.click();
                }
            }
            
            // 5. New conversation form handler
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
                        // Close modal
                        bootstrap.Modal.getInstance(document.getElementById('newConversationModal')).hide();
                        
                        // Refresh the conversation list
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
            
            // 6. New group form handler
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
                        // Close modal
                        bootstrap.Modal.getInstance(document.getElementById('newGroupModal')).hide();
                        
                        // Refresh the conversation list
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
            
            // 7. User type change handler
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
            
            // 8. User search functionality
            document.getElementById('userSearch').addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const participantSelect = document.getElementById('participantSelect');
                
                Array.from(participantSelect.options).forEach(option => {
                    const optionText = option.text.toLowerCase();
                    option.style.display = optionText.includes(searchTerm) ? '' : 'none';
                });
            });
            
           
        });
    </script>

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

    <script>
        // Handle leave group functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Store the current conversation ID when Leave Group button is clicked
            let currentLeaveGroupId = null;
            
            document.addEventListener('click', function(e) {
                if (e.target.closest('.leave-group-btn')) {
                    const btn = e.target.closest('.leave-group-btn');
                    currentLeaveGroupId = btn.dataset.conversationId;
                }
            });
            
            // When the Confirm button in the modal is clicked
            document.getElementById('confirmLeaveGroup')?.addEventListener('click', function() {
                if (!currentLeaveGroupId) return;
                
                // Send request to leave the group
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
                        // Close modal
                        bootstrap.Modal.getInstance(document.getElementById('leaveGroupModal')).hide();
                        
                        // Redirect to messaging index page
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
        });
    </script>

    <script>
    // Direct fix for scrolling issues
    document.addEventListener('DOMContentLoaded', function() {
        // Function to handle scrolling in messages container
        function fixMessageScroll() {
            const messagesContainer = document.getElementById('messagesContainer');
            if (!messagesContainer) return;
            
            // Force scroll to bottom
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
            
            // Ensure all images are loaded before scrolling
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
        
        // Run initially
        fixMessageScroll();
        
        // Run when conversation content is updated
        const conversationContent = document.getElementById('conversationContent');
        if (conversationContent) {
            // Use MutationObserver to detect when messages are added
            const observer = new MutationObserver(function() {
                fixMessageScroll();
            });
            
            observer.observe(conversationContent, {
                childList: true,
                subtree: true
            });
        }
        
        // Fix scrolling when a conversation is clicked
        document.querySelectorAll('.conversation-item').forEach(item => {
            item.addEventListener('click', function() {
                // Use setTimeout to run after the conversation is loaded
                setTimeout(fixMessageScroll, 1000);
            });
        });
    });

    // Auto-select the conversation from URL parameter
    document.addEventListener('DOMContentLoaded', function() {
        // Get conversation ID from URL
        const urlParams = new URLSearchParams(window.location.search);
        const conversationId = urlParams.get('conversation');
        
        // If we have a conversation ID, select it
        if (conversationId) {
            // Find the conversation item
            const conversationItem = document.querySelector(`.conversation-item[data-conversation-id="${conversationId}"]`);
            if (conversationItem) {
                // Trigger a click on it
                conversationItem.click();
                // Update browser history to remove the query parameter (optional)
                window.history.replaceState({}, document.title, '{{ route("admin.messaging.index") }}');
            }
        }
    });



    // Auto-refresh functionality for active conversations
    document.addEventListener('DOMContentLoaded', function() {
        let lastMessageId = 0; // Track the last message ID to detect new messages
        let isRefreshing = false; // Prevent multiple simultaneous refreshes
        
        window.lastRefreshTimestamp = 0;
        window.lastMessageSendTimestamp = 0;

        // Function to refresh the active conversation
        window.refreshActiveConversation = function() {
            // Check if there's an active conversation
            const activeConversationItem = document.querySelector('.conversation-item.active');
            if (!activeConversationItem || window.isRefreshing) return;
            
            // Don't refresh if we just sent a message in the last 2 seconds
            const now = Date.now();
            if (now - window.lastMessageSendTimestamp < 2000) {
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
                        
                        // Only update if content has changed
                        if (data.html !== currentContent) {
                            contentDiv.innerHTML = data.html;
                            
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

        // Make sure isRefreshing is also global
        window.isRefreshing = false;
        
        // Mark conversation as read
        function markConversationAsRead(conversationId) {
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
                    // Success - update UI if needed
                    const unreadBadge = document.querySelector(`.conversation-item[data-conversation-id="${conversationId}"] .unread-badge`);
                    if (unreadBadge) {
                        unreadBadge.remove();
                    }
                    
                    // Remove unread class from conversation item
                    const conversationItem = document.querySelector(`.conversation-item[data-conversation-id="${conversationId}"]`);
                    if (conversationItem) {
                        conversationItem.classList.remove('unread');
                    }
                }
            })
            .catch(error => {
                console.error('Error marking conversation as read:', error);
            });
        }
        
        // Set up polling for new messages every 20 seconds
        const refreshInterval = setInterval(refreshActiveConversation, 20000);

        // Also refresh when a conversation is clicked
        document.addEventListener('click', function(e) {
            const conversationItem = e.target.closest('.conversation-item');
            if (conversationItem) {
                // Short delay to let the conversation load first
                setTimeout(refreshActiveConversation, 1000);
            }
        });
        
        // Clean up the interval when leaving the page
        window.addEventListener('beforeunload', function() {
            clearInterval(refreshInterval);
        });
    });
    </script>

    <script>
        // Function to update the unread message badge in the navbar
        function updateNavbarUnreadCount() {
            fetch('{{ route($rolePrefix.".messaging.unread-count") }}')
                .then(response => response.json())
                .then(data => {
                    // Find parent window's updateUnreadCount function and call it
                    if (window.updateUnreadCount) {
                        window.updateUnreadCount(data.count);
                    } else {
                        // If function not directly available, update using DOM
                        const messageCount = document.querySelector('.message-count');
                        if (messageCount) {
                            if (data.count > 0) {
                                messageCount.textContent = data.count;
                                messageCount.style.display = 'block';
                            } else {
                                messageCount.style.display = 'none';
                            }
                        }
                    }
                })
                .catch(error => console.error('Error updating message count:', error));
        }

        // Update the markConversationAsRead function to call updateNavbarUnreadCount
        function markConversationAsRead(conversationId) {
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
                    // Success - update UI if needed
                    const unreadBadge = document.querySelector(`.conversation-item[data-conversation-id="${conversationId}"] .unread-badge`);
                    if (unreadBadge) {
                        unreadBadge.remove();
                    }
                    
                    // Remove unread class from conversation item
                    const conversationItem = document.querySelector(`.conversation-item[data-conversation-id="${conversationId}"]`);
                    if (conversationItem) {
                        conversationItem.classList.remove('unread');
                    }
                    
                    // Update the navbar badge count
                    updateNavbarUnreadCount();
                }
            })
            .catch(error => {
                console.error('Error marking conversation as read:', error);
            });
        }

        // Call this whenever a conversation is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // When conversation items are clicked
            document.querySelectorAll('.conversation-item').forEach(item => {
                item.addEventListener('click', function() {
                    const conversationId = this.dataset.conversationId;
                    if (conversationId) {
                        // Use setTimeout to ensure the conversation loads first
                        setTimeout(() => markConversationAsRead(conversationId), 1000);
                    }
                });
            });
            
            // Also call this when the page loads with a conversation parameter
            const urlParams = new URLSearchParams(window.location.search);
            const conversationId = urlParams.get('conversation');
            if (conversationId) {
                // Short delay to let the conversation load
                setTimeout(() => markConversationAsRead(conversationId), 1500);
            }
        });

        // Make updateUnreadCount function globally available
        window.updateUnreadCount = function(count) {
            const messageCount = document.querySelector('.message-count');
            if (messageCount) {
                if (count > 0) {
                    messageCount.textContent = count;
                    messageCount.style.display = 'block';
                } else {
                    messageCount.style.display = 'none';
                }
            }
        };
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Single source of truth for message form handling
            let messageFormInitialized = false;
            
            // Set up MutationObserver to detect when conversation content changes
            const conversationContent = document.getElementById('conversationContent');
            if (conversationContent) {
                const observer = new MutationObserver(function(mutations) {
                    // Reset flag when conversation changes
                    messageFormInitialized = false;
                    
                    // Re-initialize the form with the new content
                    setTimeout(initializeMessageForm, 200);
                });
                
                observer.observe(conversationContent, {
                    childList: true
                });
            }
            
            // Run the initial setup
            initializeMessageForm();
            
            // Also re-initialize when conversations are clicked
            document.querySelectorAll('.conversation-item').forEach(item => {
                item.addEventListener('click', function() {
                    // Reset the flag so we can re-initialize with new conversation
                    messageFormInitialized = false;
                    setTimeout(initializeMessageForm, 500);
                });
            });
        });
    </script>

</body>
</html>