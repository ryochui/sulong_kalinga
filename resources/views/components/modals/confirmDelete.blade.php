<!-- filepath: c:\xampp\htdocs\sulong_kalinga\resources\views\components\modals\confirmDelete.blade.php -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color:rgb(251, 68, 68);">
                <h5 class="modal-title text-white" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="bx bx-error-circle me-2"></i> Warning: This action cannot be undone!
                </div>
                <p>Are you sure you want to delete <strong id="deleteItemName">this item</strong>?</p>
                <p>Please enter your password to confirm.</p>
                
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="mb-3">
                        <label for="passwordInput" class="form-label">Password</label>
                        <input type="password" class="form-control" id="passwordInput" name="password" placeholder="Enter your password" required>
                        <div class="invalid-feedback" id="passwordError"></div>
                    </div>
                    <input type="hidden" name="delete_id" id="deleteItemId">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteButton">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteForm = document.getElementById('deleteForm');
        const confirmDeleteButton = document.getElementById('confirmDeleteButton');
        const passwordInput = document.getElementById('passwordInput');
        const passwordError = document.getElementById('passwordError');
        
        // When delete button is clicked
        confirmDeleteButton.addEventListener('click', function() {
            if (!passwordInput.value) {
                passwordInput.classList.add('is-invalid');
                passwordError.textContent = 'Password is required to confirm deletion';
                return;
            }
            
            // Submit the form
            deleteForm.submit();
        });
        
        // Reset validation when modal is hidden
        const deleteModal = document.getElementById('deleteModal');
        deleteModal.addEventListener('hidden.bs.modal', function() {
            passwordInput.classList.remove('is-invalid');
            passwordError.textContent = '';
            passwordInput.value = '';
        });
    });
    
    // Function to set up the delete modal with the correct item details
    function setDeleteTarget(element, route) {
        const id = element.dataset.id;
        const name = element.dataset.name;
        
        // Set the form action
        document.getElementById('deleteForm').action = route;
        
        // Set the item name in the confirmation message
        document.getElementById('deleteItemName').textContent = name;
        
        // Set the hidden input if needed
        if (document.getElementById('deleteItemId')) {
            document.getElementById('deleteItemId').value = id;
        }
    }
</script>