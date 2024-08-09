<?php
session_start();
require 'includes/database.php';

// Check if the user is logged in
if (!isset($_SESSION['userid'])) {
    $_SESSION['message'] = "You need to log in to book tickets.";
    header("Location: login.php");
    exit();
}

// Check if the required POST data is set
if (!isset($_POST['event_id'], $_POST['num_tickets'])) {
    $_SESSION['message'] = "Invalid booking request.";
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['userid'];
$event_id = intval($_POST['event_id']);
$num_tickets = intval($_POST['num_tickets']);

// Store the event and ticket data in the session
$_SESSION['event_id'] = $event_id;
$_SESSION['num_tickets'] = $num_tickets;

// Get the MySQLi connection
$conn = getDB();

try {
    // Retrieve the event details including the price per ticket
    $stmt = $conn->prepare("SELECT price FROM event WHERE id = ?");
    $stmt->bind_param('i', $event_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("Event not found.");
    }

    $event = $result->fetch_assoc();
    $price_per_ticket = intval($event['price']); // Get the price from the event table
    $total_amount = $price_per_ticket * $num_tickets; // Calculate the total amount
    $tax_amount = 0; // Update this value if there's any tax
    $transaction_uuid = uniqid(); // Generate a unique transaction ID
    $product_code = 'EPAYTEST'; // Replace with your product code
    $product_service_charge = 0; // Update this value if applicable
    $product_delivery_charge = 0; // Update this value if applicable
    $success_url = 'http://localhost/EventAura/success.php'; // Success URL
    $failure_url = 'http://localhost/EventAura/failure.php'; // Failure URL
    $signed_field_names = "total_amount,transaction_uuid,product_code";
    
    // Generate the signature
    $message = "total_amount={$total_amount},transaction_uuid={$transaction_uuid},product_code={$product_code}";
    $secret_key = '8gBm/:&EnhH.1/q'; // Replace with your secret key
    $signature = base64_encode(hash_hmac('sha256', $message, $secret_key, true));
    
    // Prepare the redirect form to eSewa
    echo '<form id="esewaForm" action="https://rc-epay.esewa.com.np/api/epay/main/v2/form" method="POST" style="display: none;">
            <input type="hidden" id="amount" name="amount" value="' . $total_amount . '" required>
            <input type="hidden" id="tax_amount" name="tax_amount" value="' . $tax_amount . '" required>
            <input type="hidden" id="total_amount" name="total_amount" value="' . $total_amount . '" required>
            <input type="hidden" id="transaction_uuid" name="transaction_uuid" value="' . $transaction_uuid . '" required>
            <input type="hidden" id="product_code" name="product_code" value="' . $product_code . '" required>
            <input type="hidden" id="product_service_charge" name="product_service_charge" value="' . $product_service_charge . '" required>
            <input type="hidden" id="product_delivery_charge" name="product_delivery_charge" value="' . $product_delivery_charge . '" required>
            <input type="hidden" id="success_url" name="success_url" value="' . $success_url . '" required>
            <input type="hidden" id="failure_url" name="failure_url" value="' . $failure_url . '" required>
            <input type="hidden" id="signed_field_names" name="signed_field_names" value="' . $signed_field_names . '" required>
            <input type="hidden" id="signature" name="signature" value="' . $signature . '" required>
            <input type="submit" value="Submit">
          </form>
          <script type="text/javascript">
              document.getElementById("esewaForm").submit();
          </script>';
} catch (Exception $e) {
    // Handle any errors
    $_SESSION['message'] = $e->getMessage();
    header("Location: index.php");
    exit();
}
?>
