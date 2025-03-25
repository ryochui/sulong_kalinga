<div class="modal fade" id="deleteBarangayModal" tabindex="-1" aria-labelledby="deleteBarangayModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color:rgb(251, 68, 68);">
                <h5 class="modal-title text-white" id="deleteBarangayModalLabel">Confirm Barangay Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="barangayDeleteMessage" class="alert d-none" role="alert"></div>
                
                <div id="deleteConfirmation">
                    <p class="text-danger">
                        <i class="bx bx-error-circle"></i> 
                        <strong>Warning!</strong> You are about to delete this barangay.
                    </p>
                    <p>Are you sure you want to permanently delete <span id="barangayNameToDelete" style="font-weight: bold;"></span>?</p>
                    <div class="mb-3">
                        <label for="barangayDeletePasswordInput" class="form-label">Enter Your Password to Confirm</label>
                        <input type="password" class="form-control" id="barangayDeletePasswordInput" placeholder="Enter your password" required>
                        <input type="hidden" id="barangayIdToDelete" value="">
                    </div>
                </div>
                
                <div id="deleteSuccess" class="d-none">
                    <p class="text-success">
                        <i class="bx bx-check-circle"></i>
                        <strong>Success!</strong> The barangay has been deleted successfully.
                    </p>
                    <p>The page will reload shortly.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelDeleteButton">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmBarangayDeleteButton">
                    <i class="bx bxs-trash"></i> Delete Barangay
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Function to open the delete modal
window.openDeleteBarangayModal = function(id, name) {
    // Reset modal state
    document.getElementById('barangayDeleteMessage').classList.add('d-none');
    document.getElementById('deleteConfirmation').classList.remove('d-none');
    document.getElementById('deleteSuccess').classList.add('d-none');
    document.getElementById('barangayDeletePasswordInput').value = '';
    document.getElementById('barangayIdToDelete').value = id;
    document.getElementById('barangayNameToDelete').textContent = name;
    
    const confirmButton = document.getElementById('confirmBarangayDeleteButton');
    confirmButton.disabled = false;
    confirmButton.innerHTML = '<i class="bx bxs-trash"></i> Delete Barangay';
    confirmButton.classList.remove('d-none');
    
    document.getElementById('cancelDeleteButton').textContent = 'Cancel';
    
    // Show the modal
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteBarangayModal'));
    deleteModal.show();
}

// Function to show error message
function showError(message) {
    const messageElement = document.getElementById('barangayDeleteMessage');
    messageElement.textContent = message;
    messageElement.classList.remove('d-none', 'alert-success');
    messageElement.classList.add('alert-danger');
}

// Function to show detailed error message with guidance
function showDependencyError(message, errorType) {
    // First hide the confirmation form
    document.getElementById('deleteConfirmation').classList.add('d-none');
    
    // Update the message element
    const messageElement = document.getElementById('barangayDeleteMessage');
    messageElement.classList.remove('d-none', 'alert-success', 'alert-warning');
    messageElement.classList.add('alert-danger');
    
    // Create structured error content with icon and guidance
    let errorContent = `
        <div class="d-flex align-items-center mb-2">
            <i class="bx bx-error-circle me-2" style="font-size: 1.5rem;"></i>
            <strong>Unable to Delete</strong>
        </div>
        <p>${message}</p>
    `;
    
    // Add specific guidance based on error type
    if (errorType === 'dependency_beneficiaries') {
        errorContent += `
            <div class="mt-2 border-top pt-2">
                <strong>What you can do instead:</strong>
                <p class="mt-2 mb-2">This barangay has beneficiaries assigned to it. For data integrity, barangays with assigned beneficiaries cannot be deleted.</p>
                <ol class="mt-2 mb-0">
                    <li>First, reassign all beneficiaries from this barangay to another barangay</li>
                    <li>Go to the <a href="{{ route('admin.beneficiaryProfile') }}">Beneficiary List</a>, filter by this barangay</li>
                    <li>Edit each beneficiary's profile to change their barangay</li>
                    <li>Once all beneficiaries are reassigned, you can delete this barangay</li>
                </ol>
            </div>
        `;
    } else if (errorType === 'last_barangay') {
        errorContent += `
            <div class="mt-2 border-top pt-2">
                <strong>System Requirement:</strong>
                <p class="mt-2 mb-2">This is the only remaining barangay for its municipality. Each municipality must have at least one barangay.</p>
                <ol class="mt-2 mb-0">
                    <li>Before deleting this barangay, add another barangay to this municipality</li>
                    <li>Click on "Add Barangay" and create at least one more barangay</li>
                    <li>Once another barangay exists, you can delete this one</li>
                </ol>
            </div>
        `;
    } else if (errorType === 'permission_denied') {
        errorContent += `
            <div class="mt-2 border-top pt-2">
                <strong>Permission Denied:</strong>
                <p class="mt-2 mb-2">Only administrators with sufficient permissions can delete barangays.</p>
                <p>Please contact a system administrator if you need this barangay removed.</p>
            </div>
        `;
    }
    
    messageElement.innerHTML = errorContent;
    
    // Change the cancel button text
    document.getElementById('cancelDeleteButton').textContent = 'Close';
    
    // Hide the delete button
    document.getElementById('confirmBarangayDeleteButton').classList.add('d-none');
}

// Function to show success message
function showSuccess() {
    document.getElementById('deleteConfirmation').classList.add('d-none');
    document.getElementById('deleteSuccess').classList.remove('d-none');
    document.getElementById('confirmBarangayDeleteButton').classList.add('d-none');
    document.getElementById('cancelDeleteButton').textContent = 'Close';
    
    setTimeout(function() {
        window.location.reload();
    }, 2000);
}

// Setup event handlers when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Password input event to clear error messages
    document.getElementById('barangayDeletePasswordInput').addEventListener('input', function() {
        document.getElementById('barangayDeleteMessage').classList.add('d-none');
    });
    
    // Delete confirmation button click handler
    document.getElementById('confirmBarangayDeleteButton').addEventListener('click', function() {
        const password = document.getElementById('barangayDeletePasswordInput').value.trim();
        const barangayId = document.getElementById('barangayIdToDelete').value;
        
        if (!password) {
            showError('Please enter your password to confirm deletion.');
            return;
        }
        
        // Show loading state
        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Deleting...';
        
        // Send delete request with CSRF token and password
        fetch(`/admin/delete-barangay/${barangayId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ password: password })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccess();
            } else {
                // Check for specific error types
                if (data.error_type) {
                    showDependencyError(data.message, data.error_type);
                } else {
                    showError(data.message || 'Failed to delete barangay.');
                    this.disabled = false;
                    this.innerHTML = '<i class="bx bxs-trash"></i> Delete Barangay';
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('An unexpected error occurred. Please try again.');
            this.disabled = false;
            this.innerHTML = '<i class="bx bxs-trash"></i> Delete Barangay';
        });
    });
});
</script>