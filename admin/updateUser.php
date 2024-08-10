<?php
session_start();
require '../includes/database.php';

header('Content-Type: application/json');

$conn = getDB();

$id = $_POST['id'];
$username = $_POST['username'];
$firstname = $_POST['firstname'];
$lastname = $_POST['lastname'];
$email = $_POST['email'];

$query = $conn->prepare("UPDATE user SET username = ?, firstname = ?, lastname = ?, email = ? WHERE id = ?");
$query->bind_param('ssssi', $username, $firstname, $lastname, $email, $id);

if ($query->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $conn->error]);
}
?>
