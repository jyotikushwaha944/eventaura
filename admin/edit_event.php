<?php
session_start();
require '../includes/database.php';
require '../includes/header.php';

// Ensure the user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['usertype_id'] != 1) {
    header('Location: ../login.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = (int)$_POST['event_id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $venue = $_POST['venue'];
    $start_datetime = $_POST['start_datetime'];
    $end_datetime = $_POST['end_datetime'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];

    $query = "UPDATE event SET name = ?, description = ?, venue = ?, start_datetime = ?, end_datetime = ?, price = ?, category_id = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    if ($stmt->execute([$name, $description, $venue, $start_datetime, $end_datetime, $price, $category_id, $event_id])) {
        header('Location: manage_events.php');
        exit();
    } else {
        $error = 'Failed to update event.';
    }
}

// Fetch event details
$event_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$query = "SELECT * FROM event WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$event_id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch categories
$query = "SELECT * FROM category";
$stmt = $conn->query($query);
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Event</title>
    <link rel="stylesheet" href="../includes/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h1>Edit Event</h1>
        <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="hidden" name="event_id" value="<?php echo htmlspecialchars($event['id']); ?>">
            <div class="mb-3">
                <label for="name" class="form-label">Event Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($event['name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" required><?php echo htmlspecialchars($event['description']); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="venue" class="form-label">Venue</label>
                <input type="text" class="form-control" id="venue" name="venue" value="<?php echo htmlspecialchars($event['venue']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="start_datetime" class="form-label">Start Date & Time</label>
                <input type="datetime-local" class="form-control" id="start_datetime" name="start_datetime" value="<?php echo htmlspecialchars(date('Y-m-d\TH:i', strtotime($event['start_datetime']))); ?>" required>
            </div>
            <div class="mb-3">
                <label for="end_datetime" class="form-label">End Date & Time</label>
                <input type="datetime-local" class="form-control" id="end_datetime" name="end_datetime" value="<?php echo htmlspecialchars(date('Y-m-d\TH:i', strtotime($event['end_datetime']))); ?>" required>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?php echo htmlspecialchars($event['price']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="category_id" class="form-label">Category</label>
                <select class="form-select" id="category_id" name="category_id" required>
                    <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['id']; ?>" <?php echo $category['id'] == $event['category_id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($category['name']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update Event</button>
        </form>
    </div>
</body>
</html>
