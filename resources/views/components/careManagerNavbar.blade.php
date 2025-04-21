<link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
<link rel="stylesheet" href="{{ asset('css/userNavbar.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

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
                <li class="nav-item">
                    <a class="nav-link {{ Request::routeIs('messages') ? 'active' : '' }}" href="#">Messages</a>
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
                    <a class="nav-link dropdown-toggle {{ Request::routeIs('account') ? 'active' : '' }}" href="#" id="highlightsDropdown" role="button" data-bs-toggle="dropdown">
                        Account
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item {{ Request::routeIs('account') ? 'active' : '' }}" href="#">Account Profile</a>
                        </li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item">Logout</button>
                            </form>
                        </li>
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
    // DOM elements we need to reference frequently
    const dropdownList = document.querySelector('.dropdown-notifications .notification-list');
    const modalList = document.querySelector('#notificationsModal .notification-list');
    const countBadge = document.querySelector('.notification-count');
    
    // Keep track of unread count in a variable instead of counting DOM elements
    let currentUnreadCount = 0;
    
    // Dropdown configuration to prevent closing when clicking inside
    const dropdown = document.querySelector('.dropdown-menu.dropdown-notifications');
    dropdown.addEventListener('click', function(e) {
        e.stopPropagation();
    });
    
    // Add fetch notifications functionality
    loadNotifications();
    
    // Set up periodic refresh every 60 seconds
    setInterval(loadNotifications, 60000);
    
    // Load notifications from the server
    function loadNotifications() {
        console.log('Fetching notifications from server...');
        fetch('{{ url("care-manager/notifications") }}')
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Data received:', data);
                if (data.success) {
                    renderNotifications(data.notifications);
                    
                    // Set the unread count from server response
                    currentUnreadCount = data.unread_count;
                    updateUnreadCount(currentUnreadCount);
                } else {
                    console.error('API returned error:', data.message || 'Unknown error');
                }
            })
            .catch(error => console.error('Error loading notifications:', error));
    }
    
    // Render all notifications to both dropdown and modal
    function renderNotifications(notifications) {
        // Clear existing notifications
        dropdownList.innerHTML = '';
        modalList.innerHTML = '';
        
        if (!notifications || notifications.length === 0) {
            const emptyMessage = '<div class="p-3 text-center text-muted">No notifications</div>';
            dropdownList.innerHTML = emptyMessage;
            modalList.innerHTML = emptyMessage;
            return;
        }
        
        console.log('Rendering notifications:', notifications.length);
        
        // Add notifications to both dropdown and modal
        notifications.forEach(notification => {
            const iconInfo = getNotificationIcon(notification);
            const timeAgo = formatTimeAgo(new Date(notification.date_created));
            
            // Create and append dropdown item (truncated)
            dropdownList.innerHTML += createNotificationHTML(
                notification, 
                iconInfo.icon, 
                iconInfo.type, 
                timeAgo, 
                true // truncate for dropdown
            );
            
            // Create and append modal item (full text)
            modalList.innerHTML += createNotificationHTML(
                notification, 
                iconInfo.icon, 
                iconInfo.type, 
                timeAgo, 
                false // don't truncate for modal
            );
        });
        
        // Attach click handlers to buttons after rendering
        addButtonClickHandlers();
    }
    
    // Create HTML string for a notification item
    function createNotificationHTML(notification, iconClass, iconType, timeAgo, truncate) {
        return `
            <div class="notification-item ${notification.is_read ? '' : 'unread'}" data-id="${notification.notification_id}">
                <div class="d-flex align-items-start">
                    <div class="notification-icon ${iconType}">
                        <i class="bi ${iconClass}"></i>
                    </div>
                    <div class="notification-content">
                        <strong>${notification.message_title || 'Notification'}</strong>
                        <p class="mb-1 ${truncate ? 'text-truncate' : ''}">${notification.message}</p>
                        <span class="notification-time">${timeAgo}</span>
                    </div>
                </div>
                ${!notification.is_read ? 
                    `<div class="notification-actions">
                        <button class="btn btn-sm btn-link mark-as-read" data-id="${notification.notification_id}">Mark as read</button>
                    </div>` 
                    : ''}
            </div>
        `;
    }
    
    // Add click handlers to all mark-as-read buttons
    function addButtonClickHandlers() {
        // Individual mark as read buttons
        document.querySelectorAll('.mark-as-read').forEach(button => {
            button.onclick = function(e) {
                e.preventDefault();
                e.stopPropagation();
                const notificationId = this.getAttribute('data-id');
                markAsRead(notificationId);
            };
        });
        
        // Mark all as read buttons
        document.querySelector('.mark-all-read').onclick = function(e) {
            e.preventDefault();
            e.stopPropagation();
            markAllAsRead();
        };
        
        const markAllReadModalBtn = document.querySelector('.mark-all-read-modal');
        if (markAllReadModalBtn) {
            markAllReadModalBtn.onclick = function(e) {
                e.preventDefault();
                markAllAsRead();
            };
        }
        
        // Make notification items clickable to mark as read
        document.querySelectorAll('.notification-item.unread').forEach(item => {
            item.onclick = function(e) {
                if (!e.target.closest('.mark-as-read')) {
                    const notificationId = this.getAttribute('data-id');
                    markAsRead(notificationId);
                }
            };
        });
    }
    
    // Mark a single notification as read
    function markAsRead(notificationId) {
        if (!notificationId) return;
        
        console.log('Marking notification as read:', notificationId);
        
        // Disable the mark-as-read button to prevent double-clicks
        const markAsReadButtons = document.querySelectorAll(`.mark-as-read[data-id="${notificationId}"]`);
        markAsReadButtons.forEach(btn => {
            btn.disabled = true;
            btn.textContent = 'Updating...';
        });
        
        // Then update on server first
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch(`{{ url('care-manager/notifications') }}/${notificationId}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('Server response:', data);
            if (data.success) {
                // Only update the UI after server confirms success
                const items = document.querySelectorAll(`.notification-item[data-id="${notificationId}"]`);
                
                items.forEach(item => {
                    // Only process if it's unread
                    if (item.classList.contains('unread')) {
                        // Remove unread class
                        item.classList.remove('unread');
                        
                        // Remove the mark-as-read button
                        const actionDiv = item.querySelector('.notification-actions');
                        if (actionDiv) {
                            actionDiv.remove();
                        }
                    }
                });
                
                // Decrement the unread count by 1 and update badge
                currentUnreadCount = Math.max(0, currentUnreadCount - 1);
                updateUnreadCount(currentUnreadCount);
            } else {
                console.error('Failed to mark notification as read');
                // Re-enable buttons but don't reload all notifications
                markAsReadButtons.forEach(btn => {
                    btn.disabled = false;
                    btn.textContent = 'Mark as read';
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Re-enable buttons
            markAsReadButtons.forEach(btn => {
                btn.disabled = false;
                btn.textContent = 'Mark as read';
            });
        });
    }
    
    // Mark all notifications as read
    function markAllAsRead() {
        console.log('Marking all notifications as read');
        
        // Disable the mark-all-read buttons
        const markAllReadButtons = document.querySelectorAll('.mark-all-read, .mark-all-read-modal');
        markAllReadButtons.forEach(btn => {
            btn.disabled = true;
            if (btn.tagName === 'BUTTON') {
                btn.textContent = 'Updating...';
            } else {
                btn.textContent = 'Updating...';
            }
        });
        
        // Then update on server first
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch('{{ route("care-manager.notifications.read-all") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('Server response:', data);
            if (data.success) {
                // Only update UI after server success
                document.querySelectorAll('.notification-item.unread').forEach(item => {
                    item.classList.remove('unread');
                    
                    // Remove mark as read buttons
                    const actionDiv = item.querySelector('.notification-actions');
                    if (actionDiv) {
                        actionDiv.remove();
                    }
                });
                
                // Update count to zero
                currentUnreadCount = 0;
                updateUnreadCount(0);
                
                // Re-enable buttons with original text
                markAllReadButtons.forEach(btn => {
                    btn.disabled = false;
                    if (btn.tagName === 'BUTTON') {
                        btn.textContent = 'Mark all as read';
                    } else {
                        btn.textContent = 'Mark all as read';
                    }
                });
            } else {
                console.error('Failed to mark all notifications as read');
                // Re-enable buttons
                markAllReadButtons.forEach(btn => {
                    btn.disabled = false;
                    if (btn.tagName === 'BUTTON') {
                        btn.textContent = 'Mark all as read';
                    } else {
                        btn.textContent = 'Mark all as read';
                    }
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Re-enable buttons
            markAllReadButtons.forEach(btn => {
                btn.disabled = false;
                if (btn.tagName === 'BUTTON') {
                    btn.textContent = 'Mark all as read';
                } else {
                    btn.textContent = 'Mark all as read';
                }
            });
        });
    }
    
    // Update the notification count badge
    function updateUnreadCount(count) {
        // If count is provided as a string, parse it
        if (typeof count === 'string') {
            count = parseInt(count, 10);
        }
        
        // Ensure count is never negative
        count = Math.max(0, count);
        
        // Update badge
        countBadge.textContent = count;
        countBadge.style.display = count > 0 ? 'inline-block' : 'none';
        
        console.log('Updated unread count:', count);
    }
    
    // Get appropriate icon for notification type
    function getNotificationIcon(notification) {
        let icon = 'bi-info-circle';
        let type = 'info';
        
        if (notification.message_title) {
            const title = notification.message_title.toLowerCase();
            
            // Location-specific icons
            if (title.includes('municipality') || title.includes('barangay')) {
                icon = 'bi-geo-alt-fill';
                type = 'primary';
            }
            // Other existing conditions
            else if (title.includes('warning') || title.includes('assign')) {
                icon = 'bi-exclamation-triangle';
                type = 'warning';
            } else if (title.includes('success') || title.includes('approved')) {
                icon = 'bi-check-circle';
                type = 'success';
            } else if (title.includes('error') || title.includes('cancel') || title.includes('denied')) {
                icon = 'bi-x-circle';
                type = 'danger';
            }
        }
        
        return { icon, type };
    }
    
    // Format time ago from date
    function formatTimeAgo(date) {
        const seconds = Math.floor((new Date() - date) / 1000);
        
        let interval = Math.floor(seconds / 31536000);
        if (interval > 1) return interval + ' years ago';
        if (interval === 1) return '1 year ago';
        
        interval = Math.floor(seconds / 2592000);
        if (interval > 1) return interval + ' months ago';
        if (interval === 1) return '1 month ago';
        
        interval = Math.floor(seconds / 86400);
        if (interval > 1) return interval + ' days ago';
        if (interval === 1) return '1 day ago';
        
        interval = Math.floor(seconds / 3600);
        if (interval > 1) return interval + ' hours ago';
        if (interval === 1) return '1 hour ago';
        
        interval = Math.floor(seconds / 60);
        if (interval > 1) return interval + ' minutes ago';
        if (interval === 1) return '1 minute ago';
        
        return 'just now';
    }
    
    // Reload notifications when modal is opened
    const notificationsModal = document.getElementById('notificationsModal');
    if (notificationsModal) {
        notificationsModal.addEventListener('show.bs.modal', loadNotifications);
    }
});
</script>