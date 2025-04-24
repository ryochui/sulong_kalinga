<link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
<link rel="stylesheet" href="{{ asset('css/userNavbar.css') }}">
<link rel="stylesheet" href="{{ asset('css/message-dropdown.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

@include('components.notificationScript')

<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand" href="/admin/dashboard" >
            <img src="{{ asset('images/cose-logo.png') }}" alt="System Logo" width="30" class="me-2">
            <span class="text-dark">SulongKalinga</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end bg-light" id="navbarNav">
            <ul class="navbar-nav">
                <!-- Messages Link with Dropdown -->
                <li class="nav-item dropdown me-2">
                    <a class="nav-link nav-message-link {{ Request::routeIs('admin.messaging.*') ? 'active' : '' }}" href="#" id="messagesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-chat-dots-fill"></i>
                        <span class="d-none d-md-inline">Messages</span>
                        <span class="badge bg-danger rounded-pill message-count" style="display: none;"></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end message-dropdown p-0" aria-labelledby="messagesDropdown">
                        <li class="dropdown-header">
                            <div class="d-flex justify-content-between align-items-center">
                            <h6 class="m-0">Messages</h6>
                                <a href="javascript:void(0)" class="mark-all-read text-decoration-none">
                                <small class="mark-all-read">Mark all as read</small>
                                </a>
                            </div>
                        </li>
                        
                        <!-- Messages will be loaded dynamically here -->
                        <div id="message-preview-container">
                            <li class="text-center py-3">
                                <div class="spinner-border spinner-border-sm text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </li>
                        </div>
                        
                        <li class="dropdown-footer">
                            <a href="{{ route('admin.messaging.index') }}" class="text-decoration-none text-primary">
                                See all messages
                            </a>
                        </li>
                    </ul>
                </li>
                
                <!-- Notifications (existing code) -->
                <li class="nav-item dropdown">
                    <a class="nav-link nav-notification-link position-relative" href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-bell-fill"></i>
                        <span>Notifications</span>
                        <span class="badge bg-danger rounded-pill notification-count" style="display: none;"></span>
                    </a>
                    <div class="dropdown-menu dropdown-notifications dropdown-menu-end p-0" aria-labelledby="notificationsDropdown">
                        <div class="dropdown-header d-flex justify-content-between align-items-center">
                            <h6 class="m-0">Notifications</h6>
                            <small class="mark-all-read">Mark all as read</small>
                        </div>
                        <div class="notification-list">
                            <!-- Admin Notifications -->
                        </div>
                        <div class="dropdown-footer text-center py-2">
                            <a href="#" class="text-primary view-all-notifications" data-bs-toggle="modal" data-bs-target="#notificationsModal">View all notifications</a>
                        </div>
                    </div>
                </li>
                
                <!-- Account Dropdown (existing code) -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ Request::routeIs('admin.account.profile.*') ? 'active' : '' }}" href="#" id="highlightsDropdown" role="button" data-bs-toggle="dropdown">
                        Account
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item {{ Request::routeIs('admin.account.profile.index') ? 'active' : '' }}" href="{{ route('admin.account.profile.index') }}">Account Profile</a>
                        </li>
                        <!-- Keep the existing language toggle -->
                        <li>
                            <div class="dropdown-item d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-translate me-2"></i>
                                    <label for="languageToggle" class="m-0" style="cursor: pointer;" onclick="event.stopPropagation();">
                                        <span>Tagalog</span>
                                    </label>
                                </div>
                                <div class="form-check form-switch ms-3">
                                    <input class="form-check-input" type="checkbox" id="languageToggle" style="cursor: pointer;">
                                </div>
                            </div>
                        </li>
                        <li>
                            <a class="dropdown-item {{ Request::routeIs('admin.account.profile.settings') ? 'active' : '' }}" href="{{ route('admin.account.profile.settings') }}">Settings</a>
                        </li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item">Logout</button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Notifications Modal (existing code) -->
<div class="modal fade notification-modal" id="notificationsModal" tabindex="-1" aria-labelledby="notificationsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notificationsModalLabel">All Notifications</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Showing all notifications</span>
                    <button class="btn btn-sm btn-outline-primary mark-all-read-modal">Mark all as read</button>
                </div>
                
                <div class="notification-list">
                    <!-- Notifications loaded dynamically -->
                </div>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Add JavaScript to load unread message count and recent messages -->
<script>

    // Helper function to update unread count display
    function updateUnreadCount(count) {
        const messageCount = document.querySelector('.message-count');
        if (messageCount) {
            if (count > 0) {
                messageCount.textContent = count;
                messageCount.style.display = 'block';
            } else {
                messageCount.style.display = 'none';
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Load unread message count
        function loadUnreadMessageCount() {
            fetch('{{ route("admin.messaging.unread-count") }}')
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    updateUnreadCount(data.count);
                })
                .catch(error => {
                    console.error('Error loading unread message count:', error);
                });
        }

        // Helper function to update unread count display
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

        // Initial load
        loadUnreadMessageCount();
    
        // Refresh count periodically regardless of dropdown state
        setInterval(loadUnreadMessageCount, 15000); // Every 15 seconds
        
        // Load recent messages for dropdown
        function loadRecentMessages() {
            const container = document.getElementById('message-preview-container');
            container.innerHTML = `
                <li class="text-center py-3">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </li>
            `;
            
            fetch('{{ route("admin.messaging.recent") }}')
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    container.innerHTML = '';
                    
                    if (!data.conversations || data.conversations.length === 0) {
                        container.innerHTML = `
                            <li class="empty-messages">
                                <span class="text-muted">No messages</span>
                            </li>
                        `;
                        return;
                    }
                    
                    // Add all conversations
                    data.conversations.forEach(conversation => {
                        const lastMessage = conversation.last_message;
                        if (!lastMessage) {
                            return;
                        }
                        
                        // Determine if this conversation has unread messages
                        const isUnread = conversation.has_unread === true;
                        
                        // Get first letter of name for the avatar placeholder
                        const nameInitial = conversation.name ? conversation.name.charAt(0) : '?';
                        
                        const previewHtml = `
                            <a href="{{ route('admin.messaging.index') }}?conversation=${conversation.conversation_id}" 
                            class="message-preview ${isUnread ? 'unread' : ''}">
                                <div class="d-flex align-items-start">
                                    <div class="message-icon">
                                        ${conversation.is_group_chat ? 
                                            `<span>${nameInitial}</span>` :
                                            `<span>${nameInitial}</span>`
                                        }
                                        <!-- Removed the badge from here -->
                                    </div>
                                    <div class="message-preview-content">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="sender-name">${conversation.name}</span>
                                            <div class="d-flex align-items-center">
                                                ${isUnread ? `<span class="message-badge">${conversation.unread_count || 1}</span>` : ''}
                                                <small class="message-time">${lastMessage.message_timestamp ? timeSince(new Date(lastMessage.message_timestamp)) : 'Just now'}</small>
                                            </div>
                                        </div>
                                        <p class="message-content">
                                            ${conversation.is_group_chat && lastMessage.sender_name ? lastMessage.sender_name + ': ' : ''}${lastMessage.content || 'No content'}
                                        </p>
                                    </div>
                                </div>
                            </a>
                        `;
                        
                        container.innerHTML += previewHtml;
                    });
                })
                .catch(error => {
                    console.error('Error loading recent messages:', error);
                    container.innerHTML = `
                        <li class="empty-messages">
                            <span class="text-muted">No messages</span>
                            <small class="d-block text-danger">Try again later</small>
                        </li>
                    `;
                });
        }

        // Fix mark all as read functionality
        const markAllReadButtons = document.querySelectorAll('.mark-all-read');
        markAllReadButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                
                e.preventDefault();
                e.stopPropagation();
                
                fetch('{{ route("admin.messaging.read-all") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    // Update UI to show all messages as read
                    document.querySelectorAll('.message-preview.unread').forEach(el => {
                        el.classList.remove('unread');
                    });
                    
                    // Remove all unread indicators and badges
                    document.querySelectorAll('.unread-indicator, .message-badge').forEach(el => {
                        el.remove();
                    });

                    // Update the selector to target only .message-badge
                    document.querySelectorAll('.message-badge').forEach(el => {
                        el.remove();
                    });

                    // Update message count
                    updateUnreadCount(0);
                    
                    // Show quick confirmation
                    const dropdown = document.querySelector('.message-dropdown');
                    const confirmation = document.createElement('div');
                    confirmation.className = 'text-center text-success py-2 read-confirmation';
                    confirmation.textContent = 'All messages marked as read';
                    dropdown.appendChild(confirmation);
                    
                    // Remove confirmation after 2 seconds
                    setTimeout(() => {
                        document.querySelectorAll('.read-confirmation').forEach(el => el.remove());
                    }, 2000);
                })
                .catch(error => {
                    console.error('Error marking messages as read:', error);
                });
            });
        });
        
        // Helper function to format time
        function timeSince(date) {
            const seconds = Math.floor((new Date() - date) / 1000);
            
            let interval = seconds / 31536000;
            if (interval > 1) return Math.floor(interval) + " years ago";
            
            interval = seconds / 2592000;
            if (interval > 1) return Math.floor(interval) + " months ago";
            
            interval = seconds / 86400;
            if (interval > 1) return Math.floor(interval) + " days ago";
            
            interval = seconds / 3600;
            if (interval > 1) return Math.floor(interval) + " hours ago";
            
            interval = seconds / 60;
            if (interval > 1) return Math.floor(interval) + " minutes ago";
            
            return "Just now";
        }
        
        // Try to load the counts safely
        try {
            loadUnreadMessageCount();
        } catch (e) {
            console.error('Could not load message count:', e);
        }
        
        // Load messages when dropdown is opened
        document.getElementById('messagesDropdown').addEventListener('click', function() {
            try {
                loadRecentMessages();
            } catch (e) {
                console.error('Could not load recent messages:', e);
            }
        });
        
        // Refresh counts and messages periodically
        setInterval(function() {
            try {
                loadUnreadMessageCount(); 
                
                // Also refresh messages if dropdown is open
                if (document.querySelector('.message-dropdown.show')) {
                    loadRecentMessages();
                }
            } catch (e) {
                console.error('Could not refresh message data:', e);
            }
        }, 30000); // Every 30 seconds
    });
</script>