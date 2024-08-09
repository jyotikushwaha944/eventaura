<?php

require_once 'includes/database.php';


$conn = getDB();

// Fetch event types
$type_sql = "SELECT * FROM event_types";
$type_result = $conn->query($type_sql);
?>

<footer class="footer mt-auto py-3 bg-light">
    <div class="container">
        <div class="row">
            <?php if ($type_result->num_rows > 0): ?>
                <?php while($type_row = $type_result->fetch_assoc()): ?>
                    <div class="col-12 col-md-3 event-type text-center">
                        <img src="path_to_icons/<?php echo htmlspecialchars($type_row['icon']); ?>" alt="<?php echo htmlspecialchars($type_row['type_name']); ?>" style="width:50px;height:50px;">
                        <p><?php echo htmlspecialchars($type_row['type_name']); ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No event types available</p>
            <?php endif; ?>
        </div>
    </div>
</footer>

<?php $conn->close(); ?>
