<?php
session_start();
require 'includes/database.php';

// Get the MySQLi connection
$conn = getDB();

$latitude = isset($_POST['latitude']) ? $_POST['latitude'] : '';
$longitude = isset($_POST['longitude']) ? $_POST['longitude'] : '';

// Check if latitude and longitude are provided
if (!empty($latitude) && !empty($longitude)) {
    // Radius in kilometers
    $radius = 20;

    // Prepare the SQL query with Haversine formula
    $sql = "
        SELECT *, (
            6371 * acos(
                cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) +
                sin(radians(?)) * sin(radians(latitude))
            )
        ) AS distance
        FROM event
        HAVING distance < ?
        ORDER BY start_datetime DESC
    ";

    try {
        // Prepare and execute the query
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Preparation failed: " . $conn->error);
        }

        $stmt->bind_param('dddd', $latitude, $longitude, $latitude, $radius);

        $stmt->execute();
        $result = $stmt->get_result();

        if (!$result) {
            throw new Exception("Error fetching events: " . $conn->error);
        }

        $events = $result->fetch_all(MYSQLI_ASSOC);

    } catch (Exception $e) {
        // Handle any errors
        die($e->getMessage());
    }
} else {
    // If latitude and longitude are not provided, get all events
    $sql = "SELECT * FROM event ORDER BY start_datetime DESC";

    try {
        // Prepare and execute the query
        $result = $conn->query($sql);

        if (!$result) {
            throw new Exception("Error fetching events: " . $conn->error);
        }

        $events = $result->fetch_all(MYSQLI_ASSOC);

    } catch (Exception $e) {
        // Handle any errors
        die($e->getMessage());
    }
}

// Pass search parameters to the next page
$_SESSION['search_events'] = [
    'events' => $events
];

// Redirect to the search results page
header("Location: searchresults.php");
exit;
?>
