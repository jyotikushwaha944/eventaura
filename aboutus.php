<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css"> <!-- Custom stylesheet -->
    <style>
        body {
            margin: 0;
            padding: 0;
        }
        .about-us-image {
            background-image: url('img/Heading.png'); /* Correct path to your image */
            background-size: cover;
            background-position: center;
            height: 800px; /* Increased height */
            width: 100vw; /* Full viewport width */
            max-width: 100%; /* Ensure the image does not overflow */
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            margin: 0; /* Remove margin to decrease gap */
        }
        .container {
            padding-left: 0; /* Remove container padding on the left */
            padding-right: 0; /* Remove container padding on the right */
        }
        .about-us-section {
            margin-top: 0; /* Remove margin to decrease gap between image and section */
        }
        .about-us-mission, .about-us-vision {
            padding: 20px;
            background-color: #f8f9fa; /* Light gray background */
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .about-us-mission h2, .about-us-vision h2 {
            color: #007bff; /* Bootstrap primary color */
            font-size: 2rem; /* Larger font size */
            font-weight: 700; /* Bold font weight */
            margin-bottom: 15px;
            text-transform: uppercase; /* Uppercase text */
            letter-spacing: 1px; /* Spacing between letters */
            border-bottom: 2px solid #007bff; /* Bottom border with color */
            padding-bottom: 10px; /* Padding below the text */
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1); /* Subtle text shadow */
            font-family: 'Arial', sans-serif; /* Custom font family */
        }
        .about-us-mission p, .about-us-vision p {
            font-size: 1rem;
            line-height: 1.6;
            color: #343a40;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="about-us-image"></div> <!-- Image section -->
    <div class="container-fluid mt-0">
        <div class="row about-us-section">
            <div class="col-md-6 about-us-mission">
                <h2>Our Mission</h2>
                <p>At EventAura, our mission is to transform your vision into unforgettable experiences. We are dedicated to organizing events with precision and creativity, ensuring every detail is perfect and every moment is memorable. Whether it’s a corporate conference, a wedding, or a community gathering, we bring expertise and enthusiasm to every project.</p>
            </div>
            <div class="col-md-6 about-us-vision">
                <h2>Our Vision</h2>
                <p>Our vision is to be the leading event management company known for our innovative solutions and exceptional service. We strive to exceed expectations by combining the latest trends in event planning with personalized attention, creating events that not only meet but surpass our clients' dreams and aspirations.</p>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
