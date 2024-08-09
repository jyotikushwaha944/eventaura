<?php
session_start();
require 'includes/database.php'; // Ensure this file correctly establishes the $conn variable

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$conn = getDB();
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Initialize variables
$title = $description = $event_datetime_start = $event_datetime_end = $location = $category = '';

// Fetch event categories
try {
    $categoryQuery = "SELECT id, category_name FROM event_category"; // Adjust table and column names as needed
    $categoryResult = $conn->query($categoryQuery);
    if (!$categoryResult) {
        throw new Exception("Error fetching event categories: " . $conn->error);
    }
    $categories = $categoryResult->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    die($e->getMessage());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $title = $_GET['title'] ?? '';
    $description = $_GET['description'] ?? '';
    $event_datetime_start = $_GET['event_datetime_start'] ?? '';
    $event_datetime_end = $_GET['event_datetime_end'] ?? '';
    $location = $_GET['location'] ?? '';
    $category = $_GET['category'] ?? '';

    try {
        $sql = "SELECT * FROM events WHERE 1=1";
        
        // Add search conditions
        $params = [];
        $types = '';

        if (!empty($title)) {
            $sql .= " AND title LIKE ?";
            $params[] = "%$title%";
            $types .= 's';
        }
        if (!empty($description)) {
            $sql .= " AND description LIKE ?";
            $params[] = "%$description%";
            $types .= 's';
        }
        if (!empty($event_datetime_start) && !empty($event_datetime_end)) {
            $sql .= " AND event_datetime BETWEEN ? AND ?";
            $params[] = $event_datetime_start;
            $params[] = $event_datetime_end;
            $types .= 'ss';
        }
        if (!empty($location)) {
            $sql .= " AND location LIKE ?";
            $params[] = "%$location%";
            $types .= 's';
        }
        if (!empty($category)) {
            $sql .= " AND category_id = ?";
            $params[] = $category;
            $types .= 'i';
        }

        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            throw new Exception("Error preparing statement: " . $conn->error);
        }

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result === false) {
            throw new Exception("Error executing query: " . $stmt->error);
        }

        $events = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Events</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Find Events</h2>

        <form action="" method="GET">
            <div class="form-row mb-3">
                <div class="col-md-3">
                    <input type="text" name="title" class="form-control" placeholder="Event Title" value="<?php echo htmlspecialchars($title); ?>">
                </div>
                <div class="col-md-3">
                    <input type="text" name="description" class="form-control" placeholder="Description" value="<?php echo htmlspecialchars($description); ?>">
                </div>
                <div class="col-md-3">
                    <input type="datetime-local" name="event_datetime_start" class="form-control" placeholder="Start Date" value="<?php echo htmlspecialchars($event_datetime_start); ?>">
                </div>
                <div class="col-md-3">
                    <input type="datetime-local" name="event_datetime_end" class="form-control" placeholder="End Date" value="<?php echo htmlspecialchars($event_datetime_end); ?>">
                </div>
            </div>
            <div class="form-group">
                <input type="text" name="location" class="form-control" placeholder="Location" value="<?php echo htmlspecialchars($location); ?>">
            </div>
            <div class="form-group">
                <label for="category">Event Category</label>
                <select id="category" name="category" class="form-control">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat['id']); ?>" <?php echo ($category == $cat['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['category_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>

        <?php if (isset($events) && !empty($events)): ?>
            <div class="mt-4">
                <h3>Results</h3>
                <div class="list-group">
                    <?php foreach ($events as $event): ?>
                        <a href="#" class="list-group-item list-group-item-action">
                            <h5 class="mb-1"><?php echo htmlspecialchars($event['title']); ?></h5>
                            <p class="mb-1"><?php echo htmlspecialchars($event['description']); ?></p>
                            <small>Date: <?php echo htmlspecialchars($event['event_datetime']); ?></small><br>
                            <small>Location: <?php echo htmlspecialchars($event['location']); ?></small>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php elseif (isset($events)): ?>
            <p class="mt-4">No events found.</p>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
