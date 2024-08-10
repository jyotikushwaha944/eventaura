<?php
session_start();
require 'includes/database.php';
require 'header.php'; // Include header

if (!isset($_GET['event_id'])) {
    die('Event ID not provided.');
}

$event_id = $_GET['event_id'];

// Get the MySQLi connection
$conn = getDB();

try {
    // Prepare and execute the query to get event details
    $stmt = $conn->prepare("SELECT * FROM event WHERE id = ?");
    $stmt->bind_param('i', $event_id);
    $stmt->execute();
    $event_result = $stmt->get_result();

    if ($event_result->num_rows === 0) {
        throw new Exception("Event not found.");
    }

    $event = $event_result->fetch_assoc();

    // Prepare and execute the query to get the total number of tickets already booked
    $stmt = $conn->prepare("SELECT SUM(num_tickets) as total_booked FROM booking WHERE event_id = ?");
    $stmt->bind_param('i', $event_id);
    $stmt->execute();
    $booking_result = $stmt->get_result();
    $booking_data = $booking_result->fetch_assoc();

    $total_booked = $booking_data['total_booked'] ?? 0;
    $available_tickets = max(0, $event['participant_count'] - $total_booked);

} catch (Exception $e) {
    // Handle any errors
    die($e->getMessage());
}
?>

<div class="container mt-5">
    <div class="card">
        <?php if (!empty($event['image_large'])): ?>
            <img src="<?php echo htmlspecialchars($event['image_large']); ?>" class="card-img-top" alt="Event Image" style="width: 100%; height: auto;">
        <?php else: ?>
            <img src="https://via.placeholder.com/1200x300" class="card-img-top" alt="Placeholder Image" style="width: 100%; height: auto;">
        <?php endif; ?>
        <div class="card-body">
            <h2 class="card-title text-center"><?php echo htmlspecialchars($event['name']); ?></h2>
            <div class="row">
                <div class="col-md-8">
                    <p class="card-text"><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><strong>Start DateTime:</strong> <?php echo htmlspecialchars($event['start_datetime']); ?></li>
                        <li class="list-group-item"><strong>End DateTime:</strong> <?php echo htmlspecialchars($event['end_datetime']); ?></li>
                        <li class="list-group-item"><strong>Location:</strong> <?php echo htmlspecialchars($event['venue']); ?></li>
                        <li class="list-group-item"><strong>Price:</strong> <?php echo htmlspecialchars($event['price']); ?></li>
                        <li class="list-group-item"><strong>Available Tickets:</strong> <?php echo $available_tickets; ?></li>
                    </ul>
                </div>
                <div class="col-md-4 text-center d-flex align-items-center justify-content-center">
                    <?php if (isset($_SESSION['userid'])): ?>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ticketModal">Get Tickets</button>
                    <?php else: ?>
                        <p class="text-danger">Please log in to book tickets.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Ticket Modal -->
<div class="modal fade" id="ticketModal" tabindex="-1" role="dialog" aria-labelledby="ticketModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="register_ticket.php" method="POST" onsubmit="return validateForm();">
                <div class="modal-header">
                    <h5 class="modal-title" id="ticketModalLabel">Book Tickets</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="num_tickets">Number of Tickets</label>
                        <input type="number" id="num_tickets" name="num_tickets" class="form-control" min="1" value="1" required>
                    </div>
                    <div class="form-group">
                        <label for="total_price">Total Price</label>
                        <input type="text" id="total_price" class="form-control" value="<?php echo htmlspecialchars($event['price']); ?>" readonly>
                    </div>
                    <input type="hidden" name="event_id" value="<?php echo htmlspecialchars($event['id']); ?>">
                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($_SESSION['userid']); ?>">
                    <input type="hidden" id="available_tickets" value="<?php echo $available_tickets; ?>">
                    <p id="error_message" class="text-danger" style="display: none;">Not enough seats available.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Book</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const pricePerTicket = <?php echo intval($event['price']); ?>;
    const numTicketsInput = document.getElementById('num_tickets');
    const totalPriceInput = document.getElementById('total_price');
    const availableTickets = parseInt(document.getElementById('available_tickets').value, 10);
    const errorMessage = document.getElementById('error_message');

    numTicketsInput.addEventListener('input', function() {
        const numTickets = parseInt(numTicketsInput.value) || 1;
        const totalPrice = numTickets * pricePerTicket;
        totalPriceInput.value = totalPrice;

        if (numTickets <= availableTickets) {
            errorMessage.style.display = 'none';
            numTicketsInput.classList.remove('is-invalid');
           
        } else {
            errorMessage.style.display = 'block';
            numTicketsInput.classList.add('is-invalid');
        }
    });

    // Validate form before submitting
    window.validateForm = function() {
        const numTickets = parseInt(numTicketsInput.value, 10);
        if (numTickets > availableTickets) {
            errorMessage.style.display = 'block';
            return false;
        }
        return true;
    }
});
</script>
</body>
</html>
