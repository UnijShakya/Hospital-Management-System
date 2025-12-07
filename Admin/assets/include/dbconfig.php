<?php
// Database configuration
$host = "localhost";      // Usually localhost
$user = "root";           // Your MySQL username
$password = "";           // Your MySQL password
$database = "nplh";       // Your database name

// Create connection
$con = mysqli_connect($host, $user, $password, $database);

// Check connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}
// echo "Connected successfully"; // Optional test
?>
