<?php
session_start();
require 'includes/database.php'; 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (!isset($_SESSION['loggedIn'])) {
    header("Location: login.php");
    exit();
}
$conn = getDB();
$categories = [];
try {
    $stmt = $conn->prepare("SELECT id, name FROM category");
    if (!$stmt) {
        throw new Exception($conn->error);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
    $stmt->close();
} catch (Exception $e) {
    die("Error fetching categories: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and sanitize form inputs
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category_id = $_POST['category_id'];
    $participant_count = $_POST['participant_count'];
    $start_datetime = $_POST['start_datetime'];
    $end_datetime = $_POST['end_datetime'];
    $venue = $_POST['venue'];
    $venue2 = $_POST['venue2'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $price = $_POST['price'];
    $created_by = $_SESSION['userid'];
    $created_datetime = date('Y-m-d H:i:s');

    // Handle file uploads
    $uploadDir = 'img/uploads/';
    $largeImagePath = '';
    $smallImagePath = '';

    function generateUniqueFilename($originalName) {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $uniqueName = uniqid('img_', true) . '.' . $extension;
        return $uniqueName;
    }

    if (isset($_FILES['image_large']) && $_FILES['image_large']['error'] == UPLOAD_ERR_OK) {
        $largeImageName = generateUniqueFilename($_FILES['image_large']['name']);
        $largeImagePath = $uploadDir . 'large_' . $largeImageName;
        move_uploaded_file($_FILES['image_large']['tmp_name'], $largeImagePath);
    }

    if (isset($_FILES['image_small']) && $_FILES['image_small']['error'] == UPLOAD_ERR_OK) {
        $smallImageName = generateUniqueFilename($_FILES['image_small']['name']);
        $smallImagePath = $uploadDir . 'small_' . $smallImageName;
        move_uploaded_file($_FILES['image_small']['tmp_name'], $smallImagePath);
    }

    try {
        $stmt = $conn->prepare("INSERT INTO event (name, description, venue, venue2, participant_count, start_datetime, end_datetime, created_datetime, price, created_by, category_id, latitude, longitude, image_large, image_small) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            throw new Exception($conn->error);
        }

        $stmt->bind_param("ssssisssdiiddss", $title, $description, $venue, $venue2, $participant_count, $start_datetime, $end_datetime, $created_datetime, $price, $created_by, $category_id, $latitude, $longitude, $largeImagePath, $smallImagePath);
        
        $stmt->execute();

        if ($stmt->affected_rows === 0) {
            throw new Exception("Failed to insert the event.");
        }

        $_SESSION['message'] = "Event created successfully.";
        header("Location: index.php");
        exit();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .autocomplete-dropdown {
            max-height: 200px;
            overflow-y: auto;
            position: absolute;
            z-index: 1000;
            width: 100%;
            background-color: white; 
            border: 1px solid #ccc; 
            margin-top: -1px; 
        }
        .autocomplete-item {
            padding: 10px;
            cursor: pointer;
            white-space: nowrap; 
            overflow: hidden;
            text-overflow: ellipsis; 
        }
        .autocomplete-item:hover {
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>
    <div class="container-fluid mt-5">
        <h2 class="mb-4">Create Event</h2>
        
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($_SESSION['message']); ?>
                <?php unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" class="form-control form-control-sm" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="category_id">Category</label>
                    <select id="category_id" name="category_id" class="form-control form-control-sm" required>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo htmlspecialchars($category['id']); ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label for="price">Price</label>
                    <input type="text" id="price" name="price" class="form-control form-control-sm" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="participant_count">No. of Participants</label>
                    <input type="number" id="participant_count" name="participant_count" class="form-control form-control-sm" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="start_datetime">Start DateTime</label>
                    <input type="datetime-local" id="start_datetime" name="start_datetime" class="form-control form-control-sm" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="end_datetime">End DateTime</label>
                    <input type="datetime-local" id="end_datetime" name="end_datetime" class="form-control form-control-sm" required>
                </div>
            </div>
            <div class="form-group position-relative">
                <label for="venue">Location</label>
                <input type="text" id="venue" name="venue" class="form-control form-control-sm" autocomplete="off" required>
                <div id="autocomplete-dropdown" class="autocomplete-dropdown"></div>
                <input type="hidden" id="latitude" name="latitude">
                <input type="hidden" id="longitude" name="longitude">
            </div>
            <div class="form-group position-relative">
                <label for="venue">Venue</label>
                <select name="venue2" id="venues">
                    <option value="Nepal Pragya Pratisthan">Nepal Pragya Pratisthan</option>
                    <option value="Bishwo Bhasha Campus Hall">Bishwo Bhasha Campus Hall</option>
                    <option value="APF Stadium">APF Stadium</option>
                    <option value="Hotel Yak & Yeti Conference Hall">Hotel Yak & Yeti Conference Hall</option>
                    <option value="Gyaneshwor Hall">Gyaneshwor Hall</option>
                    <option value="Birgunj Stadium">Birgunj Stadium</option>
                    <option value="Dharan Stadium">Dharan Stadium</option>
                </select>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control form-control-sm" rows="3" required></textarea>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="image_large">Upload Large Image (1200x1300)</label>
                    <input type="file" id="image_large" name="image_large" class="form-control-file" accept="image/*">
                </div>
                <div class="form-group col-md-6">
                    <label for="image_small">Upload Small Image (350x200)</label>
                    <input type="file" id="image_small" name="image_small" class="form-control-file" accept="image/*">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Create Event</button>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const venueInput = document.getElementById('venue');
            const dropdown = document.getElementById('autocomplete-dropdown');
            
            venueInput.addEventListener('input', function() {
                const query = venueInput.value;
                if (query.length >= 4) {
                    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(data => {
                            dropdown.innerHTML = ''; 
                            data.forEach(place => {
                                const div = document.createElement('div');
                                div.className = 'autocomplete-item';
                                div.textContent = place.display_name;
                                div.dataset.lat = place.lat;
                                div.dataset.lon = place.lon;
                                div.addEventListener('click', function() {
                                    venueInput.value = place.display_name;
                                    document.getElementById('latitude').value = place.lat;
                                    document.getElementById('longitude').value = place.lon;
                                    dropdown.innerHTML = ''; // Clear dropdown
                                });
                                dropdown.appendChild(div);
                            });
                        })
                        .catch(error => console.error('Error:', error));
                } else {
                    dropdown.innerHTML = ''; 
                }
            });
        });
    </script>
</body>
</html>
