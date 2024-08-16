<?php
session_start();
require '../includes/database.php';
require '../header.php'; // Include header

// Ensure the user is an admin
if (!isset($_SESSION['userid']) || $_SESSION['usertype_id'] != 3) {
    header('Location: ../login.php');
    exit();
}

$conn = getDB(); // Assuming getDB() returns a MySQLi connection

// Fetch events where IsActive is true (1)
$query = "SELECT * FROM event WHERE IsActive = 1";
$result = $conn->query($query);

$events = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Events</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #f0f8ff; /* Light blue background */
        }
        h1 {
            color: #004080; /* Dark blue for the header */
        }
        .table {
            background-color: #ffffff; /* White table background */
        }
        .table thead th {
            background-color: #007bff; /* Bootstrap primary color */
            color: white;
        }
        .table td {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .action-links a {
            margin-right: 10px;
            color: #007bff; /* Bootstrap primary color */
        }
        .action-links a:hover {
            color: #0056b3; /* Darker blue on hover */
        }
        .fa-edit, .fa-trash {
            color: #007bff; /* Bootstrap primary color */
        }
        .fa-edit:hover, .fa-trash:hover {
            color: #0056b3; /* Darker blue on hover */
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h1>Manage Events</h1>
        <a href="edit_event.php" class="btn btn-primary mb-3">Add New Event</a>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Venue</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($events as $event): ?>
                <tr>
                    <td><?php echo htmlspecialchars($event['id']); ?></td>
                    <td><?php echo htmlspecialchars($event['name']); ?></td>
                    <td title="<?php echo htmlspecialchars($event['venue']); ?>"><?php echo htmlspecialchars($event['venue']); ?></td>
                    <td><?php echo htmlspecialchars($event['start_datetime']); ?></td>
                    <td><?php echo htmlspecialchars($event['end_datetime']); ?></td>
                    <td class="action-links">
                        <a href="edit_event.php?id=<?php echo $event['id']; ?>" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="#" class="delete-btn" data-id="<?php echo $event['id']; ?>" title="Delete">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Bootstrap Modal -->
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
                    Are you sure you want to delete this event? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteButton">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        var eventIdToDelete = null;

        $('.delete-btn').on('click', function(event) {
            event.preventDefault();
            eventIdToDelete = $(this).data('id');
            $('#confirmDeleteModal').modal('show');
        });

        $('#confirmDeleteButton').on('click', function() {
            $.ajax({
                url: 'delete_event.php',
                type: 'GET',
                data: { id: eventIdToDelete },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + response.error);
                    }
                }
            });
            $('#confirmDeleteModal').modal('hide');
        });
    });
    </script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>
