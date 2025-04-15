<div class="modal fade" id="editGcpRedirectModal" tabindex="-1" aria-labelledby="editGcpRedirectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#0d6efd;">
                <h5 class="modal-title text-white" id="editGcpRedirectModalLabel">Redirecting to Edit Beneficiary</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="mb-4">
                    <i class="bx bx-edit-alt text-primary" style="font-size: 3rem;"></i>
                </div>
                <h5>General Care Plan Editing Information</h5>
                <p>The General Care Plan is incorporated directly into the beneficiary profile.</p>
                <p>Editing a General Care Plan affects the beneficiary information directly.</p>
                <p>You will be redirected to the Edit Beneficiary page where you can modify the General Care Plan information.</p>
                
                <div class="progress mt-4">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
                </div>
                
                <p class="mt-3 mb-0">Redirecting in <span id="editGcpCountdown">5</span> seconds...</p>
                <input type="hidden" id="editGcpBeneficiaryId" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="editGcpNowButton">Go Now</button>
            </div>
        </div>
    </div>
</div>

<script>
let editGcpCountdownInterval;

function openEditGcpRedirectModal(beneficiaryId) {
    // Set the beneficiary ID for redirect
    document.getElementById('editGcpBeneficiaryId').value = beneficiaryId;
    
    // Reset progress bar
    const progressBar = document.querySelector('#editGcpRedirectModal .progress-bar');
    progressBar.style.width = '0%';
    
    // Reset countdown
    document.getElementById('editGcpCountdown').textContent = '5';
    
    // Show the modal
    const modal = new bootstrap.Modal(document.getElementById('editGcpRedirectModal'));
    modal.show();
    
    // Start progress bar animation
    let progress = 0;
    const progressInterval = setInterval(() => {
        progress += 1;
        progressBar.style.width = `${progress}%`;
        if (progress >= 100) clearInterval(progressInterval);
    }, 50);
    
    // Start countdown
    let countdown = 5;
    clearInterval(editGcpCountdownInterval);
    editGcpCountdownInterval = setInterval(() => {
        countdown -= 1;
        document.getElementById('editGcpCountdown').textContent = countdown;
        
        if (countdown <= 0) {
            clearInterval(editGcpCountdownInterval);
            redirectToEditBeneficiary();
        }
    }, 1000);
}

function redirectToEditBeneficiary() {
    const beneficiaryId = document.getElementById('editGcpBeneficiaryId').value;
    // Use the proper route for editing a beneficiary
    window.location.href = `{{ route('admin.beneficiaries.edit', ':id') }}`.replace(':id', beneficiaryId);
}

// When document is ready
document.addEventListener('DOMContentLoaded', function() {
    // Handle immediate redirect button
    document.getElementById('editGcpNowButton').addEventListener('click', function() {
        clearInterval(editGcpCountdownInterval);
        redirectToEditBeneficiary();
    });
    
    // Stop countdown if modal is closed
    document.getElementById('editGcpRedirectModal').addEventListener('hidden.bs.modal', function() {
        clearInterval(editGcpCountdownInterval);
    });
});
</script>