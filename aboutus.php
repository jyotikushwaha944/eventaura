<?php
// Start session and include database connection
session_start();
require 'includes/database.php'; // Ensure the path is correct

// Check if $conn is defined
if (!isset($conn)) {
    die("Database connection failed.");
}

// Fetch some event categories and user types to display on the about page
$category_query = "SELECT name FROM category";
$category_result = $conn->query($category_query);

$usertype_query = "SELECT type FROM usertype";
$usertype_result = $conn->query($usertype_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Eventaura</title>
    <link rel="stylesheet" href="path_to_bootstrap.css">
    <style>
        .about-section {
            padding: 50px;
            background-color: #f9f9f9;
        }
        .about-section h2 {
            font-size: 36px;
            margin-bottom: 20px;
        }
        .about-section p {
            font-size: 18px;
            line-height: 1.6;
        }
        .category-list, .usertype-list {
            list-style-type: none;
            padding: 0;
        }
        .category-list li, .usertype-list li {
            font-size: 16px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <?php include 'navbar.php'; ?>

    <!-- About Us Section -->
    <div class="about-section container">
        <h2>About Eventaura</h2>
        <p>Eventaura is your go-to platform for discovering and managing events in various categories. Our mission is to make event planning and participation seamless, providing a wide range of event options from cultural festivals to corporate seminars.</p>

        <h3>Our Event Categories</h3>
        <ul class="category-list">
            <?php
            if ($category_result->num_rows > 0) {
                while ($row = $category_result->fetch_assoc()) {
                    echo "<li>" . htmlspecialchars($row['name']) . "</li>";
                }
            } else {
                echo "<li>No categories available at the moment.</li>";
            }
            ?>
        </ul>

        <h3>User Roles on Eventaura</h3>
        <ul class="usertype-list">
            <?php
            if ($usertype_result->num_rows > 0) {
                while ($row = $usertype_result->fetch_assoc()) {
                    echo "<li>" . htmlspecialchars($row['type']) . "</li>";
                }
            } else {
                echo "<li>No user roles available at the moment.</li>";
            }
            ?>
        </ul>

        <h3>Our Mission</h3>
        <p>At Eventaura, we strive to bring people together by making events accessible to everyone. Whether you are an individual looking to attend an event, an organization hosting a gathering, or an administrator ensuring everything runs smoothly, Eventaura is here to support you.</p>
    </div>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

</body>
</html>

<?php
$conn->close();
?>
