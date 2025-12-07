<?php

include('db.php');

// Get current page for active link
$currentPage = basename($_SERVER['PHP_SELF']);

// Logout handling
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_unset();
    session_destroy();
    header("Location: login_doctor.php");
    exit();
}

$doctor_name = $_SESSION['doctor_name'] ?? 'Doctor';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Doctor Panel</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<style>
body {
    font-family: Arial, sans-serif;
    background: #f8f9fa;
    margin: 0;
}
.sidebar {
    width: 220px;
    background: #272727;
    color: #fff;
    height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
    padding-top: 60px;
    transition: all 0.3s;
}
.sidebar h4 {
    text-align: center;
    padding: 10px 0;
    border-bottom: 1px solid #444;
    margin-bottom: 20px;
}
.sidebar .nav-link {
    color: #adb5bd;
    padding: 12px 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 500;
    transition: all 0.2s;
}
.sidebar .nav-link:hover,
.sidebar .nav-link.active {
    background: #495057;
    color: #fff;
    border-radius: 5px;
}
.sidebar .nav-link i {
    font-size: 1.2rem;
}
@media (max-width: 768px) {
    .sidebar {
        position: relative;
        width: 100%;
        height: auto;
        padding-top: 20px;
    }
    .content {
        margin-left: 0;
    }
}
.content {
    margin-left: 220px;
    padding: 20px;
    transition: all 0.3s;
}
</style>
</head>
<body>

<div class="sidebar">
    <h4>Welcome, <?= htmlspecialchars($doctor_name) ?></h4>
    <nav class="nav flex-column">
        <a class="nav-link <?= $currentPage=='doctor_dashboard.php' ? 'active' : '' ?>" href="doctor_dashboard.php">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <a class="nav-link <?= $currentPage=='doctor_schedule.php' ? 'active' : '' ?>" href="doctor_schedule.php">
            <i class="bi bi-calendar-check"></i> Appointment Schedule
        </a>
        <a class="nav-link <?= $currentPage=='appointments.php' ? 'active' : '' ?>" href="appointments.php">
            <i class="bi bi-journal-check"></i> Appointments
        </a>
        <a class="nav-link <?= $currentPage=='patient_details.php' ? 'active' : '' ?>" href="patient_details.php">
            <i class="bi bi-person-vcard"></i> Patients
        </a>
        <a class="nav-link <?= $currentPage=='profile.php' ? 'active' : '' ?>" href="profile.php">
            <i class="bi bi-person-circle"></i> Profile
        </a>
        <a class="nav-link text-danger" href="?action=logout">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
    </nav>
</div>

<div class="content">
    <!-- Main content goes here -->
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
