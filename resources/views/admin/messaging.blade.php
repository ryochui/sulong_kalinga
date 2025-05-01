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
                        <h5 class="modal-title" id="newConversationModalLabel">New Message</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="newConversationForm" action="{{ route('admin.messaging.create') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="recipientType" class="form-label">Recipient Type</label>
                                <select class="form-select" id="recipientType" name="recipient_type">
                                    <option value="cose_staff" selected>Staff Member</option>
                                </select>
                                <small class="form-text text-muted">Administrators can only message Care Managers and other Admins.</small>
                            </div>

                            <div id="existingConversationFeedback" class="mb-3 d-none"></div>
                            
                            <div class="mb-3">
                                <label for="recipientId" class="form-label">Select Recipient</label>
                                <select class="form-select" id="recipientId" name="recipient_id" required>
                                    <option value="">Loading recipients...</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="initialMessage" class="form-label">Message</label>
                                <textarea class="form-control" id="initialMessage" name="initial_message" rows="3" placeholder="Type your message here..."></textarea>
                            </div>
                            
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" id="startConversationBtn" class="btn btn-primary">Send Message</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- New Group Conversation Modal -->
        <div class="modal fade" id="newGroupModal" tabindex="-1" aria-labelledby="newGroupModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
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
                                <input type="text" class="form-control" id="groupName" name="name" required placeholder="Enter a name for this group">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Add Participants</label>
                                
                                <div class="participant-section mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="m-0">Staff Members</h6>
                                        <button type="button" class="btn btn-sm btn-outline-primary toggle-section" data-section="staff">
                                            <i class="bi bi-plus"></i> Add
                                        </button>
                                    </div>
                                    <div class="participant-list staff-list" style="display: none;">
                                        <div class="input-group mb-2">
                                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                                            <input type="text" class="form-control user-search" data-type="cose_staff" placeholder="Search staff...">
                                        </div>
                                        <div class="user-checkboxes cose_staff-users scrollable-checklist">
                                            <!-- Staff checkboxes will be added here -->
                                            <div class="text-center p-2">
                                                <div class="spinner-border spinner-border-sm text-primary" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            
                            </div>
                            
                            <div class="mb-3">
                                <label for="selectedParticipants" class="form-label">Selected Participants <span class="badge bg-primary" id="participant-count">0</span></label>
                                <div id="selectedParticipants" class="selected-participants p-2 border rounded">
                                    <div class="text-muted text-center no-participants">No participants selected</div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="groupInitialMessage" class="form-label">Initial Message (Optional)</label>
                                <textarea class="form-control" id="groupInitialMessage" name="initial_message" rows="3" placeholder="Write an initial message..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" id="createGroupBtn">Create Group</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <!-- Leave Group Confirmation Modal -->
    <div class="modal fade" id="leaveGroupModal" tabindex="-1" aria-labelledby="leaveGroupModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="leaveGroupModalLabel">Leave Group</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="leaveGroupMessage">Are you sure you want to leave this group?</p>
                    <div id="lastMemberWarning" class="alert alert-warning d-none">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <strong>Warning:</strong> You are the last member of this group. If you leave, the group and all its messages will be permanently deleted.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmLeaveBtn">Leave Group</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Group Members Modal -->
    <div class="modal fade" id="viewMembersModal" tabindex="-1" aria-labelledby="viewMembersModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewMembersModalLabel">Group Members</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="groupMembersList" class="list-group">
                        <!-- Members will be loaded dynamically here -->
                        <div class="text-center py-3">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Group Member Modal -->
    <div class="modal fade" id="addMemberModal" tabindex="-1" aria-labelledby="addMemberModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addMemberModalLabel">Add Group Member</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addMemberForm">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="conversation_id" id="addMemberConversationId">
                        
                        <div class="mb-3">
                            <label for="memberType" class="form-label">Member Type</label>
                            <select class="form-select" id="memberType" name="member_type">
                                <option value="cose_staff" selected>Staff Member</option>
                            </select>
                            <small class="form-text text-muted">Administrators can only message Care Managers and other Admins.</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="memberId" class="form-label">Select Member</label>
                            <select class="form-select" id="memberId" name="member_id" required>
                                <option value="">Loading members...</option>
                            </select>
                        </div>
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="addMemberBtn">Add to Group</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Confirm Unsend Modal -->
    <div class="modal fade" id="confirmUnsendModal" tabindex="-1" aria-labelledby="confirmUnsendModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="confirmUnsendModalLabel">Confirm Unsend Message</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to unsend this message? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmUnsendButton">Unsend Message</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Modal -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="errorModalLabel">Error</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="errorModalMessage"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    
    <script>
       // ============= MESSAGING SYSTEM - MAIN SCRIPT =============
        // Global variables and configuration
        const DEBUG = true;
        const ROLE_PREFIX = document.querySelector('meta[name="role-prefix"]')?.getAttribute('content') || 'admin';
        const ROUTE_PREFIX = ROLE_PREFIX + '.messaging';
        const BASE_URL = window.location.origin + '/' + ROLE_PREFIX + '/messaging';

        // Make search function accessible from window scope
        window.initializeSearchButton = function() {
            // Get all required elements
            const searchBtn = document.getElementById('messageSearchBtn');
            const messagesContainer = document.getElementById('messagesContainer');
            const searchContainer = document.getElementById('messageSearchContainer');
            
            if (!searchBtn) {
                console.error('Search button not found with ID: messageSearchBtn');
                return;
            }
            
            if (!searchContainer) {
                console.error('Search container not found with ID: messageSearchContainer');
                return;
            }

            // Check if container needs to be populated and populate if needed
            if (!createAndPopulateSearchContainer()) {
                return; // Exit if container can't be populated
            }
            
            /*if (!messagesContainer) {
                console.error('Messages container not found with ID: messagesContainer');
                return;
            }*/
            
            console.log('Search elements found, initializing search functionality');
            
            // Force initialize search style properties
            searchContainer.style.display = 'none';
            
            // Get references to search form elements
            const searchInput = document.getElementById('messageSearchInput');
            const prevButton = document.getElementById('searchPrevBtn');
            const nextButton = document.getElementById('searchNextBtn');
            const closeButton = document.getElementById('closeSearchBtn');
            const resultsInfo = document.getElementById('searchResultsCount');
            
            // Search button toggles the search container visibility - IMPORTANT CHANGE HERE
            searchBtn.onclick = function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Search button clicked - direct onclick handler');
                
                // Get the search container
                const searchContainer = document.getElementById('messageSearchContainer');
                if (!searchContainer) return false;
                
                // Check current visibility
                const isCurrentlyHidden = searchContainer.style.display === 'none' || 
                                        getComputedStyle(searchContainer).display === 'none';
                
                // Toggle visibility using both class and style
                if (isCurrentlyHidden) {
                    console.log('Showing search container');
                    searchContainer.classList.add('active');
                    searchContainer.style.display = 'block'; // Override the inline style
                    
                    // Also add search-active class to messages container to adjust spacing
                    const messagesContainer = document.getElementById('messagesContainer');
                    if (messagesContainer) {
                        messagesContainer.classList.add('search-active');
                    }
                    
                    // Focus the input field
                    const searchInput = document.getElementById('messageSearchInput');
                    if (searchInput) {
                        setTimeout(() => searchInput.focus(), 100);
                    }
                } else {
                    console.log('Hiding search container');
                    searchContainer.classList.remove('active');
                    searchContainer.style.display = 'none';
                    
                    // Remove search-active class from messages container
                    const messagesContainer = document.getElementById('messagesContainer');
                    if (messagesContainer) {
                        messagesContainer.classList.remove('search-active');
                    }
                }
                
                return false;
            };
            
            // Add input handler with debounce
            if (searchInput) {
                searchInput.addEventListener('input', debounce(function() {
                    const value = this.value.trim();
                    
                    // Clear previous search results
                    resetSearchHighlights();
                    
                    // Show minimum character warning
                    if (value.length > 0 && value.length < 2) {
                        prevButton.disabled = true;
                        nextButton.disabled = true;
                        resultsInfo.textContent = 'Enter at least 2 characters to search';
                        resultsInfo.style.color = '#dc3545'; // Red color for warning
                        return;
                    } else {
                        resultsInfo.style.color = ''; // Reset color
                    }
                    
                    if (value.length >= 2) {
                        searchInConversation(value, messagesContainer, resultsInfo);
                        
                        // Enable navigation buttons if matches found
                        prevButton.disabled = searchMatches.length === 0;
                        nextButton.disabled = searchMatches.length === 0;
                    } else {
                        resultsInfo.textContent = '';
                        prevButton.disabled = true;
                        nextButton.disabled = true;
                    }
                }, 300));
            }
            
            // Navigation buttons
            if (prevButton) {
                prevButton.addEventListener('click', function() {
                    if (searchMatches.length > 0) {
                        navigateSearchResults(false, messagesContainer, resultsInfo);
                    }
                });
            }
            
            if (nextButton) {
                nextButton.addEventListener('click', function() {
                    if (searchMatches.length > 0) {
                        navigateSearchResults(true, messagesContainer, resultsInfo);
                    }
                });
            }
            
            // Close button
            if (closeButton) {
                closeButton.addEventListener('click', function() {
                    searchContainer.classList.remove('active');
                    const messagesContainer = document.getElementById('messagesContainer');
                    if (messagesContainer) {
                        messagesContainer.classList.remove('search-active');
                    }
                    resetSearchHighlights();
                    if (searchInput) searchInput.value = '';
                    if (resultsInfo) resultsInfo.textContent = '';
                });
            }
            
            // Handle escape key
            searchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    if (searchMatches.length > 0) {
                        navigateSearchResults(true, messagesContainer, resultsInfo);
                    }
                } else if (e.key === 'Escape') {
                    searchContainer.style.display = 'none';
                    resetSearchHighlights();
                    searchInput.value = '';
                    resultsInfo.textContent = '';
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Add this particular debugging/fix attempt right after page loads
            setTimeout(function() {
                const searchBtn = document.getElementById('messageSearchBtn');
                if (searchBtn) {
                    console.log('Search button found on page load, adding backup handler');
                    searchBtn.addEventListener('click', function(e) {
                        console.log('Search button clicked via backup handler');
                        e.preventDefault();
                        const searchContainer = document.getElementById('messageSearchContainer');
                        if (searchContainer) {
                            if (searchContainer.style.display === 'block') {
                                searchContainer.style.display = 'none';
                            } else {
                                searchContainer.style.display = 'block';
                            }
                        } else {
                            console.error('Search container still not found!');
                        }
                    });
                } else {
                    console.error('Search button not found on page load!');
                }
            }, 1000);
        });

        // State tracking variables
        let currentLeaveGroupId = null;
        let isLastGroupMember = false;
        let selectedAdmins = [];
        let selectedCareWorkers = [];
        let lastAttachmentButtonId = null;
        let searchMatches = [];
        let currentMatchIndex = -1;
        let searchInitialized = false;

        // Timing variables to prevent conflicts
        window.intentionalClear = false;
        window.lastRefreshTimestamp = 0;
        window.lastMessageSendTimestamp = 0;
        window.messageFormInitialized = false;
        window.isRefreshing = false;
        window.preventRefreshUntil = 0;
        window.lastBlurContent = '';
        window.lastBlurTime = 0;
        window.lastTypingTime = 0;
        window.lastKnownScrollPosition = 0;
        window.scrollTimeoutId = null;

        // ============= UTILITY FUNCTIONS =============
        function debugLog(...args) {
            if (DEBUG) {
                console.log('[DEBUG]', ...args);
            }
        }

        function getRouteUrl(routeName) {
            // Extract the endpoint part after the last dot
            const endpoint = routeName.split('.').pop();
            
            // Base URL for messaging routes
            const baseUrl = `${window.location.origin}/${ROLE_PREFIX}/messaging`;
            
            // Map route names to actual endpoints as defined in Laravel routes
            switch(endpoint) {
                case 'mark-read':
                    return `${baseUrl}/mark-as-read`;
                case 'get-conversation-list':
                    return `${baseUrl}/get-conversations`;
                case 'send':
                    return `${baseUrl}/send-message`;
                case 'create':
                    return `${baseUrl}/create-conversation`;
                case 'create-group':
                    return `${baseUrl}/create-group`;
                case 'unread-count':
                    return `${baseUrl}/unread-count`;
                case 'get-users':
                    return `${baseUrl}/get-users`;
                case 'get-conversation':
                    return `${baseUrl}/get-conversation`;
                case 'leave-group':
                    return `${baseUrl}/leave-conversation`;
                case 'add-group-member':
                    return `${baseUrl}/add-group-member`;
                case 'unsend':
                    return `${baseUrl}/unsend-message`;
                case 'check-existing-conversation':
                    return `${baseUrl}/get-conversations-with-recipient`;
                default:
                    return `${baseUrl}/${endpoint}`;
            }
        }

        function isAtBottom(container) {
            const buffer = Math.min(150, container.clientHeight * 0.2); // 20% of container height or 150px
            return container.scrollHeight - container.scrollTop - container.clientHeight <= buffer;
        }

        function escapeRegExp(string) {
            return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }

        function debounce(func, delay) {
            let timeout;
            return function() {
                const context = this;
                const args = arguments;
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(context, args), delay);
            };
        }

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

        function setupLeaveGroupButton() {
            // Get leave group button in conversation header
            const leaveBtn = document.querySelector('.leave-group-btn');
            if (!leaveBtn) return;
            
            // Add click handler
            leaveBtn.addEventListener('click', function() {
                // Get conversation ID
                const conversationId = this.getAttribute('data-conversation-id');
                if (!conversationId) return;
                
                // Store for use in confirmation
                currentLeaveGroupId = conversationId;
                
                // Check if we're the last member
                const isLastMember = this.getAttribute('data-last-member') === 'true';
                const lastMemberWarning = document.getElementById('lastMemberWarning');
                
                if (lastMemberWarning) {
                    if (isLastMember) {
                        lastMemberWarning.classList.remove('d-none');
                        isLastGroupMember = true;
                    } else {
                        lastMemberWarning.classList.add('d-none');
                        isLastGroupMember = false;
                    }
                }
                
                // Show confirmation modal
                const modal = document.getElementById('leaveGroupModal');
                const bsModal = new bootstrap.Modal(modal);
                bsModal.show();
            });
            
            // Set up confirm button
            const confirmBtn = document.getElementById('confirmLeaveBtn');
            if (confirmBtn) {
                confirmBtn.addEventListener('click', function() {
                    // Hide modal
                    const modal = document.getElementById('leaveGroupModal');
                    const bsModal = bootstrap.Modal.getInstance(modal);
                    if (bsModal) bsModal.hide();
                    
                    if (!currentLeaveGroupId) return;
                    
                    // Show loading state
                    this.disabled = true;
                    this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Leaving...';
                    
                    // Send leave request
                    fetch(getRouteUrl(ROUTE_PREFIX + '.leave-group'), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            conversation_id: currentLeaveGroupId,
                            is_last_member: isLastGroupMember
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Refresh conversation list
                            smoothRefreshConversationList();
                            
                            // Reset conversation content
                            const conversationContent = document.getElementById('conversationContent');
                            if (conversationContent) {
                                conversationContent.innerHTML = `
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
                                `;
                            }
                        } else {
                            showErrorModal(data.error || 'Failed to leave group');
                        }
                        
                        // Reset button state
                        this.disabled = false;
                        this.innerHTML = 'Leave Group';
                    })
                    .catch(error => {
                        console.error('Error leaving group:', error);
                        showErrorModal('An error occurred while leaving the group');
                        
                        // Reset button state
                        this.disabled = false;
                        this.innerHTML = 'Leave Group';
                    });
                });
            }
        }

        function setupAddMemberButton() {
            // Get add member button
            const addMemberModal = document.getElementById('addMemberModal');
            if (!addMemberModal) return;
            
            // When modal is shown, set conversation ID
            addMemberModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const conversationId = button.getAttribute('data-conversation-id');
                
                if (conversationId) {
                    // Set conversation ID to hidden field
                    const idField = document.getElementById('addMemberConversationId');
                    if (idField) idField.value = conversationId;
                    
                    // Fetch members when member type changes
                    const memberTypeSelect = document.getElementById('memberType');
                    const memberIdSelect = document.getElementById('memberId');
                    
                    if (memberTypeSelect && memberIdSelect) {
                        // Reset selection
                        memberIdSelect.innerHTML = '<option value="">Loading members...</option>';
                        
                        // Add change handler if not already added
                        if (!memberTypeSelect.dataset.handlerAdded) {
                            memberTypeSelect.addEventListener('change', function() {
                                fetchPotentialMembers(this.value, memberIdSelect, conversationId);
                            });
                            memberTypeSelect.dataset.handlerAdded = 'true';
                        }
                        
                        // Initial fetch
                        fetchPotentialMembers(memberTypeSelect.value, memberIdSelect, conversationId);
                    }
                }
            });
            
            // Add submission handler for adding member
            const addMemberForm = document.getElementById('addMemberForm');
            if (addMemberForm) {
                addMemberForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const conversationId = document.getElementById('addMemberConversationId').value;
                    const memberId = document.getElementById('memberId').value;
                    
                    if (!conversationId || !memberId) {
                        showErrorModal('Please select a member to add');
                        return;
                    }
                    
                    // Show loading state
                    const submitButton = document.getElementById('confirmAddMemberBtn');
                    if (submitButton) {
                        submitButton.disabled = true;
                        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Adding...';
                    }
                    
                    // Send request to add member
                    fetch(getRouteUrl(ROUTE_PREFIX + '.add-group-member'), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            conversation_id: conversationId,
                            user_id: memberId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Hide modal
                            const modal = document.getElementById('addMemberModal');
                            const bsModal = bootstrap.Modal.getInstance(modal);
                            if (bsModal) {
                                bsModal.hide();
                            }
                            
                            // Refresh conversation
                            forceRefreshConversation(conversationId);
                        } else {
                            showErrorModal(data.message || 'Failed to add member');
                        }
                        
                        // Reset button state
                        if (submitButton) {
                            submitButton.disabled = false;
                            submitButton.innerHTML = 'Add Member';
                        }
                    })
                    .catch(error => {
                        console.error('Error adding member:', error);
                        showErrorModal('An error occurred while adding the member');
                        
                        // Reset button state
                        if (submitButton) {
                            submitButton.disabled = false;
                            submitButton.innerHTML = 'Add Member';
                        }
                    });
                });
            }
        }

        function fetchPotentialMembers(type, selectElement, conversationId) {
            if (!selectElement || !conversationId) return;
            
            // Show loading state
            selectElement.disabled = true;
            selectElement.innerHTML = '<option value="">Loading potential members...</option>';
            
            // Fetch potential members
            fetch(`/${ROLE_PREFIX}/messaging/get-users?type=${type}&exclude_conversation=${conversationId}`)
                .then(response => response.json())
                .then(data => {
                    // Enable the select
                    selectElement.disabled = false;
                    
                    // Check for users
                    if (data.users && data.users.length > 0) {
                        // Users found, populate the dropdown
                        selectElement.innerHTML = '<option value="" selected disabled>Select a member to add</option>';
                        
                        data.users.forEach(user => {
                            const option = document.createElement('option');
                            option.value = user.id;
                            option.textContent = user.name;
                            selectElement.appendChild(option);
                        });
                    } else {
                        // No users found
                        selectElement.innerHTML = '<option value="" selected disabled>No available members found</option>';
                    }
                })
                .catch(error => {
                    console.error('Error fetching potential members:', error);
                    selectElement.innerHTML = '<option value="" selected disabled>Error loading members</option>';
                    selectElement.disabled = false;
                });
        }

        // ============= CONVERSATION LIST AND LOADING =============
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

        function loadConversation(conversationId) {
            // Show loading state
            const conversationContent = document.getElementById('conversationContent');
            if (!conversationContent) return;
            
            conversationContent.innerHTML = `
                <div class="d-flex justify-content-center align-items-center h-100">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `;
            
            // Change the active conversation class
            const conversationItems = document.querySelectorAll('.conversation-item');
            conversationItems.forEach(item => {
                item.classList.remove('active');
                if (item.dataset.conversationId === conversationId) {
                    item.classList.add('active');
                }
            });
            
            // Add debug logging for request
            console.log('Fetching conversation:', conversationId);
            
            fetch(`/${ROLE_PREFIX}/messaging/get-conversation?id=${conversationId}`)
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Received conversation data successfully', data);
                    
                    // Validate data structure before proceeding
                    if (!data) {
                        throw new Error('Response data is empty or null');
                    }
                    
                    if (!data.html) {
                        throw new Error('Response missing required html property');
                    }
                    
                    // Update the conversation content
                    conversationContent.innerHTML = data.html;
                    
                    // Initialize message form
                    window.messageFormInitialized = false;
                    initializeMessageForm(conversationId);
                    
                    // Initialize search button and message actions after loading conversation
                    setTimeout(() => {
                        window.initializeSearchButton(); // Make sure we use window.initializeSearchButton
                        initializeMessageActions();
                        setupAttachmentButton(); // Ensure attachment button works too
                    }, 500);
                    
                    // Reset search UI after loading a new conversation
                    if (typeof window.resetSearchAfterConversationLoad === 'function') {
                        window.resetSearchAfterConversationLoad();
                    }
                    
                    // Scroll to bottom of messages
                    const messagesContainer = document.getElementById('messagesContainer');
                    if (messagesContainer) {
                        messagesContainer.scrollTop = messagesContainer.scrollHeight;
                    }
                    
                    // Mark conversation as read
                    markConversationAsRead(conversationId);
                })
                .catch(error => {
                    console.error('Failed to load conversation:', error.message || 'Unknown error');
                    conversationContent.innerHTML = `
                        <div class="alert alert-danger m-3">
                            <h4 class="alert-heading">Failed to load conversation</h4>
                            <p>${error.message || 'Unknown error occurred'}</p>
                        </div>
                    `;
                });
        }

        // ============= MESSAGE FORM HANDLING =============
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
                    
                    // Clear error message when typing starts
                    if (fileErrorContainer && !fileErrorContainer.classList.contains('d-none')) {
                        fileErrorContainer.classList.add('d-none');
                        fileErrorContainer.innerHTML = '';
                    }
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
            
            // File attachment handling - FIXED
            if (attachmentBtn && fileInput) {
                // Handle attachment button click
                console.log('Setting up attachment button click handler');
                
                // First, remove any existing listeners to avoid duplicates
                const newBtn = attachmentBtn.cloneNode(true);
                attachmentBtn.parentNode.replaceChild(newBtn, attachmentBtn);
                
                // Add fresh click event listener
                newBtn.addEventListener('click', function(e) {
                    console.log('Attachment button clicked');
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Clear errors when opening file picker
                    if (fileErrorContainer) {
                        fileErrorContainer.classList.add('d-none');
                        fileErrorContainer.textContent = '';
                    }
                    
                    // Force file input to be clickable
                    fileInput.disabled = false;
                    
                    // Trigger file dialog
                    console.log('Triggering file input click');
                    fileInput.click();
                    
                    return false;
                });
                
                // Add change event listener to file input
                fileInput.addEventListener('change', handleFileInputChange);
            }
            
            // Message form submission - FIXED DUPLICATE SUBMISSIONS
            messageForm.removeEventListener('submit', handleMessageSubmit); // Remove any existing listeners
            messageForm.addEventListener('submit', handleMessageSubmit);

            // Define the submission handler as a named function
            function handleMessageSubmit(e) {
                e.preventDefault();
                e.stopPropagation(); // Prevent event bubbling
                
                // Prevent double submission
                if (this.dataset.isSubmitting === 'true') {
                    console.log('Form already submitting, ignoring duplicate submission');
                    return false;
                }
                this.dataset.isSubmitting = 'true';
                
                // Clear any existing error messages
                const fileErrorContainer = document.getElementById('fileErrorContainer') || createErrorContainer();
                fileErrorContainer.classList.add('d-none');
                fileErrorContainer.innerHTML = '';
                
                // Get direct references to all required elements
                const textarea = document.getElementById('messageContent');
                const fileInput = document.getElementById('fileUpload');
                const filePreviewContainer = document.getElementById('filePreviewContainer');
                
                // Basic validation for empty messages
                const hasTextContent = textarea && textarea.value.trim() !== '';
                const hasFilePreview = filePreviewContainer && filePreviewContainer.querySelectorAll('.file-preview').length > 0;
                
                if (!hasTextContent && !hasFilePreview) {
                    console.log('Validation failed: Message is empty and no files attached');
                    fileErrorContainer.innerHTML = 'Please enter a message or attach a file.';
                    fileErrorContainer.classList.remove('d-none');
                    this.dataset.isSubmitting = 'false';
                    return false;
                }
                
                // Show "sending" state but without spinner
                const sendBtn = document.getElementById('sendMessageBtn');
                const originalBtnContent = sendBtn.innerHTML;
                sendBtn.disabled = true;
                
                // Create a fresh FormData object
                const formData = new FormData();
                
                // Add form fields manually
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                formData.append('conversation_id', document.querySelector('input[name="conversation_id"]').value);
                formData.append('content', textarea.value);
                
                // Add files directly from the input element
                if (fileInput && fileInput.files && fileInput.files.length > 0) {
                    console.log(`Found ${fileInput.files.length} files to upload`);
                    
                    for (let i = 0; i < fileInput.files.length; i++) {
                        formData.append('attachments[]', fileInput.files[i]);
                    }
                }
                
                // Send the message
                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
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
                            throw new Error('Server returned non-JSON response');
                        });
                    }
                })
                .then(data => {
                    console.log('Message sent successfully:', data);
                    
                    if (data.success) {
                        // Clear textarea content
                        const textarea = document.getElementById('messageContent');
                        
                        if (textarea) {
                            // Set flag to allow clearing
                            window.intentionalClear = true;
                            
                            // Clear content
                            textarea.value = '';
                            
                            // Reset height
                            textarea.style.height = '';
                            
                            // Remove stored draft
                            const conversationId = document.querySelector('input[name="conversation_id"]')?.value;
                            if (conversationId) {
                                localStorage.removeItem('messageContent_' + conversationId);
                            }
                            
                            // Reset flag
                            setTimeout(() => { window.intentionalClear = false; }, 100);
                        }
                        
                        // Clear file previews if they exist
                        if (filePreviewContainer) {
                            filePreviewContainer.innerHTML = '';
                        }
                        
                        // Clear file input by resetting value
                        if (fileInput) {
                            fileInput.value = '';
                        }
                        
                        // Reset the sending state
                        sendBtn.disabled = false;
                        sendBtn.innerHTML = originalBtnContent;
                        
                        // Record time to prevent immediate refresh
                        window.lastMessageSendTimestamp = Date.now();
                        
                        // IMMEDIATE REFRESH: First refresh conversation, then conversation list
                        const conversationId = document.querySelector('input[name="conversation_id"]')?.value;
                        if (conversationId) {
                            // Force refresh the conversation immediately
                            forceRefreshConversation(conversationId, true); // Added immediate flag
                        }
                        
                        // Also refresh conversation list after a delay 
                        setTimeout(() => {
                            smoothRefreshConversationList();
                        }, 500);
                    } else {
                        // Handle failure
                        console.error('Failed to send message:', data.error || 'Unknown error');
                        sendBtn.disabled = false;
                        sendBtn.innerHTML = originalBtnContent;
                        
                        fileErrorContainer.innerHTML = 'Failed to send message: ' + (data.error || 'Unknown error');
                        fileErrorContainer.classList.remove('d-none');
                    }
                    
                    // Reset submission flag
                    this.dataset.isSubmitting = 'false';
                })
                .catch(error => {
                    console.error('Error sending message:', error);
                    sendBtn.disabled = false;
                    sendBtn.innerHTML = originalBtnContent;
                    
                    fileErrorContainer.innerHTML = 'An error occurred while sending your message. Please try again.';
                    fileErrorContainer.classList.remove('d-none');
                    
                    // Reset submission flag
                    this.dataset.isSubmitting = 'false';
                });
            }
        }

        function handleFileInputChange(event) {
            const fileInput = event.target;
            const files = fileInput.files;
            
            if (files.length === 0) return;
            
            const filePreviewContainer = document.getElementById('filePreviewContainer');
            if (!filePreviewContainer) return;
            
            // Get or create error container
            const fileErrorContainer = document.getElementById('fileErrorContainer') || createErrorContainer();
            fileErrorContainer.classList.add('d-none');
            fileErrorContainer.innerHTML = '';
            
            // Check for maximum of 5 files
            const existingFiles = filePreviewContainer.querySelectorAll('.file-preview').length;
            const totalFilesAfterAdd = existingFiles + files.length;
            
            if (totalFilesAfterAdd > 5) {
                fileErrorContainer.innerHTML = 'You can upload a maximum of 5 files at once.';
                fileErrorContainer.classList.remove('d-none');
                return;
            }
            
            // Process each selected file
            for (let i = 0; i < files.length; i++) {
                if (isValidFileType(files[i])) {
                    createFilePreview(files[i], filePreviewContainer);
                } else {
                    fileErrorContainer.innerHTML = 'Invalid file type. Please select images, PDFs, Word, Excel, or text files.';
                    fileErrorContainer.classList.remove('d-none');
                }
            }
        }

        // File handling helper functions
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
            // Create the preview element
            const filePreview = document.createElement('div');
            filePreview.className = 'file-preview';
            
            // Process preview based on file type
            if (file.type.startsWith('image/')) {
                // For images, create a thumbnail
                const img = document.createElement('img');
                img.className = 'file-thumbnail';
                filePreview.appendChild(img);
                
                // Use FileReader to load the image preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    img.src = e.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                // For other files, show an appropriate icon
                const iconDiv = document.createElement('div');
                iconDiv.className = 'file-icon';
                iconDiv.innerHTML = `<i class="bi ${getFileIconClass(file)}"></i>`;
                filePreview.appendChild(iconDiv);
            }
            
            // Add file name
            const fileNameDiv = document.createElement('div');
            fileNameDiv.className = 'file-name';
            fileNameDiv.textContent = file.name.length > 20 ? file.name.substring(0, 17) + '...' : file.name;
            fileNameDiv.title = file.name;
            filePreview.appendChild(fileNameDiv);
            
            // Add remove button
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'remove-file';
            removeBtn.innerHTML = '&times;';
            removeBtn.addEventListener('click', function() {
                filePreview.remove();
            });
            filePreview.appendChild(removeBtn);
            
            // Add to container
            container.appendChild(filePreview);
        }

        // File attachment handling
        function setupAttachmentButton() {
            const attachmentBtn = document.getElementById('attachmentBtn');
            const fileInput = document.getElementById('fileUpload');
            
            if (!attachmentBtn || !fileInput) {
                console.log("Attachment elements not found, skipping setup");
                return;
            }
            
            console.log('Setting up attachment button');
            
            // Remove existing event listeners by replacing with clone
            const newBtn = attachmentBtn.cloneNode(true);
            attachmentBtn.parentNode.replaceChild(newBtn, attachmentBtn);
            
            // Add fresh click handler
            newBtn.addEventListener('click', function(e) {
                console.log('Attachment button clicked!');
                e.preventDefault();
                e.stopPropagation();
                
                // Clear any error message
                const fileErrorContainer = document.getElementById('fileErrorContainer');
                if (fileErrorContainer) {
                    fileErrorContainer.classList.add('d-none');
                    fileErrorContainer.textContent = '';
                }
                
                // This is the critical line that triggers the file browser
                fileInput.click();
            });
            
            console.log('Attachment button setup complete');
        }

        // ============= CONVERSATION REFRESH =============
        function refreshActiveConversation() {
            // Don't refresh if there are file attachments
            const filePreviewContainer = document.getElementById('filePreviewContainer');
            if (filePreviewContainer && filePreviewContainer.querySelectorAll('.file-preview').length > 0) {
                debugLog('Refresh prevented - attachments present');
                return;
            }
            
            // Don't refresh if user is typing
            const textarea = document.getElementById('messageContent');
            if (textarea && textarea.value.trim() && Date.now() - window.lastTypingTime < 5000) {
                debugLog('Refresh prevented - user is typing');
                return;
            }
            
            // Don't refresh if we just sent a message
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
            
            const conversationId = activeConversationItem.dataset.conversationId;
            forceRefreshConversation(conversationId);
            
            // Reset refreshing flag after delay
            setTimeout(() => {
                window.isRefreshing = false;
            }, 2000);
        }

        function forceRefreshConversation(conversationId, immediate = false) {
            if (!conversationId) return;
            
            const activeConversationId = document.querySelector('.conversation-item.active')?.dataset.conversationId;
            if (activeConversationId !== conversationId) return;
            
            // Add timestamp to prevent caching
            const cacheParam = '&_=' + new Date().getTime();
            
            // Log refresh attempt
            console.log(`Force refreshing conversation ${conversationId} (immediate: ${immediate})`);
            
            // If immediate refresh is requested, bypass the queue
            if (immediate) {
                window.isRefreshing = true;
            } else if (window.isRefreshing) {
                console.log('Refresh already in progress, skipping');
                return;
            }
            
            fetch(getRouteUrl(ROUTE_PREFIX + '.get-conversation') + '?id=' + conversationId + cacheParam, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Cache-Control': 'no-cache, no-store, must-revalidate',
                    'Pragma': 'no-cache',
                    'Expires': '0'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.html) {
                    // First store scroll position and check if at bottom
                    const messagesContainer = document.getElementById('messagesContainer');
                    const wasAtBottom = messagesContainer ? isAtBottom(messagesContainer) : true;
                    
                    // Update conversation content
                    const conversationContent = document.getElementById('conversationContent');
                    if (conversationContent) {
                        conversationContent.innerHTML = data.html;
                        
                        // Re-initialize message form and actions
                        window.messageFormInitialized = false;
                        
                        // Set a timeout to ensure DOM is fully updated
                        setTimeout(() => {
                            initializeMessageForm(conversationId, true);
                            initializeMessageActions();
                            setupAttachmentButton(); // Make sure attachment button works
                            initializeSearchButton(); // Restore search functionality
                            
                            // Scroll to bottom if user was at bottom before
                            const newMessagesContainer = document.getElementById('messagesContainer');
                            if (newMessagesContainer && wasAtBottom) {
                                newMessagesContainer.scrollTop = newMessagesContainer.scrollHeight;
                            }
                        }, 100);
                    }
                }
                
                // Reset refreshing flag
                window.isRefreshing = false;
            })
            .catch(error => {
                console.error('Error refreshing conversation:', error);
                window.isRefreshing = false;
            });
        }

        function smoothRefreshConversationList() {
            fetch(getRouteUrl(ROUTE_PREFIX + '.get-conversations'))
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    const conversationListContainer = document.querySelector('.conversation-list-items');
                    if (conversationListContainer) {
                        conversationListContainer.innerHTML = data.html;
                        addConversationClickHandlers();
                        updateNavbarUnreadCount();
                    }
                })
                .catch(error => {
                    console.error('Failed to refresh conversation list:', error);
                });
        }

        // ============= MESSAGE ACTIONS =============
        function initializeMessageActions() {
            // Add event delegation for message actions
            const messagesContainer = document.getElementById('messagesContainer');
            if (!messagesContainer) return;
            
            // Remove any existing handlers
            messagesContainer.removeEventListener('click', handleMessageActions);
            
            // Add click handler for message actions
            messagesContainer.addEventListener('click', handleMessageActions);
        }

        function handleMessageActions(e) {
            // Check if this is a message action button
            const actionBtn = e.target.closest('.message-action');
            if (!actionBtn) return;
            
            e.preventDefault();
            
            const messageId = actionBtn.getAttribute('data-message-id');
            const action = actionBtn.getAttribute('data-action');
            
            if (!messageId || !action) return;
            
            console.log(`Message action: ${action} on message ${messageId}`);
            
            switch (action) {
                case 'unsend':
                    showUnsendConfirmation(messageId);
                    break;
                default:
                    console.log(`Unhandled message action: ${action}`);
            }
        }

        function showUnsendConfirmation(messageId) {
            const modal = document.getElementById('confirmUnsendModal');
            const confirmBtn = document.getElementById('confirmUnsendButton');
            
            if (!modal || !confirmBtn) return;
            
            // Remove previous listener and add new one
            const newBtn = confirmBtn.cloneNode(true);
            confirmBtn.parentNode.replaceChild(newBtn, confirmBtn);
            
            newBtn.addEventListener('click', function() {
                unsendMessage(messageId);
                
                // Hide the modal
                const bsModal = bootstrap.Modal.getInstance(modal);
                if (bsModal) {
                    bsModal.hide();
                }
            });
            
            // Show the modal
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
        }

        function unsendMessage(messageId) {
            fetch(getRouteUrl(ROUTE_PREFIX + '.unsend'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    message_id: messageId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Refresh the conversation to show the unsent message
                    const conversationId = document.querySelector('input[name="conversation_id"]')?.value;
                    if (conversationId) {
                        forceRefreshConversation(conversationId);
                    }
                } else {
                    showErrorModal(data.error || 'Failed to unsend message');
                }
            })
            .catch(error => {
                console.error('Error unsending message:', error);
                showErrorModal('An error occurred while unsending the message');
            });
        }

        // ============= MARK AS READ =============
        function markConversationAsRead(conversationId) {
            if (!conversationId) return;
            
            console.log('Marking conversation as read:', conversationId);
            
            fetch(getRouteUrl(ROUTE_PREFIX + '.mark-read'), {
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
                    console.log('Conversation marked as read');
                    updateNavbarUnreadCount();
                }
            })
            .catch(error => {
                console.error('Error marking conversation as read:', error);
            });
        }

        function updateNavbarUnreadCount() {
            // First check if the user has any unread messages
            fetch(getRouteUrl(ROUTE_PREFIX + '.unread-count'))
                .then(response => response.json())
                .then(data => {
                    // Update the navbar badge
                    const navbarBadge = document.getElementById('navbarMessageCount');
                    const sidebarBadge = document.getElementById('sidebarMessageCount');
                    
                    if (data.unreadCount > 0) {
                        // Show and update badges
                        if (navbarBadge) {
                            navbarBadge.textContent = data.unreadCount;
                            navbarBadge.classList.remove('d-none');
                        }
                        if (sidebarBadge) {
                            sidebarBadge.textContent = data.unreadCount;
                            sidebarBadge.classList.remove('d-none');
                        }
                    } else {
                        // Hide badges when no unread messages
                        if (navbarBadge) {
                            navbarBadge.classList.add('d-none');
                        }
                        if (sidebarBadge) {
                            sidebarBadge.classList.add('d-none');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error getting unread count:', error);
                });
        }

        // ============= NEW CONVERSATION =============
        function setupNewConversationForm() {
            const form = document.getElementById('newConversationForm');
            if (!form) return;
            
            const recipientTypeSelect = document.getElementById('recipientType');
            const recipientIdSelect = document.getElementById('recipientId');
            const startBtn = document.getElementById('startConversationBtn');
            const feedbackContainer = document.getElementById('existingConversationFeedback');
            
            // Fetch users when recipient type changes
            if (recipientTypeSelect && recipientIdSelect) {
                recipientTypeSelect.addEventListener('change', function() {
                    fetchUsers(this.value, recipientIdSelect);
                });
                
                // Initial fetch based on default type
                fetchUsers(recipientTypeSelect.value, recipientIdSelect);
            }
            
            // Check for existing conversation when recipient is selected
            if (recipientIdSelect) {
                recipientIdSelect.addEventListener('change', function() {
                    const selectedRecipientId = this.value;
                    const recipientType = recipientTypeSelect?.value || 'cose_staff';
                    
                    if (!selectedRecipientId) return;
                    
                    // Clear existing feedback
                    if (feedbackContainer) {
                        feedbackContainer.innerHTML = '';
                        feedbackContainer.classList.add('d-none');
                    }
                    
                    // Check if a conversation already exists with this recipient
                    const formData = new FormData();
                    formData.append('recipient_id', selectedRecipientId);
                    formData.append('recipient_type', recipientType);
                    
                    fetch(getRouteUrl(ROUTE_PREFIX + '.check-existing-conversation'), {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'X-Requested-With': 'XMLHttpRequest',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.exists && data.conversation_id) {
                            // Show feedback about existing conversation
                            if (feedbackContainer) {
                                feedbackContainer.innerHTML = `
                                    <div class="alert alert-info">
                                        <i class="bi bi-info-circle me-2"></i>
                                        You already have an existing conversation with this person.
                                    </div>
                                `;
                                feedbackContainer.classList.remove('d-none');
                            }
                            
                            // Store conversation ID on button for redirect
                            if (startBtn) {
                                startBtn.dataset.conversationId = data.conversation_id;
                                startBtn.textContent = 'Go to Existing Conversation';
                            }
                        } else {
                            // Reset button text and conversation ID
                            if (startBtn) {
                                startBtn.dataset.conversationId = '';
                                startBtn.textContent = 'Send Message';
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error checking for existing conversation:', error);
                    });
                });
            }
            
            // Handle form submission - redirect if needed
            form.addEventListener('submit', function(event) {
                // If there's an existing conversation redirect
                if (startBtn && startBtn.dataset.conversationId) {
                    event.preventDefault();
                    
                    // Show loading state
                    startBtn.disabled = true;
                    startBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Redirecting...';
                    
                    // Redirect to existing conversation
                    window.location.href = `/${ROLE_PREFIX}/messaging?conversation=${startBtn.dataset.conversationId}`;
                    return false;
                }
            });
        }

        function fetchUsers(type, selectElement) {
            if (!selectElement) return;
            
            // Show loading state
            selectElement.disabled = true;
            selectElement.innerHTML = '<option value="">Loading...</option>';
            
            // Make AJAX request to get users
            console.log(`Fetching users of type: ${type}`);
            
            fetch(`/${ROLE_PREFIX}/messaging/get-users?type=${type}`)
                .then(response => {
                    console.log('User fetch response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Received users data:', data);
                    
                    // Enable the select
                    selectElement.disabled = false;
                    
                    // Clear feedback if it exists
                    const existingFeedback = document.getElementById('existingConversationFeedback');
                    if (existingFeedback) {
                        existingFeedback.classList.add('d-none');
                        existingFeedback.innerHTML = '';
                    }
                    
                    // Reset conversation ID if it was set
                    const startBtn = document.getElementById('startConversationBtn');
                    if (startBtn) {
                        startBtn.dataset.conversationId = '';
                    }
                    
                    // Check for users
                    if (data.users && data.users.length > 0) {
                        // User found, populate the dropdown
                        selectElement.innerHTML = '<option value="" selected disabled>Select a recipient</option>';
                        
                        data.users.forEach(user => {
                            const option = document.createElement('option');
                            option.value = user.id;
                            option.textContent = user.name;
                            option.dataset.email = user.email || '';
                            option.dataset.mobile = user.mobile || '';
                            selectElement.appendChild(option);
                        });
                    } else {
                        // No users found
                        selectElement.innerHTML = '<option value="" selected disabled>No users found</option>';
                    }
                })
                .catch(error => {
                    console.error('Error fetching users:', error);
                    selectElement.innerHTML = '<option value="" selected disabled>Error loading users</option>';
                    selectElement.disabled = false;
                });
        }

        

        // ============= GROUP MANAGEMENT =============
        function setupGroupEvents() {
            // New Group Form submission
            const newGroupForm = document.getElementById('newGroupForm');
            if (newGroupForm) {
                newGroupForm.addEventListener('submit', handleNewGroupSubmit);
            }
            
            // Toggle participant sections
            const toggleButtons = document.querySelectorAll('.toggle-section');
            toggleButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const section = this.getAttribute('data-section');
                    const listElement = document.querySelector('.participant-list.' + section + '-list');
                    
                    if (listElement) {
                        if (listElement.style.display === 'none') {
                            listElement.style.display = 'block';
                            this.innerHTML = '<i class="bi bi-dash"></i> Hide';
                            
                            // Fetch users for this section if it's empty
                            const userCheckboxes = listElement.querySelector('.user-checkboxes');
                            if (userCheckboxes && userCheckboxes.children.length === 0) {
                                fetchGroupUsers(section === 'staff' ? 'cose_staff' : section);
                            }
                        } else {
                            listElement.style.display = 'none';
                            this.innerHTML = '<i class="bi bi-plus"></i> Add';
                        }
                    }
                });
            });
            
            // Leave group button handling
            setupLeaveGroupButton();
            
            // Add member button handling
            setupAddMemberButton();
        }

        function handleNewGroupSubmit(e) {
            e.preventDefault();
            
            // Get form data
            const formData = new FormData(this);
            
            // Add selected staff members
            selectedAdmins.forEach(id => formData.append('admins[]', id));
            selectedCareWorkers.forEach(id => formData.append('care_workers[]', id));
            
            // Validate form
            if (formData.get('name').trim() === '') {
                showErrorModal('Please enter a group name');
                return;
            }
            
            if (selectedAdmins.length === 0 && selectedCareWorkers.length === 0) {
                showErrorModal('Please select at least one participant');
                return;
            }
            
            // Show loading state
            const submitButton = document.getElementById('createGroupBtn');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Creating...';
            }
            
            // Submit form
            fetch(getRouteUrl(ROUTE_PREFIX + '.create-group'), {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reset form and close modal
                    document.getElementById('newGroupForm').reset();
                    selectedAdmins = [];
                    selectedCareWorkers = [];
                    updateSelectedParticipants();
                    
                    // Hide modal
                    const modal = document.getElementById('newGroupModal');
                    const bsModal = bootstrap.Modal.getInstance(modal);
                    if (bsModal) {
                        bsModal.hide();
                    }
                    
                    // Refresh conversation list
                    smoothRefreshConversationList();
                    
                    // Load the new conversation
                    if (data.conversation_id) {
                        setTimeout(() => {
                            loadConversation(data.conversation_id);
                        }, 300);
                    }
                } else {
                    showErrorModal(data.message || 'Failed to create group');
                }
                
                // Reset button state
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.innerHTML = 'Create Group';
                }
            })
            .catch(error => {
                console.error('Error creating group:', error);
                showErrorModal('An error occurred while creating the group');
                
                // Reset button state
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.innerHTML = 'Create Group';
                }
            });
        }

        // ============= SEARCH FUNCTIONALITY =============
        function setupSearchFunctionality() {
            // Conversation search input
            const searchInput = document.getElementById('conversationSearch');
            
            if (searchInput) {
                // Debounce search input
                searchInput.addEventListener('input', debounce(function() {
                    const searchTerm = this.value.trim();
                    searchConversations(searchTerm);
                }, 300));
            }
        }

        function searchConversations(term) {
            const conversationItems = document.querySelectorAll('.conversation-item');
            
            if (term === '') {
                // Show all conversations
                conversationItems.forEach(item => {
                    item.style.display = '';
                });
                return;
            }
            
            // Filter conversations
            const regex = new RegExp(escapeRegExp(term), 'i');
            
            conversationItems.forEach(item => {
                const name = item.querySelector('.conversation-name')?.textContent || '';
                const lastMessage = item.querySelector('.conversation-snippet')?.textContent || '';
                
                if (regex.test(name) || regex.test(lastMessage)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        // ============= MOBILE VIEW =============
        function setupMobileView() {
            // Toggle conversation list on mobile
            const toggleButton = document.querySelector('.toggle-conversation-list');
            const conversationList = document.querySelector('.conversation-list');
            
            if (toggleButton && conversationList) {
                toggleButton.addEventListener('click', function() {
                    conversationList.classList.toggle('hidden');
                });
            }
        }

        // ============= ERROR HANDLING =============
        function showErrorModal(message) {
            const modal = document.getElementById('errorModal');
            const messageElement = document.getElementById('errorModalMessage');
            
            if (!modal || !messageElement) {
                // Fallback to alert
                alert(message);
                return;
            }
            
            // Set the error message
            messageElement.textContent = message;
            
            // Show the modal
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
        }

        // ============= URL PARAMETER HANDLING =============
        function handleUrlParameters() {
            // Check for conversation parameter
            const urlParams = new URLSearchParams(window.location.search);
            const conversationId = urlParams.get('conversation');
            
            if (conversationId) {
                console.log('Loading conversation from URL:', conversationId);
                
                // Activate this conversation
                const conversationItem = document.querySelector(`.conversation-item[data-conversation-id="${conversationId}"]`);
                if (conversationItem) {
                    // Simulate a click on the conversation item
                    conversationItem.click();
                } else {
                    // Direct load if item not found in list (may be a new conversation)
                    loadConversation(conversationId);
                }
            }
        }

        // ============= INPUT PROTECTION =============
        function setupInputProtection() {
            // Prevent form resubmission on refresh
            if (window.history.replaceState) {
                window.history.replaceState(null, null, window.location.href);
            }
            
            // Create a MutationObserver to watch for dynamically loaded forms
            // This may be causing double-initialization issues, so we'll add a flag
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.addedNodes && mutation.addedNodes.length > 0) {
                        for (let i = 0; i < mutation.addedNodes.length; i++) {
                            const node = mutation.addedNodes[i];
                            
                            // Check if we've added a new conversation with a message form
                            if (node.nodeType === Node.ELEMENT_NODE && node.querySelector) {
                                const newForm = node.querySelector('#messageForm');
                                if (newForm) {
                                    // Get conversation ID from the form
                                    const conversationId = newForm.querySelector('input[name="conversation_id"]')?.value;
                                    if (conversationId) {
                                        console.log('Found dynamically added message form for conversation:', conversationId);
                                        
                                        // Reset flag to allow initialization
                                        window.messageFormInitialized = false;
                                        
                                        // Initialize the form
                                        setTimeout(() => {
                                            initializeMessageForm(conversationId);
                                            // Also initialize attachment button
                                            setupAttachmentButton();
                                        }, 100);
                                    }
                                }
                            }
                        }
                    }
                });
            });
            
            // Start observing for conversation content changes
            const targetNode = document.getElementById('conversationContent');
            if (targetNode) {
                observer.observe(targetNode, { 
                    childList: true,
                    subtree: true
                });
            }
        }

        // ============= SIDEBAR =============
        function setupSidebar() {
            // Check if this is the messaging page
            const sidebarMenuItem = document.querySelector('.sidebar-menu a[href*="messaging"]');
            if (sidebarMenuItem) {
                sidebarMenuItem.classList.add('active');
            }
        }

        // ============= INITIALIZATION =============
        // Initially setup forms, conversation handling, etc.
        document.addEventListener('DOMContentLoaded', function() {
            // Set up direct conversations
            addConversationClickHandlers();
            setupNewConversationForm();
            setupGroupEvents();
            setupSearchFunctionality();
            setupMobileView();
            setupInputProtection();
            setupSidebar();
            
            // Handle URL parameters
            handleUrlParameters();
            
            // Update unread count
            updateNavbarUnreadCount();
            
            // Set up refresh interval
            setInterval(refreshActiveConversation, 8000);
            
            console.log('Messaging system initialized');
        });

        // Add spinner animation styles if needed
        function addSpinnerStyles() {
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
                        width: 1rem;
                        height: 1rem;
                        vertical-align: text-bottom;
                        border: 0.2em solid currentColor;
                        border-right-color: transparent;
                        border-radius: 50%;
                        animation: spinner-border .75s linear infinite;
                    }
                    
                    .spinner-border-sm {
                        width: 1rem;
                        height: 1rem;
                        border-width: 0.15em;
                    }
                `;
                document.head.appendChild(styleEl);
            }
        }

        // Call this function to ensure spinner styles are added
        addSpinnerStyles();

        // ============= CONVERSATION SEARCH =============

        function closeSearchUI() {
            // Get reference to the correct search container
            const searchContainer = document.getElementById('messageSearchContainer');
            if (searchContainer) {
                searchContainer.classList.remove('active');
            }
            
            // Also update messages container
            const messagesContainer = document.getElementById('messagesContainer');
            if (messagesContainer) {
                messagesContainer.classList.remove('search-active');
            }
            
            // Reset search state
            resetSearchHighlights();
            searchMatches = [];
            currentMatchIndex = -1;
            
            // Reset form elements
            const searchInput = document.getElementById('messageSearchInput');
            const resultsInfo = document.getElementById('searchResultsCount');
            
            if (searchInput) searchInput.value = '';
            if (resultsInfo) resultsInfo.textContent = '';
            
            // Disable navigation buttons
            const prevButton = document.getElementById('searchPrevBtn');
            const nextButton = document.getElementById('searchNextBtn');
            
            if (prevButton) prevButton.disabled = true;
            if (nextButton) nextButton.disabled = true;
        }

        function resetSearchAfterConversationLoad() {
            closeSearchUI();
            window.searchInitialized = false;
        }

        function searchInConversation(term, container, resultsInfo) {
            // Reset previous search
            resetSearchHighlights();
            searchMatches = [];
            currentMatchIndex = -1;
            
            if (!term || term.trim() === '') {
                if (resultsInfo) resultsInfo.textContent = '';
                return;
            }
            
            // Get all message content elements
            const messageContents = container.querySelectorAll('.message-content');
            
            // Escape regex special characters
            const escapedTerm = escapeRegExp(term);
            const regex = new RegExp(`(${escapedTerm})`, 'gi');
            
            // Search in each message
            messageContents.forEach(content => {
                const originalText = content.innerText;
                
                if (regex.test(originalText)) {
                    // Create a clone to work with
                    const clone = content.cloneNode(true);
                    const textNodes = getTextNodesIn(clone);
                    
                    // Apply highlighting to each text node
                    textNodes.forEach(node => {
                        const text = node.nodeValue;
                        if (regex.test(text)) {
                            const span = document.createElement('span');
                            span.innerHTML = text.replace(regex, '<span class="search-highlight">$1</span>');
                            
                            if (node.parentNode) {
                                node.parentNode.replaceChild(span, node);
                                
                                // Store matching elements
                                const highlights = span.querySelectorAll('.search-highlight');
                                highlights.forEach(highlight => {
                                    searchMatches.push(highlight);
                                });
                            }
                        }
                    });
                    
                    // Replace content with highlighted version
                    content.innerHTML = clone.innerHTML;
                }
            });
            
            // Update results info
            if (resultsInfo) {
                if (searchMatches.length > 0) {
                    resultsInfo.textContent = `${searchMatches.length} match${searchMatches.length !== 1 ? 'es' : ''} found`;
                    
                    // Highlight first match
                    currentMatchIndex = 0;
                    highlightCurrentMatch();
                } else {
                    resultsInfo.textContent = 'No matches found';
                }
            }
        }

        function navigateSearchResults(forward, container, resultsInfo) {
            if (searchMatches.length === 0) return;
            
            // Update current match index
            if (forward) {
                currentMatchIndex = (currentMatchIndex + 1) % searchMatches.length;
            } else {
                currentMatchIndex = (currentMatchIndex - 1 + searchMatches.length) % searchMatches.length;
            }
            
            highlightCurrentMatch();
            
            // Update results info
            if (resultsInfo && searchMatches.length > 0) {
                resultsInfo.textContent = `${currentMatchIndex + 1} of ${searchMatches.length} matches`;
            }
        }

        function highlightCurrentMatch() {
            // Remove active class from all matches
            searchMatches.forEach(match => {
                match.classList.remove('search-highlight-active');
            });
            
            // Add active class to current match
            if (searchMatches[currentMatchIndex]) {
                searchMatches[currentMatchIndex].classList.add('search-highlight-active');
                
                // Scroll to match
                searchMatches[currentMatchIndex].scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }
        }

        function resetSearchHighlights() {
            const highlights = document.querySelectorAll('.search-highlight');
            
            highlights.forEach(highlight => {
                // Replace with just the text content
                const text = document.createTextNode(highlight.textContent);
                highlight.parentNode.replaceChild(text, highlight);
            });
        }

        function resetSearchAfterConversationLoad() {
            closeSearchUI();
            window.searchInitialized = false;
        }

        function getTextNodesIn(node) {
            const textNodes = [];
            
            if (node.nodeType === 3) {
                textNodes.push(node);
            } else {
                const children = node.childNodes;
                for (let i = 0; i < children.length; i++) {
                    textNodes.push.apply(textNodes, getTextNodesIn(children[i]));
                }
            }
            
            return textNodes;
        }

        function createAndPopulateSearchContainer() {
            // Find the container
            const searchContainer = document.getElementById('messageSearchContainer');
            
            // If container exists but is empty, populate it
            if (searchContainer && searchContainer.children.length === 0) {
                console.log('Search container exists but is empty, populating it now');
                
                // Create search form HTML
                searchContainer.innerHTML = `
                    <div class="search-input-container d-flex align-items-center">
                        <input type="text" class="form-control form-control-sm me-2" id="messageSearchInput" placeholder="Search in conversation...">
                        <div class="search-buttons d-flex">
                            <button type="button" class="btn btn-sm btn-outline-primary me-1" id="searchPrevBtn" disabled>
                                <i class="bi bi-arrow-up"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary me-1" id="searchNextBtn" disabled>
                                <i class="bi bi-arrow-down"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" id="closeSearchBtn">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    </div>
                    <div class="search-results-count mt-1 text-center" id="searchResultsCount"></div>
                `;
                
                return true; // Container was populated
            } else if (!searchContainer) {
                console.error('Search container not found');
                return false;
            } else {
                console.log('Search container already populated');
                return true;
            }
        }
    </script>
</body>
</html>