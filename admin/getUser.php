<?php
session_start();
require '../includes/database.php';

header('Content-Type: application/json');

$conn = getDB();

$id = $_GET['id'];
$query = $conn->prepare("SELECT * FROM user WHERE id = ?");
$query->bind_param('i', $id);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();

echo json_encode($user);
?>
