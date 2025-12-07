<?php
session_start();
include('assets/include/dbconfig.php'); 

if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true){
    header("Location: login.php");
    exit();
}

include('assets/include/sidebar.php'); // Sidebar
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
    body {
        display: flex;
        min-height: 100vh;
        font-family: 'Segoe UI', sans-serif;
        background: #f5f6f8;
    }

    .content {
        margin-left: 220px;
        /* space for sidebar */
        padding: 40px;
        width: 100%;
    }

    .card {
        border-radius: 12px;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s, box-shadow 0.3s, background 0.3s;
        cursor: pointer;
        background: #fff;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
        background: #f8f9fa;
    }

    .card i {
        font-size: 2.8rem;
        color: #0d6efd;
    }

    .card h5 {
        margin-top: 15px;
        font-weight: 600;
    }

    .card p {
        font-size: 1.5rem;
        font-weight: 500;
        margin: 5px 0 0;
        color: #212529;
    }

    .card .card-footer {
        font-size: 0.9rem;
        color: #6c757d;
    }

    .row.g-4 {
        margin-top: 20px;
    }

    @media (max-width: 768px) {
        .content {
            margin-left: 0;
            padding: 20px;
        }
    }
    </style>
</head>

<body>

    <div class="main-content" id="mainContent">
        <h2 class="mb-4">🏥 Hospital Dashboard</h2>
        <div class="row g-4">
            <!-- Patients Card -->
            <div class="col-md-4 col-sm-6">
                <div class="card text-center p-4">
                    <i class="bi bi-person-vcard"></i>
                    <h5>Patients</h5>
                    <?php
                $res = mysqli_query($con, "SELECT COUNT(*) AS total FROM patients");
                $row = mysqli_fetch_assoc($res);
                echo "<p>".$row['total']."</p>";
                ?>
                    <div class="card-footer">Total registered patients</div>
                </div>
            </div>

            <!-- Doctors Card -->
            <div class="col-md-4 col-sm-6">
                <div class="card text-center p-4">
                    <i class="bi bi-person-badge"></i>
                    <h5>Doctors</h5>
                    <?php
                $res = mysqli_query($con, "SELECT COUNT(*) AS total FROM doctors");
                $row = mysqli_fetch_assoc($res);
                echo "<p>".$row['total']."</p>";
                ?>
                    <div class="card-footer">Total doctors in hospital</div>
                </div>
            </div>

            <!-- Appointments Card -->
            <div class="col-md-4 col-sm-6">
                <div class="card text-center p-4">
                    <i class="bi bi-calendar-check"></i>
                    <h5>Appointments</h5>
                    <?php
        $res = mysqli_query($con, "SELECT COUNT(*) AS total FROM appointment_slots WHERE status='booked'");
        $row = mysqli_fetch_assoc($res);
        echo "<p>".$row['total']."</p>";
        ?>
                    <div class="card-footer">Total booked appointments</div>
                </div>
            </div>


        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>