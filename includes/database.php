<?php
function getDB() {
    $db_host = "localhost";
    $db_user = "root";
    $db_pass = "";
    $db_name = "eventaura";
    
    $conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
    
    if (mysqli_connect_error()) {
        die('Database connection failed: ' . mysqli_connect_error());
    }
    
    return $conn;
}
?>
