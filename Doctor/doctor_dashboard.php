<?php
session_start();
include('db.php');
include('sidebar.php'); // sidebar included

// Handle logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_unset();
    session_destroy();
    header("Location: login_doctor.php");
    exit();
}

// Check if doctor is logged in
if (!isset($_SESSION['doctor_id'])) {
    header("Location: login_doctor.php");
    exit();
}

$doctor_id = $_SESSION['doctor_id'];
$doctor_name = $_SESSION['doctor_name'];

// Fetch total appointments (count booked slots for this doctor)
$apptResult = $con->prepare("SELECT COUNT(*) as total_appointments FROM appointment_slots WHERE doctor_id=? AND status='booked'");
$apptResult->bind_param("i", $doctor_id);
$apptResult->execute();
$apptCount = $apptResult->get_result()->fetch_assoc()['total_appointments'];

// Fetch total patients (distinct patients who booked appointments with this doctor)
$patientResult = $con->prepare("
    SELECT COUNT(DISTINCT patient_id) as total_patients 
    FROM appointment_slots 
    WHERE doctor_id=? AND patient_id IS NOT NULL
");
$patientResult->bind_param("i", $doctor_id);
$patientResult->execute();
$patientCount = $patientResult->get_result()->fetch_assoc()['total_patients'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Doctor Dashboard</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<style>
body {
    background: #f8f9fa;
}

/* Main content shifted right to leave sidebar space */
.content-wrapper {
    margin-left: 220px; /* same width as sidebar */
    padding: 40px;
}

.card {
    border-radius: 10px;
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-5px);
}
</style>
</head>
<body>

<div class="content-wrapper">
    <h2 class="text-center mb-4">Welcome, Dr. <?= htmlspecialchars($doctor_name) ?></h2>

    <div class="row justify-content-center text-center">
        <div class="col-md-4 mb-3">
            <div class="card shadow p-4">
                <h5>Total Appointments</h5>
                <h2><?= $apptCount ?></h2>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card shadow p-4">
                <h5>Total Patients</h5>
                <h2><?= $patientCount ?></h2>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
