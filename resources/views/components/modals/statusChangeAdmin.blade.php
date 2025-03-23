<div class="modal fade" id="statusChangeAdminModal" tabindex="-1" aria-labelledby="statusChangeAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusChangeAdminModalLabel">Confirm Status Change</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="adminStatusChangeMessage" class="alert d-none" role="alert"></div>
                <p>Are you sure you want to change the status of this <span id="adminEntityType" style="font-weight: bold;"></span>?</p>
                <form id="statusChangeAdminForm">
                    <div class="mb-3">
                        <label for="adminPasswordInput" class="form-label">Enter Password to Confirm</label>
                        <input type="password" class="form-control" id="adminPasswordInput" placeholder="Enter your password" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmAdminStatusChangeButton">Confirm</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    let selectedAdminStatusElement = null;
    let previousAdminStatusValue = null; // Store the previous value of the dropdown
    let adminEntityType = ""; // Store the entity type dynamically
    let administratorId = null; // Store the administrator ID dynamically

    // Function to open the modal and store the selected status element
    window.openStatusChangeAdminModal = function (selectElement, type, id, oldStatus) {
        selectedAdminStatusElement = selectElement; // Store the reference to the dropdown
        previousAdminStatusValue = oldStatus; // Store the previous value
        adminEntityType = type; // Set the entity type dynamically
        administratorId = id; // Set the administrator ID dynamically
        const adminEntityTypeElement = document.getElementById("adminEntityType");
        if (adminEntityTypeElement) {
            adminEntityTypeElement.textContent = adminEntityType; // Update the modal text
        }
        const statusChangeAdminModal = new bootstrap.Modal(document.getElementById("statusChangeAdminModal"));
        statusChangeAdminModal.show();
    };

    // Handle the status change confirmation
    const confirmAdminStatusChangeButton = document.getElementById("confirmAdminStatusChangeButton");
    if (confirmAdminStatusChangeButton) {
        confirmAdminStatusChangeButton.addEventListener("click", function () {
            const passwordInput = document.getElementById("adminPasswordInput");
            const enteredPassword = passwordInput.value.trim();
            const messageElement = document.getElementById("adminStatusChangeMessage");

            // Remove any existing messages
            messageElement.classList.add("d-none");

            if (!enteredPassword) {
                messageElement.textContent = "Please enter your password to confirm the status change.";
                messageElement.classList.remove("d-none", "alert-success");
                messageElement.classList.add("alert-danger");
                return;
            }

            // Validate the password with the server - using hardcoded CSRF token from Blade
            fetch('/validate-password', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' // Include CSRF token for security
                },
                body: JSON.stringify({ password: enteredPassword })
            })
            .then(response => response.json())
            .then(data => {
                if (data.valid) {
                    // Update the status in the database
                    fetch(`/admin/administrators/${administratorId}/status`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}' // Include CSRF token for security
                        },
                        body: JSON.stringify({ status: selectedAdminStatusElement.value })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            messageElement.textContent = `Status changed to "${selectedAdminStatusElement.value}" for ${adminEntityType}.`;
                            messageElement.classList.remove("d-none", "alert-danger");
                            messageElement.classList.add("alert-success");

                            // Reset the modal fields
                            passwordInput.value = "";

                            // Refresh the page after a longer delay to show the success message
                            setTimeout(() => {
                                const statusChangeAdminModal = bootstrap.Modal.getInstance(document.getElementById("statusChangeAdminModal"));
                                statusChangeAdminModal.hide();
                                location.reload();
                            }, 3000); // 3 seconds delay
                        } else {
                            messageElement.textContent = "Failed to update status. Please try again.";
                            messageElement.classList.remove("d-none", "alert-success");
                            messageElement.classList.add("alert-danger");
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        messageElement.textContent = "An error occurred. Please try again.";
                        messageElement.classList.remove("d-none", "alert-success");
                        messageElement.classList.add("alert-danger");
                    });
                } else {
                    messageElement.textContent = "Incorrect password. Please try again.";
                    messageElement.classList.remove("d-none", "alert-success");
                    messageElement.classList.add("alert-danger");
                }
            })
            .catch(error => {
                console.error('Error:', error);
                messageElement.textContent = "An error occurred. Please try again.";
                messageElement.classList.remove("d-none", "alert-success");
                messageElement.classList.add("alert-danger");
            });
        });
    }

    // Remove the error message when the user starts to input the password again
    const passwordInput = document.getElementById("adminPasswordInput");
    if (passwordInput) {
        passwordInput.addEventListener("input", function () {
            const messageElement = document.getElementById("adminStatusChangeMessage");
            messageElement.classList.add("d-none");
        });
    }

    // Handle modal cancellation
    const statusChangeAdminModalElement = document.getElementById("statusChangeAdminModal");
    if (statusChangeAdminModalElement) {
        statusChangeAdminModalElement.addEventListener("hidden.bs.modal", function () {
            if (selectedAdminStatusElement && previousAdminStatusValue !== null) {
                // Revert the dropdown to its previous value if the modal is cancelled
                selectedAdminStatusElement.value = previousAdminStatusValue === 'Active' ? 'Active' : 'Inactive';
                location.reload(); // Refresh the page if the modal is cancelled
            }
        });
    }
});
</script>