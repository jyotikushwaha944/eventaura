<?php
include 'matrix_factorization.php';
require 'includes/database.php';

// Get the MySQLi connection
$conn = getDB();

function fetchUserEventMatrix() {
    $sql = "SELECT user_id, event_id, num_tickets FROM booking";
    $result = $conn->query($sql);

    $userEventMatrix = [];
    while ($row = $result->fetch_assoc()) {
        $userEventMatrix[$row['user_id']][$row['event_id']] = $row['num_tickets'];
    }

    return $userEventMatrix;
}

$userEventMatrix = fetchUserEventMatrix();

function getRecommendations($nR, $userId) {
    $userIndex = $userId - 1; // Adjust for zero-based indexing
    arsort($nR[$userIndex]); // Sort the predicted ratings in descending order

    $recommendations = array_keys($nR[$userIndex]);
    return $recommendations;
}

function fetchEventDetails($eventIds) {
    $ids = implode(",", $eventIds);
    $sql = "SELECT * FROM event WHERE id IN ($ids)";
    $result = $conn->query($sql);

    $events = [];
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }

    return $events;
}

$K = 2; // Number of latent factors
$R = $userEventMatrix; // The user-event matrix
list($nR, $P, $Q) = matrixFactorization($R, $K);

$userId = 1; // Example user ID, this should come from session or request
$recommendations = getRecommendations($nR, $userId);
$recommendedEvents = fetchEventDetails($recommendations);
?>
