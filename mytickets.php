<?php
session_start();
require 'includes/database.php';
require 'header.php'; // Include header

// Get the MySQLi connection
$conn = getDB();

try {
    if (!isset($_SESSION['userid'])) {
        throw new Exception("User not logged in.");
    }
    $user_id = $_SESSION['userid'];

    // Fetch booked events with the number of tickets
    $query = "
        SELECT e.*, SUM(b.num_tickets) as ticket_count 
        FROM event e
        JOIN booking b ON e.id = b.event_id
        WHERE b.user_id = ?
        GROUP BY e.id
        ORDER BY e.created_datetime DESC
    ";

    // Prepare the query
    if (!$stmt = $conn->prepare($query)) {
        throw new Exception("Error preparing statement: " . $conn->error);
    }

    // Bind parameters
    if (!$stmt->bind_param('i', $user_id)) {
        throw new Exception("Error binding parameters: " . $stmt->error);
    }

    // Execute the query
    if (!$stmt->execute()) {
        throw new Exception("Error executing query: " . $stmt->error);
    }

    // Get results
    $result = $stmt->get_result();
    if (!$result) {
        throw new Exception("Error fetching results: " . $conn->error);
    }

    $booked_events = $result->fetch_all(MYSQLI_ASSOC);

    // Close the statement
    $stmt->close();
} catch (Exception $e) {
    // Handle any errors
    die($e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Booked Events</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .ticket-card {
            width: 100mm; /* Ticket width */
            border: 1px solid #007bff;
            border-radius: 8px;
            padding: 10px;
            margin: 10px auto;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            font-size: 0.75rem; /* Smaller font size */
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background-color: #f8f9fa;
        }
        .ticket-card img {
            width: 100%;
            height: 25mm; /* Image height */
            object-fit: cover;
            border-radius: 8px;
        }
        .ticket-card .ticket-details {
            margin-top: 5px;
        }
        .ticket-card .ticket-details p {
            margin-bottom: 3px;
        }
        .ticket-card .ticket-details .ticket-title {
            font-size: 1rem;
            font-weight: bold;
        }
        .ticket-card .barcode {
            margin-top: 5px;
            font-size: 0.75rem;
            text-align: center;
        }
        .print-button-container {
            text-align: center;
            margin-top: 10px;
        }
        @media print {
            body {
                margin: 0;
            }
            .container {
                width: 100%;
                padding: 0;
            }
            .ticket-card {
                border: none;
                box-shadow: none;
                page-break-inside: avoid;
                margin: 0;
                width: 100mm; /* Ensure the ticket retains size on print */
                height: auto; /* Adjust height based on content */
            }
            .print-button-container {
                display: none;
            }
            /* Print only ticket section */
            .print-content {
                display: block;
            }
            body * {
                visibility: hidden;
            }
            .print-content, .print-content * {
                visibility: visible;
            }
            .print-content {
                position: absolute;
                left: 0;
                top: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row mb-4">
            <div class="col-md-12">
                <h2 class="text-center">My Booked Events</h2>
            </div>
        </div>

        <?php if (empty($booked_events)): ?>
            <div class="alert alert-warning text-center">You have not booked any events.</div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($booked_events as $event): ?>
                    <div class="col-md-12 mb-4">
                        <div class="ticket-card print-content">
                            <!-- Sample Barcode Image URL -->
                            <img src="https://barcode.tec-it.com/barcode.ashx?data=<?php echo urlencode($event['id']); ?>&code=Code128&dpi=96" alt="Event Barcode">
                            <div class="ticket-details">
                                <p class="ticket-title"><?php echo htmlspecialchars($event['name']); ?></p>
                                <p><strong>Description:</strong> <?php echo htmlspecialchars($event['description']); ?></p>
                                <p><strong>Start DateTime:</strong> <?php echo htmlspecialchars($event['start_datetime']); ?></p>
                                <p><strong>End DateTime:</strong> <?php echo htmlspecialchars($event['end_datetime']); ?></p>
                                <p><strong>Location:</strong> <?php echo htmlspecialchars($event['venue']); ?></p>
                                <p><strong>Price:</strong> <?php echo htmlspecialchars($event['price']); ?></p>
                                <p><strong>Tickets Booked:</strong> <?php echo htmlspecialchars($event['ticket_count']); ?></p>
                            </div>
                            <div class="barcode">
                                <!-- Barcode text -->
                                <p>Barcode: <?php echo htmlspecialchars($event['id']); ?></p>
                            </div>
                        </div>
                        <!-- Print button for each ticket -->
                        <div class="print-button-container">
                            <button class="btn btn-primary" onclick="printTicket(<?php echo htmlspecialchars($event['id']); ?>)">Print Ticket</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>

    <script>
        function printTicket(ticketId) {
            // Get the specific ticket content
            var ticketContent = Array.from(document.querySelectorAll('.ticket-card')).find(card => card.querySelector('.barcode p').textContent.includes(ticketId)).innerHTML;

            // Open a new window
            var printWindow = window.open('', '_blank');

            // Write ticket content to new window
            printWindow.document.open();
            printWindow.document.write(`
                <html>
                    <head>
                        <title>Print Ticket</title>
                        <style>
                            .ticket-card {
                                width: 100mm; /* Ticket width */
                                border: 1px solid #007bff;
                                border-radius: 8px;
                                padding: 10px;
                                margin: 10px auto;
                                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
                                font-size: 0.75rem; /* Smaller font size */
                                position: relative;
                                display: flex;
                                flex-direction: column;
                                justify-content: space-between;
                                background-color: #f8f9fa;
                            }
                            .ticket-card img {
                                width: 100%;
                                height: 25mm; /* Image height */
                                object-fit: cover;
                                border-radius: 8px;
                            }
                            .ticket-card .ticket-details {
                                margin-top: 5px;
                            }
                            .ticket-card .ticket-details p {
                                margin-bottom: 3px;
                            }
                            .ticket-card .ticket-details .ticket-title {
                                font-size: 1rem;
                                font-weight: bold;
                            }
                            .ticket-card .barcode {
                                margin-top: 5px;
                                font-size: 0.75rem;
                                text-align: center;
                            }
                        </style>
                    </head>
                    <body onload="window.print();window.close();">
                        <div class="ticket-card">
                            ${ticketContent}
                        </div>
                    </body>
                </html>
            `);
            printWindow.document.close();
        }
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
