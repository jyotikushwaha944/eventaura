<?php
session_start();
require '../includes/database.php';
require '../header.php'; // Include header

// Ensure the user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['usertype_id'] != 1) {
    header('Location: ../login.php');
    exit();
}

// Handle add/edit/delete venues
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $name = $_POST['name'];
    $address = $_POST['address'];
    $capacity = $_POST['capacity'];
    $contact_info = $_POST['contact_info'];
    $facilities = $_POST['facilities'];
    $description = $_POST['description'];
    $images = $_POST['images'];

    if ($id) {
        // Update venue
        $stmt = $conn->prepare("UPDATE venues SET name=?, address=?, capacity=?, contact_info=?, facilities=?, description=?, images=? WHERE id=?");
        $stmt->bind_param("ssissssi", $name, $address, $capacity, $contact_info, $facilities, $description, $images, $id);
    } else {
        // Add new venue
        $stmt = $conn->prepare("INSERT INTO venues (name, address, capacity, contact_info, facilities, description, images) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssissss", $name, $address, $capacity, $contact_info, $facilities, $description, $images);
    }
    $stmt->execute();
}

// Handle delete request
if (isset($_GET['delete'])) {
    $venue_id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM venues WHERE id = ?");
    $stmt->bind_param("i", $venue_id);
    $stmt->execute();
}

// Fetch venues
$result = $conn->query("SELECT * FROM venues");
$venues = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Venues</title>
    <link rel="stylesheet" href="../includes/bootstrap.min.css">
    <script src="../includes/jquery.min.js"></script>
    <script src="../includes/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="container mt-4">
        <h1>Manage Venues</h1>
        <form method="post" action="manage_venues.php">
            <input type="hidden" name="id" id="venue_id">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" class="form-control" id="address" name="address" required>
            </div>
            <div class="form-group">
                <label for="capacity">Capacity</label>
                <input type="number" class="form-control" id="capacity" name="capacity" required>
            </div>
            <div class="form-group">
                <label for="contact_info">Contact Info</label>
                <input type="text" class="form-control" id="contact_info" name="contact_info">
            </div>
            <div class="form-group">
                <label for="facilities">Facilities</label>
                <textarea class="form-control" id="facilities" name="facilities"></textarea>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description"></textarea>
            </div>
            <div class="form-group">
                <label for="images">Images (comma-separated URLs)</label>
                <input type="text" class="form-control" id="images" name="images">
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>

        <h2>Existing Venues</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Capacity</th>
                    <th>Contact Info</th>
                    <th>Facilities</th>
                    <th>Description</th>
                    <th>Images</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($venues as $venue): ?>
                <tr>
                    <td><?= htmlspecialchars($venue['name']) ?></td>
                    <td><?= htmlspecialchars($venue['address']) ?></td>
                    <td><?= htmlspecialchars($venue['capacity']) ?></td>
                    <td><?= htmlspecialchars($venue['contact_info']) ?></td>
                    <td><?= htmlspecialchars($venue['facilities']) ?></td>
                    <td><?= htmlspecialchars($venue['description']) ?></td>
                    <td><?= htmlspecialchars($venue['images']) ?></td>
                    <td>
                        <button class="btn btn-warning btn-sm" onclick="editVenue(<?= htmlspecialchars(json_encode($venue)) ?>)">Edit</button>
                        <a href="manage_venues.php?delete=<?= $venue['id'] ?>" class="btn btn-danger btn-sm">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
    function editVenue(venue) {
        document.getElementById('venue_id').value = venue.id;
        document.getElementById('name').value = venue.name;
        document.getElementById('address').value = venue.address;
        document.getElementById('capacity').value = venue.capacity;
        document.getElementById('contact_info').value = venue.contact_info;
        document.getElementById('facilities').value = venue.facilities;
        document.getElementById('description').value = venue.description;
        document.getElementById('images').value = venue.images;
    }
    </script>
</body>
</html>
