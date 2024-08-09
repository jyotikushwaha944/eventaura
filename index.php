<?php
session_start();
require 'includes/database.php';
require 'header.php'; // Include header
require 'matrix_factorization.php';
// require 'recommendations.php';


// Get the MySQLi connection
$conn = getDB();

try {
    // Fetch categories
    $category_result = $conn->query("SELECT id, name FROM category");
    if (!$category_result) {
        throw new Exception("Error fetching categories: " . $conn->error);
    }
    $categories = $category_result->fetch_all(MYSQLI_ASSOC);

    // Fetch events based on the selected category if it exists
    $category_id = isset($_POST['event_type']) ? (int)$_POST['event_type'] : 0;
    $query = "SELECT * FROM event";
    if ($category_id > 0) {
        $query .= " WHERE category_id = " . $category_id;
    }
    $query .= " ORDER BY created_datetime DESC";

    $result = $conn->query($query);
    if (!$result) {
        throw new Exception("Error fetching events: " . $conn->error);
    }
    $events = $result->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    // Handle any errors
    die($e->getMessage());
}

function fetchUserEventMatrix($conn) {
    // SQL query to fetch booking data
    $sql = "SELECT user_id, event_id, num_tickets FROM booking";

    // Execute the query and check for errors
    $result = $conn->query($sql);
    if (!$result) {
        die("Query failed: " . $conn->error);
    }

    $userEventMatrix = [];

    // Fetch all booking data
    while ($row = $result->fetch_assoc()) {
        $user_id = $row['user_id'];
        $event_id = $row['event_id'];
        $num_tickets = $row['num_tickets'];

        // Initialize the user-event matrix if not already initialized
        if (!isset($userEventMatrix[$user_id])) {
            $userEventMatrix[$user_id] = [];
        }

        // Set or update the number of tickets for the event
        if (!isset($userEventMatrix[$user_id][$event_id])) {
            $userEventMatrix[$user_id][$event_id] = 0;
        }
        $userEventMatrix[$user_id][$event_id] += $num_tickets;
    }

    return $userEventMatrix;
}

$userEventMatrix = fetchUserEventMatrix($conn);

echo 'User Event Matrix'. print_r($userEventMatrix);


function getRecommendations($nR, $userId) {
    $userIndex = $userId - 1; // Adjust for zero-based indexing
    arsort($nR[$userIndex]); // Sort the predicted ratings in descending order

    $recommendations = array_keys($nR[$userIndex]);
    return $recommendations;
}

function fetchEventDetails($conn, $eventIds) {
    if (empty($eventIds)) {
        return [];
    }
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


// echo "Predicted Ratings (nR): " . print_r($nR, true);
// echo "Matrix P: " . print_r($P, true);
// echo "Matrix Q: " . print_r($Q, true);


$userId = 3; // Example user ID, this should come from session or request

$recommendations = getRecommendations($nR, $userId);
// echo "Recommendations: " . print_r($recommendations, true);

$recommendedEvents = fetchEventDetails($conn, $recommendations);
// echo "Number of recommended events: " . count($recommendedEvents);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventaura</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card {
            height: 400px; /* Fixed height for the card */
            display: flex;
            flex-direction: column;
            overflow: hidden; /* Ensure contents do not overflow the card */
        }

        .card-img-wrapper {
            height: 200px; /* Fixed height for the image wrapper */
            overflow: hidden; /* Ensure image does not overflow */
        }

        .card-img {
            width: 100%;
            height: 100%; /* Cover the height of the wrapper */
            object-fit: cover; /* Ensure image covers the area without distortion */
        }

        .card-body {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 0.75rem;
            height: 200px;
            overflow-y: auto; /* Add vertical scrollbar if content overflows */
        }

        .card-body::-webkit-scrollbar {
            width: 8px; /* Thin scrollbar */
        }

        .card-body::-webkit-scrollbar-thumb {
            background: #888; /* Scrollbar color */
            border-radius: 4px; /* Rounded scrollbar */
        }

        .card-body::-webkit-scrollbar-thumb:hover {
            background: #555; /* Darker scrollbar on hover */
        }

        .card-title, .card-text {
            margin-bottom: 1px; /* Remove extra spacing between text elements */
        }

        .card-body .btn {
            margin-top: auto; /* Push the button to the bottom of the card body */
        }

        .alert-info {
            font-size: 0.875rem; /* Smaller font size for alerts */
        }

        .form-control-sm {
            font-size: 0.875rem; /* Smaller font size for select dropdown */
        }

        .btn-primary {
            font-size: 0.875rem; /* Smaller font size for buttons */
        }

        .event-details {
            font-size: 0.875rem; /* Smaller font size for event details */
        }

        .event-details strong {
            font-weight: bold; /* Bold text for labels */
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <!-- No Upcoming Events text -->
        </div>
        <div class="col-md-4 d-flex align-items-center justify-content-end">
            <form action="index.php" method="POST" class="d-flex">
                <div class="form-group mb-0 mr-2">
                    <select id="event_type" name="event_type" class="form-control form-control-sm">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo htmlspecialchars($category['id']); ?>" <?php echo (isset($_POST['event_type']) && $_POST['event_type'] == $category['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">Find Events</button>
            </form>
        </div>
    </div>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-info text-center"><?php echo htmlspecialchars($_SESSION['message']); ?></div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <?php if (empty($events)): ?>
        <div class="alert alert-warning text-center">No events found.</div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($events as $event): ?>
                <div class="col-md-4 mb-4">
                    <div class="card border-primary">
                        <a href="event_detail.php?event_id=<?php echo htmlspecialchars($event['id']); ?>" class="text-decoration-none">
                            <div class="card-img-wrapper">
                                <?php if (!empty($event['image_small'])): ?>
                                    <img src="<?php echo htmlspecialchars($event['image_small']); ?>" class="card-img" alt="Event Image">
                                <?php else: ?>
                                    <img src="https://via.placeholder.com/350x200?text=Event+Image" class="card-img" alt="Placeholder Image">
                                <?php endif; ?>
                            </div>
                            <div class="card-body event-details">
                                <h5 class="card-title" title="<?php echo htmlspecialchars($event['name']); ?>"><?php echo htmlspecialchars($event['name']); ?></h5>
                                <p class="card-text"><small class="text-muted"><strong>Start DateTime:</strong> <?php echo htmlspecialchars($event['start_datetime']); ?></small></p>
                                <p class="card-text"><small class="text-muted"><strong>End DateTime:</strong> <?php echo htmlspecialchars($event['end_datetime']); ?></small></p>
                                <p class="card-text"><small class="text-muted"><strong>Location:</strong> <?php echo htmlspecialchars($event['venue']); ?></small></p>
                                <p class="card-text"><small class="text-muted"><strong>Price:</strong> <?php echo htmlspecialchars($event['price']); ?></small></p>
                                <p class="card-text"><small class="text-muted"><strong>Description:</strong> <?php echo htmlspecialchars($event['description']); ?></small></p>
                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <a href="registerTicket.php?event_id=<?php echo htmlspecialchars($event['id']); ?>" class="btn btn-primary btn-sm">Register</a>
                                <?php endif; ?>
                            </div>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<div class="container">
    <h2>Recommended Events</h2>
    <div class="row">
        <?php foreach ($recommendedEvents as $event): ?>
            <div class="col-md-4">
                <div class="card mb-4">
                    <img src="<?php echo $event['image_small']; ?>" class="card-img-top" alt="<?php echo $event['name']; ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $event['name']; ?></h5>
                        <p class="card-text"><?php echo $event['description']; ?></p>
                        <a href="event_details.php?id=<?php echo $event['id']; ?>" class="btn btn-primary">View Details</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
