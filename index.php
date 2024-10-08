<?php
session_start();
require 'includes/database.php';
require 'header.php'; 
require 'recommendations.php';


$conn = getDB();

try {
    
    $category_result = $conn->query("SELECT id, name FROM category");
    if (!$category_result) {
        throw new Exception("Error fetching categories: " . $conn->error);
    }
    $categories = $category_result->fetch_all(MYSQLI_ASSOC);

    
    $category_id = isset($_POST['event_type']) ? (int)$_POST['event_type'] : 0;
    $query = "SELECT * FROM event WHERE start_datetime >= NOW() AND IsActive = 1";
    if ($category_id > 0) {
        $query .= " AND category_id = " . $category_id;
    }
    $query .= " ORDER BY created_datetime DESC";

    $result = $conn->query($query);
    if (!$result) {
        throw new Exception("Error fetching events: " . $conn->error);
    }
    $events = $result->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    
    die($e->getMessage());
}


if (isset($_SESSION['userid'])) {
    $user_id = $_SESSION['userid'];
    $recommendedEvents = findRecommendedEvents($conn, $user_id);
} else {
    $recommendedEvents = [];
}
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
            height: 100%; 
            display: flex;
            flex-direction: column;
            border: 1px solid #ddd; 
            border-radius: 0.5rem; 
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); 
        }

        .card-img-wrapper {
            height: 200px;
            overflow: hidden;
        }

        .card-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .card-body {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 1rem;
        }

        .card-body .btn {
            margin-top: auto; 
        }

        .alert-info {
            font-size: 0.875rem; 
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

        .section-title {
            border-bottom: 2px solid #007bff; /* Blue underline for section titles */
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
        }

        .video-wrapper {
            width: 100%;
            height: 400px;
            overflow: hidden;
            margin-bottom: 2rem; 
            border-radius: 0.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); 
        }

        .video-wrapper video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
</head>
<body>

<div class="container-fluid mt-2"> 
    
    <div class="video-wrapper">
        <video autoplay muted loop>
            <source src="img/175579-853849692_small.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>

    <!-- Recommendations Section -->
    <?php if (!empty($recommendedEvents)): ?>
        <div class="row">
            <div class="col-12 mb-4">
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-info text-center"><?php echo htmlspecialchars($_SESSION['message']); ?></div>
                    <?php unset($_SESSION['message']); ?>
                <?php endif; ?>
                <h4 class="section-title">Recommended events for you</h4>
                <div class="row">
                    <?php foreach ($recommendedEvents as $event): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card border-secondary">
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
                                        <p class="card-text"><small class="text-muted"><?php echo htmlspecialchars($event['description']); ?></small></p>
                                        <a href="event_detail.php?event_id=<?php echo htmlspecialchars($event['id']); ?>" class="btn btn-primary btn-sm">View Details</a>
                                    </div>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Search Events Section -->
    <div class="row mb-4">
        <div class="col-md-4 align-items-center justify-content-end">
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
        <div class="col-md-8">
            <!-- No Upcoming Events text -->
        </div>
    </div>

    <h4 class="section-title">Our top picks for you</h4>

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
                                <p class="card-text"><small class="text-muted"><strong>Venue:</strong> <?php echo htmlspecialchars($event['venue2']); ?></small></p>
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

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<?php include 'footer.php'; ?>

</body>
</html>
