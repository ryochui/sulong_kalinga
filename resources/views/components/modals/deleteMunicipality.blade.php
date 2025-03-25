<!-- filepath: c:\xampp\htdocs\sulong_kalinga\resources\views\components\modals\deleteMunicipality.blade.php -->
<div class="modal fade" id="deleteMunicipalityModal" tabindex="-1" aria-labelledby="deleteMunicipalityModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color:rgb(251, 68, 68);">
                <h5 class="modal-title text-white" id="deleteMunicipalityModalLabel">Confirm Municipality Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="municipalityDeleteMessage" class="alert d-none" role="alert"></div>
                
                <div id="deleteConfirmation">
                    <p class="text-danger">
                        <i class="bx bx-error-circle"></i> 
                        <strong>Warning!</strong> You are about to delete this municipality.
                    </p>
                    <p>Are you sure you want to permanently delete <span id="municipalityNameToDelete" style="font-weight: bold;"></span>?</p>
                    <div class="mb-3">
                        <label for="municipalityDeletePasswordInput" class="form-label">Enter Your Password to Confirm</label>
                        <input type="password" class="form-control" id="municipalityDeletePasswordInput" placeholder="Enter your password" required>
                        <input type="hidden" id="municipalityIdToDelete" value="">
                    </div>
                </div>
                
                <div id="deleteSuccess" class="d-none">
                    <p class="text-success">
                        <i class="bx bx-check-circle"></i>
                        <strong>Success!</strong> The municipality has been deleted successfully.
                    </p>
                    <p>The page will reload shortly.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelDeleteButton">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmMunicipalityDeleteButton">
                    <i class="bx bxs-trash"></i> Delete Municipality
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Function to open the delete modal
window.openDeleteMunicipalityModal = function(id, name) {
    // Reset modal state
    document.getElementById('municipalityDeleteMessage').classList.add('d-none');
    document.getElementById('deleteConfirmation').classList.remove('d-none');
    document.getElementById('deleteSuccess').classList.add('d-none');
    document.getElementById('municipalityDeletePasswordInput').value = '';
    document.getElementById('municipalityIdToDelete').value = id;
    document.getElementById('municipalityNameToDelete').textContent = name;
    
    const confirmButton = document.getElementById('confirmMunicipalityDeleteButton');
    confirmButton.disabled = false;
    confirmButton.innerHTML = '<i class="bx bxs-trash"></i> Delete Municipality';
    confirmButton.classList.remove('d-none');
    
    document.getElementById('cancelDeleteButton').textContent = 'Cancel';
    
    // Show the modal
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteMunicipalityModal'));
    deleteModal.show();
}

// Function to show error message
function showMunicipalityError(message) {
    const messageElement = document.getElementById('municipalityDeleteMessage');
    messageElement.textContent = message;
    messageElement.classList.remove('d-none', 'alert-success');
    messageElement.classList.add('alert-danger');
}

// Function to show detailed error message with guidance
function showMunicipalityDependencyError(message, errorType) {
    // First hide the confirmation form
    document.getElementById('deleteConfirmation').classList.add('d-none');
    
    // Update the message element
    const messageElement = document.getElementById('municipalityDeleteMessage');
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
    if (errorType === 'dependency_barangays') {
        errorContent += `
            <div class="mt-2 border-top pt-2">
                <strong>What you can do instead:</strong>
                <p class="mt-2 mb-2">This municipality has barangays assigned to it. For data integrity, municipalities with assigned barangays cannot be deleted.</p>
                <ol class="mt-2 mb-0">
                    <li>First, delete all barangays from this municipality</li>
                    <li>If no other references (such as assignments to beneficiaries) exist, you can delete this municipality</li>
                </ol>
            </div>
        `;
    } else if (errorType === 'dependency_beneficiaries') {
        errorContent += `
            <div class="mt-2 border-top pt-2">
                <strong>System Requirement:</strong>
                <p class="mt-2 mb-2">This municipality has beneficiaries assigned to it. Data integrity requires that all records remain linked.</p>
                <ol class="mt-2 mb-0">
                    <li>First, reassign all beneficiaries to another municipality</li>
                    <li>Then delete all barangays from this municipality</li>
                    <li>Finally, you can delete this municipality</li>
                </ol>
            </div>
        `;
    } else if (errorType === 'dependency_care_users') {
        errorContent += `
            <div class="mt-2 border-top pt-2">
                <strong>User Assignments:</strong>
                <p class="mt-2 mb-2">This municipality has care workers or care managers assigned to it.</p>
                <ol class="mt-2 mb-0">
                    <li>First, reassign all users to another municipality</li>
                    <li>Go to the Care Workers and Care Managers profiles</li>
                    <li>Assign them to different municipalities</li>
                    <li>If there are no barangays under this municipality, then you can delete this municipality</li>
                </ol>
            </div>
        `;
    } else if (errorType === 'permission_denied') {
        errorContent += `
            <div class="mt-2 border-top pt-2">
                <strong>Permission Denied:</strong>
                <p class="mt-2 mb-2">Only administrators with sufficient permissions can delete municipalities.</p>
                <p>Please contact a system administrator if you need this municipality removed.</p>
            </div>
        `;
    }
    
    messageElement.innerHTML = errorContent;
    
    // Change the cancel button text
    document.getElementById('cancelDeleteButton').textContent = 'Close';
    
    // Hide the delete button
    document.getElementById('confirmMunicipalityDeleteButton').classList.add('d-none');
}

// Function to show success message
function showMunicipalitySuccess() {
    document.getElementById('deleteConfirmation').classList.add('d-none');
    document.getElementById('deleteSuccess').classList.remove('d-none');
    document.getElementById('confirmMunicipalityDeleteButton').classList.add('d-none');
    document.getElementById('cancelDeleteButton').textContent = 'Close';
    
    setTimeout(function() {
        window.location.reload();
    }, 2000);
}

// Setup event handlers when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Password input event to clear error messages
    document.getElementById('municipalityDeletePasswordInput').addEventListener('input', function() {
        document.getElementById('municipalityDeleteMessage').classList.add('d-none');
    });
    
    // Delete confirmation button click handler
    document.getElementById('confirmMunicipalityDeleteButton').addEventListener('click', function() {
        const password = document.getElementById('municipalityDeletePasswordInput').value.trim();
        const municipalityId = document.getElementById('municipalityIdToDelete').value;
        
        if (!password) {
            showMunicipalityError('Please enter your password to confirm deletion.');
            return;
        }
        
        // Show loading state
        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Deleting...';
        
        // Send delete request with CSRF token and password
        fetch(`/admin/delete-municipality/${municipalityId}`, {
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
                showMunicipalitySuccess();
            } else {
                // Check for specific error types
                if (data.error_type) {
                    showMunicipalityDependencyError(data.message, data.error_type);
                } else {
                    showMunicipalityError(data.message || 'Failed to delete municipality.');
                    this.disabled = false;
                    this.innerHTML = '<i class="bx bxs-trash"></i> Delete Municipality';
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMunicipalityError('An unexpected error occurred. Please try again.');
            this.disabled = false;
            this.innerHTML = '<i class="bx bxs-trash"></i> Delete Municipality';
        });
    });
});
</script>