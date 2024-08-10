<?php
session_start();
require 'includes/database.php';
require 'header.php'; // Include header

// Get the MySQLi connection
$conn = getDB();

try {
    // Prepare and execute the query to fetch all events
    $result = $conn->query("SELECT * FROM events ORDER BY created_datetime DESC");
    if (!$result) {
        throw new Exception("Error fetching events: " . $conn->error);
    }
    $events = $result->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    // Handle any errors
    die($e->getMessage());
}
?>

<div class="container mt-5">
    <h2 class="mb-4 text-center">Browse Events</h2>
    <div class="row">
        <?php if (empty($events)): ?>
            <div class="col-12">
                <div class="alert alert-warning text-center">No events found.</div>
            </div>
        <?php else: ?>
            <?php foreach ($events as $event): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <img src="path_to_images/<?php echo htmlspecialchars($event['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($event['name']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($event['name']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($event['description']); ?></p>
                            <p class="card-text"><small class="text-muted">Start: <?php echo htmlspecialchars($event['start_datetime']); ?></small></p>
                            <p class="card-text"><small class="text-muted">End: <?php echo htmlspecialchars($event['end_datetime']); ?></small></p>
                            <p class="card-text"><small class="text-muted">Location: <?php echo htmlspecialchars($event['venue']); ?></small></p>
                            <p class="card-text"><small class="text-muted">Price: $<?php echo htmlspecialchars($event['price']); ?></small></p>
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <a href="registerTicket.php?event_id=<?php echo htmlspecialchars($event['id']); ?>" class="btn btn-primary">Register</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
