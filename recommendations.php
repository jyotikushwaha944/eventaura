<?php
function fetchBookedEvents($conn, $user_id) {
    $query = "
        SELECT e.id AS event_id, e.category_id
        FROM booking b
        JOIN event e ON b.event_id = e.id
        WHERE b.user_id = ? AND b.status = 'booked'
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $bookedEvents = [];
    while ($row = $result->fetch_assoc()) {
        $bookedEvents[] = $row;
    }
    $stmt->close();
    return $bookedEvents;
};


function getBookedCategoryIds($conn, $bookedEvents)
{
    // Extract category IDs from booked events
    $bookedCategories = [];
    foreach ($bookedEvents as $event) {
        $bookedCategories[] = $event['category_id'];
    }
    return $bookedCategories;
}

function fetchAllUpcomingEvents($conn){
    $query = "
    SELECT e.id, e.name, e.description, e.start_datetime, e.end_datetime, e.venue, e.category_id
    FROM event e
    WHERE e.start_datetime > NOW()
    ";
    $result = $conn->query($query);
    $allEvents = [];
    while ($row = $result->fetch_assoc()) {
    $allEvents[] = $row;
    }
    return $allEvents;
}

function findRecommendedEvents($conn, $user_id){
    $bookedEvents = fetchBookedEvents($conn, $user_id);
    $allEvents = fetchAllUpcomingEvents($conn);
    $bookedCategories = getBookedCategoryIds($conn, $bookedEvents);

    $recommendedEvents = [];
    foreach ($allEvents as $event) {
        // Only recommend events from categories of the user's booked events
        if (in_array($event['category_id'], $bookedCategories)) {
            // Check if the user has already booked this event
            $query = "SELECT COUNT(*) FROM booking WHERE user_id = ? AND event_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('ii', $user_id, $event['id']);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();
    
            // If the user has not booked this event, add to recommendations
            if ($count == 0) {
                $recommendedEvents[] = $event;
            }
        }
    }

    
    usort($recommendedEvents, function($a, $b) {
        return strtotime($a['start_datetime']) - strtotime($b['start_datetime']);
    });
    return $recommendedEvents;
}
?>
