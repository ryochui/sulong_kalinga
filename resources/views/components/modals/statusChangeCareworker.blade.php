<div class="modal fade" id="statusChangeCareworkerModal" tabindex="-1" aria-labelledby="statusChangeCareworkerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusChangeCareworkerModalLabel">Confirm Status Change</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="careworkerStatusChangeMessage" class="alert d-none" role="alert"></div>
                <p>Are you sure you want to change the status of this <span id="careworkerEntityType" style="font-weight: bold;"></span>?</p>
                <form id="statusChangeCareworkerForm">
                    <div class="mb-3">
                        <label for="careworkerPasswordInput" class="form-label">Enter Password to Confirm</label>
                        <input type="password" class="form-control" id="careworkerPasswordInput" placeholder="Enter your password" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmCareworkerStatusChangeButton">Confirm</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    let selectedCareworkerStatusElement = null;
    let previousCareworkerStatusValue = null; // Store the previous value of the dropdown
    let careworkerEntityType = ""; // Store the entity type dynamically
    let careworkerId = null; // Store the careworker ID dynamically

    // Function to open the modal and store the selected status element
    window.openStatusChangeCareworkerModal = function (selectElement, type, id, oldStatus) {
        selectedCareworkerStatusElement = selectElement; // Store the reference to the dropdown
        previousCareworkerStatusValue = oldStatus; // Store the previous value
        careworkerEntityType = type; // Set the entity type dynamically
        careworkerId = id; // Set the careworker ID dynamically
        const careworkerEntityTypeElement = document.getElementById("careworkerEntityType");
        if (careworkerEntityTypeElement) {
            careworkerEntityTypeElement.textContent = careworkerEntityType; // Update the modal text
        }
        const statusChangeCareworkerModal = new bootstrap.Modal(document.getElementById("statusChangeCareworkerModal"));
        statusChangeCareworkerModal.show();
    };

    // Handle the status change confirmation
    const confirmCareworkerStatusChangeButton = document.getElementById("confirmCareworkerStatusChangeButton");
    if (confirmCareworkerStatusChangeButton) {
        confirmCareworkerStatusChangeButton.addEventListener("click", function () {
            const passwordInput = document.getElementById("careworkerPasswordInput");
            const enteredPassword = passwordInput.value.trim();
            const messageElement = document.getElementById("careworkerStatusChangeMessage");

            // Remove any existing messages
            messageElement.classList.add("d-none");

            if (!enteredPassword) {
                messageElement.textContent = "Please enter your password to confirm the status change.";
                messageElement.classList.remove("d-none", "alert-success");
                messageElement.classList.add("alert-danger");
                return;
            }

            // Validate the password with the server
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
                    fetch(`/admin/careworkers/${careworkerId}/status`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}' // Include CSRF token for security
                        },
                        body: JSON.stringify({ status: selectedCareworkerStatusElement.value })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            messageElement.textContent = `Status changed to "${selectedCareworkerStatusElement.value}" for ${careworkerEntityType}.`;
                            messageElement.classList.remove("d-none", "alert-danger");
                            messageElement.classList.add("alert-success");

                            // Reset the modal fields
                            passwordInput.value = "";

                            // Refresh the page after a longer delay to show the success message
                            setTimeout(() => {
                                const statusChangeCareworkerModal = bootstrap.Modal.getInstance(document.getElementById("statusChangeCareworkerModal"));
                                statusChangeCareworkerModal.hide();
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
    const passwordInput = document.getElementById("careworkerPasswordInput");
    if (passwordInput) {
        passwordInput.addEventListener("input", function () {
            const messageElement = document.getElementById("careworkerStatusChangeMessage");
            messageElement.classList.add("d-none");
        });
    }

    // Handle modal cancellation
    const statusChangeCareworkerModalElement = document.getElementById("statusChangeCareworkerModal");
    if (statusChangeCareworkerModalElement) {
        statusChangeCareworkerModalElement.addEventListener("hidden.bs.modal", function () {
            if (selectedCareworkerStatusElement && previousCareworkerStatusValue !== null) {
                // Revert the dropdown to its previous value if the modal is cancelled
                selectedCareworkerStatusElement.value = previousCareworkerStatusValue === 'Active' ? 'active' : 'inactive';
                location.reload(); // Refresh the page if the modal is cancelled
            }
        });
    }
});
</script>