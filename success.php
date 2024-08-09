<?php
session_start();
require 'includes/database.php';

// Check if the 'data' parameter is set in the URL
if (isset($_GET['data'])) {
    // Extract and decode the 'data' parameter
    $encodedData = $_GET['data'];
    $decodedData = base64_decode($encodedData);

    // Parse the JSON data from the decoded string
    $responseData = json_decode($decodedData, true);

    if ($responseData && isset($responseData['transaction_code'], $responseData['status'], $responseData['total_amount'], $responseData['transaction_uuid'], $responseData['product_code'], $responseData['signed_field_names'], $responseData['signature'])) {
        $transaction_code = $responseData['transaction_code'];
        $status = $responseData['status'];
        $total_amount = $responseData['total_amount'];
        $transaction_uuid = $responseData['transaction_uuid'];
        $product_code = $responseData['product_code'];
        $signed_field_names = $responseData['signed_field_names'];
        $signature = $responseData['signature'];

        // Verify the signature
        $message = "transaction_code={$transaction_code},status={$status},total_amount={$total_amount},transaction_uuid={$transaction_uuid},product_code={$product_code},signed_field_names={$signed_field_names}";
        $secret_key = '8gBm/:&EnhH.1/q'; // Replace with your secret key
        $generated_signature = base64_encode(hash_hmac('sha256', $message, $secret_key, true));

        if ($signature === $generated_signature && $status === "COMPLETE") {
            // Payment is successful. Insert the booking into the database
            try {
                // Get the number of tickets and event ID from the session
                $num_tickets = $_SESSION['num_tickets'];
                $user_id = $_SESSION['userid'];
                $event_id = intval($_SESSION['event_id']);

                // Get the MySQLi connection
                $conn = getDB();

                $stmt = $conn->prepare("INSERT INTO booking (user_id, event_id, num_tickets, status) VALUES (?, ?, ?, 'booked')");
                $stmt->bind_param('iii', $user_id, $event_id, $num_tickets);

                if ($stmt->execute()) {
                    $_SESSION['message'] = "Payment successful and booking confirmed.";
                } else {
                    throw new Exception("Error inserting booking: " . $stmt->error);
                }
            } catch (Exception $e) {
                $_SESSION['message'] = $e->getMessage();
            }
        } else {
            $_SESSION['message'] = "Payment verification failed.";
        }
    } else {
        $_SESSION['message'] = "Invalid payment response.";
    }
} else {
    $_SESSION['message'] = "No payment data received.";
}

header("Location: index.php");
exit();
?>
