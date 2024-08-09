<?php
include 'database.php';

$id = (int)$_GET['id'];
$sql = "DELETE FROM events WHERE id='$id'";

if ($conn->query($sql) === TRUE) {
    header('Location: events.php');
    exit();
} else {
    echo "Error: " . $conn->error;
}
?>
