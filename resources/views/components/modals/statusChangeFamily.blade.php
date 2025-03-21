
<div class="modal fade" id="statusChangeFamilyModal" tabindex="-1" aria-labelledby="statusChangeFamilyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusChangeFamilyModalLabel">Confirm Access Change</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="familyStatusChangeMessage" class="alert d-none" role="alert"></div>
                <p>Are you sure you want to change the access of this <span id="familyEntityType" style="font-weight: bold;"></span>?</p>
                <form id="statusChangeFamilyForm">
                    <div class="mb-3">
                        <label for="familyPasswordInput" class="form-label">Enter Password to Confirm</label>
                        <input type="password" class="form-control" id="familyPasswordInput" placeholder="Enter your password" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmFamilyStatusChangeButton">Confirm</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    let selectedFamilyStatusElement = null;
    let previousFamilyStatusValue = null; // Store the previous value of the dropdown
    let familyEntityType = ""; // Store the entity type dynamically

    // Function to open the modal and store the selected status element
    window.openFamilyStatusChangeModal = function (selectElement, type) {
        selectedFamilyStatusElement = selectElement; // Store the reference to the dropdown
        previousFamilyStatusValue = selectElement.value; // Store the previous value
        familyEntityType = type; // Set the entity type dynamically
        const familyEntityTypeElement = document.getElementById("familyEntityType");
        if (familyEntityTypeElement) {
            familyEntityTypeElement.textContent = familyEntityType; // Update the modal text
        }
        const statusChangeFamilyModal = new bootstrap.Modal(document.getElementById("statusChangeFamilyModal"));
        statusChangeFamilyModal.show();
    };

    // Handle the status change confirmation
    const confirmFamilyStatusChangeButton = document.getElementById("confirmFamilyStatusChangeButton");
    if (confirmFamilyStatusChangeButton) {
        confirmFamilyStatusChangeButton.addEventListener("click", function () {
            const passwordInput = document.getElementById("familyPasswordInput");
            const enteredPassword = passwordInput.value.trim();
            const messageElement = document.getElementById("familyStatusChangeMessage");

            // Remove any existing messages
            messageElement.classList.add("d-none");

            if (!enteredPassword) {
                messageElement.textContent = "Please enter your password to confirm the access change.";
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
                    const familyMemberId = selectedFamilyStatusElement.dataset.id;
                    fetch(`/admin/family-members/${familyMemberId}/status`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}' // Include CSRF token for security
                        },
                        body: JSON.stringify({ status: selectedFamilyStatusElement.value })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            messageElement.textContent = `Access changed to "${selectedFamilyStatusElement.value}" for ${familyEntityType}.`;
                            messageElement.classList.remove("d-none", "alert-danger");
                            messageElement.classList.add("alert-success");

                            // Reset the modal fields
                            passwordInput.value = "";

                            // Refresh the page after a longer delay to show the success message
                            setTimeout(() => {
                                const statusChangeFamilyModal = bootstrap.Modal.getInstance(document.getElementById("statusChangeFamilyModal"));
                                statusChangeFamilyModal.hide();
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
    const passwordInput = document.getElementById("familyPasswordInput");
    if (passwordInput) {
        passwordInput.addEventListener("input", function () {
            const messageElement = document.getElementById("familyStatusChangeMessage");
            messageElement.classList.add("d-none");
        });
    }

    // Handle modal cancellation
    const statusChangeFamilyModalElement = document.getElementById("statusChangeFamilyModal");
    if (statusChangeFamilyModalElement) {
        statusChangeFamilyModalElement.addEventListener("hidden.bs.modal", function () {
            if (selectedFamilyStatusElement && previousFamilyStatusValue !== null) {
                // Revert the dropdown to its previous value if the modal is cancelled
                selectedFamilyStatusElement.value = previousFamilyStatusValue;
                location.reload(); // Refresh the page if the modal is cancelled
            }
        });
    }
});
</script>