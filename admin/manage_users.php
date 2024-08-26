<?php
session_start();
require '../includes/database.php';
require '../header.php'; // Include header

// Get the MySQLi connection
$conn = getDB();

try {
    // Fetch users
    $user_result = $conn->query("SELECT * FROM user WHERE IsActive=1 ORDER BY created_datetime DESC");
    if (!$user_result) {
        throw new Exception("Error fetching users: " . $conn->error);
    }
    $users = $user_result->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    // Handle any errors
    die($e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Users</title>
    <style>
        .toast-container {
    position: fixed;
    top: 1rem; /* Adjust as needed */
    right: 1rem; /* Adjust as needed */
    z-index: 1050;
    color:white;
}
    </style>
</head>
<body>
<div class="container mt-5">

    <!-- Toast Notifications -->
    <div class="toast-container">
        <div id="toastMessage" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-body">
                <!-- Message will be injected here -->
            </div>
        </div>
    </div>

    <h2>Manage Users</h2>

    <?php if (count($users) > 0): ?>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['firstname']); ?></td>
                        <td><?php echo htmlspecialchars($row['lastname']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td>
                            <button type="button" class="btn btn-sm btn-outline-primary edit-btn" data-id="<?php echo $row['id']; ?>">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <a href="#" class="delete-btn btn btn-sm btn-outline-danger" data-id="<?php echo $row['id']; ?>">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="text-center">No users available.</p>
    <?php endif; ?>

</div>


<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editUserForm">
                    <input type="hidden" id="editUserId" name="id">
                    <div class="mb-3">
                        <label for="editUsername" class="form-label">Username</label>
                        <input type="text" class="form-control" id="editUsername" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="editFirstname" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="editFirstname" name="firstname" required>
                    </div>
                    <div class="mb-3">
                        <label for="editLastname" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="editLastname" name="lastname" required>
                    </div>
                    <div class="mb-3">
                        <label for="editEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="editEmail" name="email" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Confirm Delete Modal -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteModalLabel">Confirm Deletion</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this user? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteButton">Delete</button>
            </div>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle edit button clicks
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                const userId = this.getAttribute('data-id');
                
                // Fetch user data
                fetch('getUser.php?id=' + userId)
                    .then(response => response.json())
                    .then(data => {
                        // Populate the modal with user data
                        document.getElementById('editUserId').value = data.id;
                        document.getElementById('editUsername').value = data.username;
                        document.getElementById('editFirstname').value = data.firstname;
                        document.getElementById('editLastname').value = data.lastname;
                        document.getElementById('editEmail').value = data.email;

                        // Show the modal
                        $('#editUserModal').modal('show');
                    })
                    .catch(error => showToast('Error fetching user data', 'danger'));
            });
        });

        // Handle form submission
        document.getElementById('editUserForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('updateUser.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('User updated successfully', 'success');
                    setTimeout(() => location.reload(), 2000);
                } else {
                    showToast('Error updating user', 'danger');
                }
            })
            .catch(error => showToast('Error:', 'danger'));
        });

        var userIdToDelete = null;

        $('.delete-btn').on('click', function(event) {
            event.preventDefault();
            userIdToDelete = $(this).data('id');
            $('#confirmDeleteModal').modal('show');
        });

        $('#confirmDeleteButton').on('click', function() {
            $.ajax({
                url: 'deleteUser.php',
                type: 'GET',
                data: { id: userIdToDelete },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showToast('User deleted successfully', 'success');
                        setTimeout(() => location.reload(), 2000);
                    } else {
                        showToast('Error deleting user', 'danger');
                    }
                },
                error: function() {
                    showToast('Error deleting user', 'danger');
                }
            });
            $('#confirmDeleteModal').modal('hide');
        });

        function showToast(message, type) {
            const toast = document.getElementById('toastMessage');
            toast.querySelector('.toast-body').textContent = message;
            toast.classList.add('bg-' + type);
            const bsToast = new bootstrap.Toast(toast, {
        delay: 5000 // Increase delay to 10 seconds (10000 ms)
    });
            bsToast.show();
        }
    });
</script>

</body>
</html>
