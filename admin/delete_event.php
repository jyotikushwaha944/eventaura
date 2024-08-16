<?php
include '../includes/database.php'; // Include the correct path for the database connection

$id = (int)$_GET['id'];
$conn = getDB(); // Get the database connection

// Prepare and execute the update query
$sql = "UPDATE event SET IsActive = 0 WHERE id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to update event status.']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to prepare statement.']);
}
$conn->close();
?>
