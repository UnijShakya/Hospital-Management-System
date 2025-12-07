<?php
$host = 'localhost';
$db   = 'nplh';
$user = 'root';
$pass = '';

$con = new mysqli($host, $user, $pass, $db);
if ($con->connect_error) {
    die("DB connection failed: " . $con->connect_error);
}
$con->set_charset('utf8mb4');
?>
