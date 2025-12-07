<?php
// Get current page for active highlighting
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Patient Sidebar</title>

<!-- Bootstrap & Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<style>
body {
    margin: 0;
    font-family: 'Segoe UI', Arial, sans-serif;
}

/* Sidebar */
.sidebar {
    width: 230px;
    height: 100vh;
    background: #1e1e1e;
    color: #fff;
    position: fixed;
    top: 0;
    left: 0;
    padding-top: 65px;
    display: flex;
    flex-direction: column;
    box-shadow: 2px 0 6px rgba(0,0,0,0.2);
}

.sidebar h4 {
    text-align: center;
    margin: 0;
    padding: 15px 0;
    font-size: 1.2rem;
    background: #111;
    border-bottom: 1px solid #333;
    position: fixed;
    width: 230px;
    top: 0;
    left: 0;
    z-index: 999;
}

.sidebar .nav {
    flex-grow: 1;
    margin-top: 10px;
}

.sidebar .nav-link {
    color: #adb5bd;
    padding: 12px 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.25s ease;
}

.sidebar .nav-link:hover,
.sidebar .nav-link.active {
    background: #5a5a5aff;
    color: #fff;
    border-radius: 6px;
}

.sidebar .nav-link i {
    font-size: 1.2rem;
}

/* Logout link */
.sidebar .nav-link.text-danger {
    color: #ff6b6b !important;
}
.sidebar .nav-link.text-danger:hover {
    background: #dc3545;
    color: #fff !important;
}

/* Responsive */
@media (max-width: 768px) {
    .sidebar {
        width: 100%;
        height: auto;
        position: relative;
        flex-direction: row;
        overflow-x: auto;
        padding-top: 0;
    }
    .sidebar h4 {
        display: none;
    }
    .sidebar .nav {
        flex-direction: row;
        gap: 8px;
        margin: 0;
        padding: 10px;
    }
    .sidebar .nav-link {
        padding: 8px 12px;
        font-size: 0.9rem;
        border-radius: 4px;
    }
}
</style>
</head>
<body>

<div class="sidebar">
    <h4>NPL | Patient</h4>
    <nav class="nav flex-column">

        <a class="nav-link <?= $currentPage == 'patient_dashboard.php' ? 'active' : '' ?>" href="patient_dashboard.php">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        <a class="nav-link <?= $currentPage == 'book_appointment.php' ? 'active' : '' ?>" href="book_appointment.php">
            <i class="bi bi-calendar-plus"></i> Book Appointment
        </a>

        <a class="nav-link <?= $currentPage == 'my_appointments.php' ? 'active' : '' ?>" href="my_appointments.php">
            <i class="bi bi-calendar-check"></i> My Appointments
        </a>

        <a class="nav-link <?= $currentPage == 'patient_reports.php' ? 'active' : '' ?>" href="patient_reports.php">
            <i class="bi bi-file-earmark-medical"></i> My Reports
        </a>

        <a class="nav-link <?= $currentPage == 'profile.php' ? 'active' : '' ?>" href="profile.php">
            <i class="bi bi-person-circle"></i> Profile
        </a>

        <a class="nav-link text-danger" href="?action=logout">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>

    </nav>
</div>

</body>
</html>
