<link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
<link rel="stylesheet" href="{{ asset('css/userNavbar.css') }}">
<link rel="stylesheet" href="{{ asset('css/message-dropdown.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

@include('components.notificationScript')

<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('landing') }}">
            <img src="{{ asset('images/cose-logo.png') }}" alt="System Logo" width="30" class="me-2">
            <span class="text-dark">SulongKalinga</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end bg-light" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item dropdown me-2">
                    <a class="nav-link position-relative {{ Request::routeIs('care-worker.messaging.*') ? 'active' : '' }}" href="#" id="messagesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-chat-dots-fill"></i>
                            <span class="ms-1">Messages</span>
                            <span class="badge bg-danger rounded-pill message-count ms-1" style="display: none;"></span>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end message-dropdown p-0" aria-labelledby="messagesDropdown">
                        <li class="dropdown-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>Messages</span>
                                <a href="{{ route('care-worker.messaging.index') }}" class="text-decoration-none">
                                    <small>View All</small>
                                </a>
                            </div>
                        </li>
                        <li><hr class="dropdown-divider m-0"></li>
                        
                        <!-- Messages will be loaded dynamically here -->
                        <div id="message-preview-container">
                            <li class="text-center py-3">
                                <div class="spinner-border spinner-border-sm text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </li>
                        </div>
                    </ul>
                </li>
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
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ Request::routeIs('care-worker.account.profile.*') ? 'active' : '' }}" href="#" id="highlightsDropdown" role="button" data-bs-toggle="dropdown">
                        Account
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item {{ Request::routeIs('care-worker.account.profile.index') ? 'active' : '' }}" href="{{ route('care-worker.account.profile.index') }}">Account Profile</a>
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
                            <a class="dropdown-item {{ Request::routeIs('care-worker.account.profile.settings') ? 'active' : '' }}" href="{{ route('care-worker.account.profile.settings') }}">Settings</a>
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

<!-- Notifications Modal -->
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load unread message count
    function loadUnreadMessageCount() {
        fetch('{{ route("care-worker.messaging.unread-count") }}') // Change from care-manager to care-worker
            .then(response => response.json())
            .then(data => {
                // ...rest of the code
            })
            .catch(error => console.error('Error loading unread message count:', error));
    }
    
    // Load recent messages for dropdown
    function loadRecentMessages() {
        fetch('{{ route("care-worker.messaging.recent") }}') // Change from care-manager to care-worker
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('message-preview-container');
                container.innerHTML = '';
                
                if (!data.conversations || data.conversations.length === 0) {
                    container.innerHTML = `
                        <li class="text-center py-3">
                            <span class="text-muted">No new messages</span>
                        </li>
                    `;
                    return;
                }
                
                data.conversations.forEach(conversation => {
                    const lastMessage = conversation.last_message;
                    const senderName = lastMessage && lastMessage.sender ? 
                        (lastMessage.sender.first_name + ' ' + lastMessage.sender.last_name) : 
                        'Unknown';
                    
                    const previewHtml = `
                        <li>
                            <a class="dropdown-item message-preview unread" href="{{ url('/${data.route_prefix}/messaging/conversation') }}/${conversation.conversation_id}">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        ${conversation.is_group_chat ?
                                            `<div class="rounded-circle profile-img-sm d-flex justify-content-center align-items-center bg-primary text-white">
                                                <span>${conversation.name ? conversation.name.charAt(0) : 'G'}</span>
                                            </div>` :
                                            `<img src="{{ asset('images/defaultProfile.png') }}" class="rounded-circle profile-img-sm" alt="User">`
                                        }
                                    </div>
                                    <div class="flex-grow-1 ms-2 overflow-hidden">
                                        <p class="mb-0 fw-bold">${conversation.is_group_chat ? conversation.name : senderName}</p>
                                        <p class="small text-truncate mb-0">${conversation.is_group_chat && lastMessage ? senderName + ': ' : ''}${lastMessage ? lastMessage.content : 'No messages'}</p>
                                        <p class="text-muted small mb-0">${lastMessage ? timeSince(new Date(lastMessage.message_timestamp)) : 'No date'}</p>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider m-0"></li>
                    `;
                    
                    container.innerHTML += previewHtml;
                });
            })
            .catch(error => {
                console.error('Error loading recent messages:', error);
                document.getElementById('message-preview-container').innerHTML = `
                    <li class="text-center py-3">
                        <span class="text-muted">No new messages</span>
                    </li>
                `;
            });
    }
    
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
    
    // Load on page load
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
    
    // Refresh counts periodically
    setInterval(function() {
        try {
            loadUnreadMessageCount(); 
        } catch (e) {
            console.error('Could not refresh message count:', e);
        }
    }, 60000); // Every minute
});
</script>