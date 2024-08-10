<?php
session_start();
require '../includes/database.php';

header('Content-Type: application/json');

$conn = getDB();

// Check if ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid user ID']);
    exit();
}

$id = $_GET['id'];

// Prepare and execute the delete query
$query = $conn->prepare("DELETE FROM user WHERE id = ?");
$query->bind_param('i', $id);

if ($query->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $conn->error]);
}
?>
