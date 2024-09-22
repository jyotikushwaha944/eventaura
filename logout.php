<?php
session_start();
session_unset();
session_destroy();
session_start();
$_SESSION['message'] = 'You have been successfully logged out.';
header('Location: index.php');
exit;
?>
