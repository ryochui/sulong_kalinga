<div class="modal fade" id="statusChangeModal" tabindex="-1" aria-labelledby="statusChangeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusChangeModalLabel">Confirm Status Change</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to change the status of this <span id="entityType" style="font-weight: bold;"></span>?</p>
                <form id="statusChangeForm">
                    <div class="mb-3">
                        <label for="reasonSelect" class="form-label">Reason for Status Change</label>
                        <select class="form-select" id="reasonSelect" required>
                            <option value="" selected disabled>Select a reason</option>
                            <option value="performance">Opted Out</option>
                            <option value="policy">Deceased</option>
                            <option value="resignation">Hospitalized</option>
                            <option value="other">Moved Residence</option>
                            <option value="other">No Longer need Assistance</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmStatusChangeButton">Confirm</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    let selectedStatusElement = null;
    let entityType = ""; // Store the entity type dynamically

    // Function to open the modal and store the selected status element
    window.openStatusChangeModal = function (selectElement, type) {
        selectedStatusElement = selectElement; // Store the reference to the dropdown
        entityType = type; // Set the entity type dynamically
        document.getElementById("entityType").textContent = entityType; // Update the modal text
        const statusChangeModal = new bootstrap.Modal(document.getElementById("statusChangeModal"));
        statusChangeModal.show();
    };

    // Handle the status change confirmation
    const confirmStatusChangeButton = document.getElementById("confirmStatusChangeButton");
    confirmStatusChangeButton.addEventListener("click", function () {
        const reasonSelect = document.getElementById("reasonSelect");
        const selectedReason = reasonSelect.value;

        if (!selectedReason) {
            alert("Please select a reason for the status change.");
            return;
        }

        // Simulate saving the status change (replace with actual logic)
        alert(`Status changed to "${selectedStatusElement.value}" for ${entityType} with reason: "${selectedReason}".`);

        // Reset the modal fields
        reasonSelect.value = "";
        const statusChangeModal = bootstrap.Modal.getInstance(document.getElementById("statusChangeModal"));
        statusChangeModal.hide();
    });
});
</script>