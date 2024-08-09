<?php
session_start();
require '../includes/database.php';
require '../includes/header.php'; // Include header

// Ensure the user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['usertype_id'] != 1) {
    header('Location: ../login.php');
    exit();
}

// Handle delete request
if (isset($_GET['delete'])) {
    $event_id = (int)$_GET['delete'];
    $query = "DELETE FROM event WHERE id = ?";
    $stmt = $conn->prepare($query);
    if ($stmt->execute([$event_id])) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to delete event.']);
    }
    exit();
}

// Fetch events
$query = "SELECT * FROM event";
$stmt = $conn->query($query);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Events</title>
    <link rel="stylesheet" href="../includes/bootstrap.min.css">
    <script src="../includes/jquery.min.js"></script>
    <script src="../includes/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="container mt-4">
        <h1>Manage Events</h1>
        <a href="edit_event.php" class="btn btn-primary mb-3">Add New Event</a>
        <table class="table">
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
                    <td><?php echo htmlspecialchars($event['venue']); ?></td>
                    <td><?php echo htmlspecialchars($event['start_datetime']); ?></td>
                    <td><?php echo htmlspecialchars($event['end_datetime']); ?></td>
                    <td>
                        <a href="edit_event.php?id=<?php echo $event['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                        <button class="btn btn-danger btn-sm delete-btn" data-id="<?php echo $event['id']; ?>">Delete</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
    $(document).ready(function() {
        $('.delete-btn').on('click', function() {
            var eventId = $(this).data('id');
            if (confirm('Are you sure you want to delete this event?')) {
                $.ajax({
                    url: 'manage_events.php',
                    type: 'GET',
                    data: { delete: eventId },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            alert('Error: ' + response.error);
                        }
                    }
                });
            }
        });
    });
    </script>
</body>
</html>
