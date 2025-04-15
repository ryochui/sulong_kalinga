<!-- Initial Delete Confirmation Modal -->
<div class="modal fade" id="confirmDeleteWeeklyCarePlanModal" tabindex="-1" aria-labelledby="confirmDeleteWeeklyCarePlanModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color:rgb(251, 68, 68);">
                <h5 class="modal-title text-white" id="confirmDeleteWeeklyCarePlanModalLabel">Delete Weekly Care Plan?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-danger">
                    <i class="bx bx-error-circle"></i> 
                    <strong>Warning!</strong> You are about to delete this weekly care plan.
                </p>
                <p>Are you sure you want to delete the weekly care plan for <span id="initialBeneficiaryNameToDelete" style="font-weight: bold;"></span>?</p>
                <input type="hidden" id="initialWeeklyCarePlanIdToDelete" value="">

                <p>You are about to permanently delete the record. <strong>This action cannot be undone and all data will be permanently lost.</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="proceedToPasswordButton">
                    <i class="bx bx-trash"></i> Proceed to Delete
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Setup event handlers for first-level modal
document.addEventListener('DOMContentLoaded', function() {
    // When user clicks "Proceed to Delete" in initial modal
    document.getElementById('proceedToPasswordButton').addEventListener('click', function() {
        // Get values from the initial modal
        const id = document.getElementById('initialWeeklyCarePlanIdToDelete').value;
        const name = document.getElementById('initialBeneficiaryNameToDelete').textContent;
        
        // Close the initial modal
        bootstrap.Modal.getInstance(document.getElementById('confirmDeleteWeeklyCarePlanModal')).hide();
        
        // Open the second-level modal with the collected information
        window.openPasswordConfirmationModal(id, name);
    });
});
</script>