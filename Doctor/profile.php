<?php
include 'db.php'; // your DB connection
include 'sidebar.php';
$doctor_id = 1; // Replace with logged-in doctor ID

// Fetch doctor profile
$stmt = $con->prepare("
    SELECT doctor_name, specialization_id, department_id, clinic_address, consultancy_fees, contact_no, email, created_at
    FROM doctors 
    WHERE id = ?
");
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Doctor Profile</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', Arial, sans-serif;
            margin: 0;
            background-color: #f0f2f5;
        }

        /* Layout wrapper assuming sidebar exists */
        .main-content {
            margin-left: 220px; /* adjust to match your sidebar width */
            padding: 40px;
            min-height: 100vh;
        }

        /* Keep the profile card exactly as before */
        .profile-card {
            max-width: 700px;
            margin: auto;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 30px;
        }
        .profile-card img {
            width: 130px;
            height: 130px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #4CAF50;
            margin-bottom: 20px;
        }
        .profile-card h2 {
            margin: 0 0 10px 0;
            font-size: 28px;
            color: #333;
        }
        .profile-card h3 {
            margin: 0 0 20px 0;
            font-size: 18px;
            color: #777;
        }
        .info {
            width: 100%;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }
        .info div {
            width: 100%;
            background: #f7f9fc;
            margin-bottom: 15px;
            padding: 15px;
            border-radius: 10px;
            display: flex;
            align-items: center;
        }
        .info div i {
            color: #4CAF50;
            margin-right: 10px;
            min-width: 20px;
            text-align: center;
        }
        .info div span {
            font-weight: 500;
            color: #555;
        }

        @media(max-width:800px){
            .main-content { margin-left: 0; padding: 20px; }
            .info div { width: 100%; }
        }
    </style>
</head>
<body>

<div class="main-content">
    <div class="profile-card">
        <img src="../assets/uploads/doctor.png" alt="Profile Image">
        <h2>Dr. <?php echo htmlspecialchars($doctor['doctor_name']); ?></h2>
        <h3>Specialization ID: <?php echo htmlspecialchars($doctor['specialization_id']); ?></h3>

        <div class="info">
            <div><i class="fas fa-building"></i> <span>Department ID: None </span></div>
            <div><i class="fas fa-map-marker-alt"></i> <span>Clinic: <?php echo htmlspecialchars($doctor['clinic_address']); ?></span></div>
            <div><i class="fas fa-money-bill-wave"></i> <span>Fees: Rs. <?php echo htmlspecialchars($doctor['consultancy_fees']); ?></span></div>
            <div><i class="fas fa-phone"></i> <span>Contact: <?php echo htmlspecialchars($doctor['contact_no']); ?></span></div>
            <div><i class="fas fa-envelope"></i> <span>Email: <?php echo htmlspecialchars($doctor['email']); ?></span></div>
            <div><i class="fas fa-calendar-alt"></i> <span>Joined: <?php echo htmlspecialchars($doctor['created_at']); ?></span></div>
        </div>
    </div>
</div>

</body>
</html>
