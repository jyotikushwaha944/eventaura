<?php
session_start();
$_SESSION['message'] = "Payment failed or was cancelled.";
header("Location: index.php");
exit();
?>
