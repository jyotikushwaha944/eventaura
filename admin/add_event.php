<?php
include 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $description = $conn->real_escape_string($_POST['description']);
    $venue = $conn->real_escape_string($_POST['venue']);
    $latitude = $conn->real_escape_string($_POST['latitude']);
    $longitude = $conn->real_escape_string($_POST['longitude']);
    $start_datetime = $conn->real_escape_string($_POST['start_datetime']);
    $end_datetime = $conn->real_escape_string($_POST['end_datetime']);
    $price = (float)$_POST['price'];
    $category_id = (int)$_POST['category_id'];

    $sql = "INSERT INTO events (name, description, venue, latitude, longitude, start_datetime, end_datetime, created_datetime, created_by, price, category_id)
            VALUES ('$name', '$description', '$venue', '$latitude', '$longitude', '$start_datetime', '$end_datetime', NOW(), '1', '$price', '$category_id')";

    if ($conn->query($sql) === TRUE) {
        header('Location: events.php');
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Event</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1 class="mb-4">Add Event</h1>
        <form method="post">
            <div class="form-group">
                <label for="name">Event Name</label>
                <input type="text" id="name" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control" required></textarea>
            </div>
            <div class="form-group">
                <label for="venue">Venue</label>
                <input type="text" id="venue" name="venue" class="form-control">
            </div>
            <div class="form-group">
                <label for="latitude">Latitude</label>
                <input type="text" id="latitude" name="latitude" class="form-control">
            </div>
            <div class="form-group">
                <label for="longitude">Longitude</label>
                <input type="text" id="longitude" name="longitude" class="form-control">
            </div>
            <div class="form-group">
                <label for="start_datetime">Start Date & Time</label>
                <input type="datetime-local" id="start_datetime" name="start_datetime" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="end_datetime">End Date & Time</label>
                <input type="datetime-local" id="end_datetime" name="end_datetime" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="price">Price</label>
                <input type="number" id="price" name="price" class="form-control" step="0.01">
            </div>
            <div class="form-group">
                <label for="category_id">Category</label>
                <select id="category_id" name="category_id" class="form-control" required>
                    <?php
                    $category_sql = "SELECT * FROM categories";
                    $category_result = $conn->query($category_sql);
                    while ($category = $category_result->fetch_assoc()) {
                        echo "<option value=\"{$category['id']}\">{$category['name']}</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-success">Add Event</button>
        </form>
    </div>
</body>
</html>
