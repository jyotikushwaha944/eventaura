<?php
session_start();
require 'includes/database.php';

$conn = getDB();

$search_query = isset($_POST['search_query']) ? $_POST['search_query'] : '';

try {
    // Prepare and execute the query
    $stmt = $conn->prepare("
        SELECT e.*, c.name AS category_name
        FROM event e
        LEFT JOIN category c ON e.category_id = c.id
        WHERE (e.name LIKE ? 
               OR e.venue LIKE ? 
               OR e.description LIKE ? 
               OR c.name LIKE ?)
          AND e.IsActive = 1
          AND e.start_datetime IS NOT NULL
          AND e.end_datetime IS NOT NULL
          AND e.start_datetime >= NOW()
        ORDER BY e.start_datetime DESC
    ");

    $search_query = '%' . $search_query . '%';
    $stmt->bind_param('ssss', $search_query, $search_query, $search_query, $search_query); // Fixed number of parameters
    $stmt->execute();
    $result = $stmt->get_result();
    
    if (!$result) {
        throw new Exception("Error fetching events: " . $conn->error);
    }
    
    $events = $result->fetch_all(MYSQLI_ASSOC);
    
    // Pass search parameters to the next page
    $_SESSION['search_events'] = [
        'events' => $events
    ];
    
    header("Location: searchresults.php");
    exit;
} catch (Exception $e) {
    // Handle any errors
    die($e->getMessage());
}
?>
