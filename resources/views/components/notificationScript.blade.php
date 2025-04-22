@php
    // Determine endpoints based on user role
    $userRole = Auth::user()->role_id;
    
    if ($userRole == 1) {
        // Admin
        $notificationsUrl = url('admin/notifications');
        $markAllReadUrl = route('admin.notifications.read-all');
        $roleName = 'admin';
    } elseif ($userRole == 2) {
        // Care Manager
        $notificationsUrl = url('care-manager/notifications');
        $markAllReadUrl = route('care-manager.notifications.read-all');
        $roleName = 'care-manager';
    } elseif ($userRole == 3) {
        // Care Worker
        $notificationsUrl = url('care-worker/notifications');
        $markAllReadUrl = route('care-worker.notifications.read-all');
        $roleName = 'care-worker';
    }
@endphp

<script>
document.addEventListener('DOMContentLoaded', function() {
    // DOM elements we need to reference frequently
    const dropdownList = document.querySelector('.dropdown-notifications .notification-list');
    const modalList = document.querySelector('#notificationsModal .notification-list');
    const countBadge = document.querySelector('.notification-count');
    const notificationsModal = document.getElementById('notificationsModal');
    
    // Track selected notification ID for modal focusing
    let selectedNotificationId = null;
    
    // Keep track of unread count in a variable instead of counting DOM elements
    let currentUnreadCount = 0;
    
    // Use the dynamic endpoints determined by the server
    const notificationsEndpoint = "{{ $notificationsUrl }}";
    const markAllReadEndpoint = "{{ $markAllReadUrl }}";
    console.log(`Using notifications endpoint for {{ $roleName }}: ${notificationsEndpoint}`);
    
    // Dropdown configuration to prevent closing when clicking inside
    const dropdown = document.querySelector('.dropdown-menu.dropdown-notifications');
    if (dropdown) {
        dropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
    
    // Add fetch notifications functionality
    loadNotifications();
    
    // Set up periodic refresh every 60 seconds
    setInterval(loadNotifications, 60000);
    
    // Load notifications from the server
    function loadNotifications() {
        console.log('Fetching notifications from server...');
        fetch(notificationsEndpoint)
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
                    
                    // After rendering, check if we need to focus a notification
                    if (selectedNotificationId && notificationsModal.classList.contains('show')) {
                        focusNotificationInModal(selectedNotificationId);
                    }
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
            <div class="notification-item ${notification.is_read ? '' : 'unread'}" 
                 data-id="${notification.notification_id}" 
                 id="notification-${notification.notification_id}">
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
        
        // Make notification items in dropdown clickable to open modal and focus on that notification
        document.querySelectorAll('.dropdown-notifications .notification-item').forEach(item => {
            item.onclick = function(e) {
                // Only handle if they didn't click the mark-as-read button
                if (!e.target.closest('.mark-as-read')) {
                    const notificationId = this.getAttribute('data-id');
                    
                    // If it's unread, mark it as read
                    if (this.classList.contains('unread')) {
                        markAsRead(notificationId);
                    }
                    
                    // Store the ID to focus on when modal opens
                    selectedNotificationId = notificationId;
                    
                    // Open the modal programmatically
                    const bsModal = new bootstrap.Modal(notificationsModal);
                    bsModal.show();
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
        
        fetch(`${notificationsEndpoint}/${notificationId}/read`, {
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
        
        fetch(markAllReadEndpoint, {
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
    
    // Focus and highlight a specific notification in the modal
    function focusNotificationInModal(notificationId) {
        // First, remove highlight from any previously highlighted items
        document.querySelectorAll('.notification-item.highlight').forEach(item => {
            item.classList.remove('highlight');
        });
        
        // Find the notification in the modal
        const notificationItem = modalList.querySelector(`#notification-${notificationId}`);
        
        if (notificationItem) {
            // Add highlight class
            notificationItem.classList.add('highlight');
            
            // Scroll the item into view with smooth animation
            notificationItem.scrollIntoView({ behavior: 'smooth', block: 'center' });
            
            // Optional: Flash effect to draw attention
            setTimeout(() => {
                notificationItem.style.transition = 'background-color 0.5s';
                notificationItem.style.backgroundColor = '#f8f9fa';
                
                setTimeout(() => {
                    notificationItem.style.backgroundColor = '';
                    
                    // Reset selected ID after focusing
                    setTimeout(() => {
                        selectedNotificationId = null;
                        
                        // Automatically remove highlight after 3 seconds
                        setTimeout(() => {
                            notificationItem.classList.remove('highlight');
                        }, 1500);
                        
                    }, 300);
                }, 300);
            }, 100);
        } else {
            console.log('Notification not found in modal:', notificationId);
        }
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
            // Admin notification icons
            else if (title.includes('administrator') || 
                    (title.includes('welcome') && !title.includes('care manager'))) {
                icon = 'bi-person-badge';
                type = 'info';
            }
            // Care Manager notification icons
            else if (title.includes('care manager')) {
                icon = 'bi-people';
                type = 'info';
            }
            // Welcome back notifications
            else if (title.includes('welcome back')) {
                icon = 'bi-door-open';
                type = 'success';
            }
            // Status change notification
            else if (title.includes('status')) {
                icon = 'bi-toggle-on';
                type = 'warning';  
            }
            // Profile update notification
            else if (title.includes('profile was updated')) {
                icon = 'bi-pencil-square';
                type = 'info';
            }
            // Other existing conditions
            else if (title.includes('warning') || title.includes('assign')) {
                icon = 'bi-exclamation-triangle';
                type = 'warning';
            } else if (title.includes('success') || title.includes('approved') || title.includes('welcome')) {
                icon = 'bi-check-circle';
                type = 'success';
            } else if (title.includes('error') || title.includes('cancel') || title.includes('denied') || title.includes('deactivated')) {
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
    
    // Configure modal events
    if (notificationsModal) {
        // When the modal is shown, focus on the selected notification if any
        notificationsModal.addEventListener('shown.bs.modal', function() {
            if (selectedNotificationId) {
                focusNotificationInModal(selectedNotificationId);
            }
        });
        
        // Reload notifications when modal is opened
        notificationsModal.addEventListener('show.bs.modal', loadNotifications);
    }
});
</script>