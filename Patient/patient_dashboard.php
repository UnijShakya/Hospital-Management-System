<?php
session_start();
include('db.php');
include('patient_sidebar.php'); // sidebar for patient

// Handle logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_unset();
    session_destroy();
    header("Location: login_patient.php");
    exit();
}

// Check if patient is logged in
if (!isset($_SESSION['patient_id'])) {
    header("Location: login_patient.php");
    exit();
}

$patient_id = $_SESSION['patient_id'];
$patient_name = $_SESSION['patient_name'];

// Fetch total appointments for this patient (from appointment_slots)
$apptResult = $con->prepare("
    SELECT COUNT(*) as total_appointments 
    FROM appointment_slots 
    WHERE patient_id=? AND status='booked'
");
$apptResult->bind_param("i", $patient_id);
$apptResult->execute();
$apptCount = $apptResult->get_result()->fetch_assoc()['total_appointments'];

// Fetch total doctors registered
$doctorResult = $con->query("SELECT COUNT(*) as total_doctors FROM doctors");
$doctorCount = $doctorResult->fetch_assoc()['total_doctors'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Patient Dashboard</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<style>
body {
    background: #f0f4f8;
    font-family: 'Poppins', sans-serif;
}

/* Content wrapper */
.content-wrapper {
    margin-left: 250px; /* match your sidebar width */
    padding: 40px;
}

/* Dashboard Title */
h2 {
    color: #000000ff;
    font-weight: 600;
    margin-bottom: 40px;
}

/* Card Styles */
.card {
    border-radius: 16px;
    transition: all 0.3s ease;
    box-shadow: 0 6px 20px rgba(0,0,0,0.1);
    position: relative;
    overflow: hidden;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 25px rgba(0,0,0,0.15);
}

/* Card Gradient Backgrounds */
.card-appointments {
    background: linear-gradient(135deg, #fcfcfcff, #fcfcfcff);
    color: #000000ff;
}

.card-doctors {
    background: linear-gradient(135deg, #fcfcfcff, #fcfcfcff);
    color: #000000ff;
}

/* Card Content */
.card i {
    font-size: 40px;
    opacity: 0.7;
    position: absolute;
    top: 20px;
    right: 20px;
}

.card h5 {
    font-weight: 500;
    margin-bottom: 15px;
}

.card h2 {
    font-size: 36px;
    font-weight: 700;
}

/* Responsive */
@media (max-width: 768px) {
    .content-wrapper {
        margin-left: 0;
        padding: 20px;
    }
}
</style>
</head>
<body>

<div class="content-wrapper">
    <h2 class="text-center">Welcome, <?= htmlspecialchars($patient_name) ?></h2>

    <div class="row g-4 justify-content-center">
        <div class="col-md-4">
            <div class="card card-appointments p-4">
                <i class="fas fa-calendar-check"></i>
                <h5>Total Appointments</h5>
                <h2><?= $apptCount ?></h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-doctors p-4">
                <i class="fas fa-user-md"></i>
                <h5>Total Doctors</h5>
                <h2><?= $doctorCount ?></h2>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
