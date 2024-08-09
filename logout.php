<?php
session_start();

// Clear session variables
session_unset();

// Destroy the session
session_destroy();

// Start a new session to set the logout message
session_start();
$_SESSION['message'] = 'You have been successfully logged out.';

// Redirect to the event list page or any other page
header('Location: index.php');
exit;
?>
