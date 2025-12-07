<?php include('assets/include/navbar.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login Selection</title>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
<style>
body {
    font-family: 'Roboto', sans-serif;
    margin: 0;
    min-height: 100vh;
    background: #e9f1f7;
    position: relative;
}

/* Navbar adjustment */
.navbar {
    z-index: 10;
}

/* Floating shapes in the background */
body::before, body::after {
    content: "";
    position: absolute;
    border-radius: 50%;
    background: rgba(52, 152, 219, 0.15);
    width: 500px;
    height: 500px;
    top: -150px;
    left: -150px;
    z-index: 0;
    animation: float 6s ease-in-out infinite;
}
body::after {
    background: rgba(46, 204, 113, 0.15);
    width: 600px;
    height: 600px;
    bottom: -200px;
    right: -200px;
    top: auto;
    left: auto;
    animation-delay: 3s;
}
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}

/* Container for cards */
.container {
    display: flex;
    justify-content: center;
    gap: 0.5px;
    flex-wrap: wrap;
    z-index: 1;
    position: relative;
    padding-top: 1px; /* Push below navbar */
}

/* Card design */
.card {
    background: #ffffff;
    width: 220px;
    height: 320px;
    border-radius: 20px;
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    display: flex;
    flex-direction: column;
    justify-content: center; /* Center content vertically */
    align-items: center;     /* Center content horizontally */
    padding: 20px;
    margin: 50px;
    cursor: pointer;
    transition: transform 0.5s, box-shadow 0.5s;
    position: relative;
    overflow: hidden;
}
.card:hover {
    transform: translateY(-15px) scale(1.05);
    box-shadow: 0 25px 50px rgba(0,0,0,0.2);
}
.card img {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 50%;
    margin-bottom: 20px;
    transition: transform 0.5s;
}
.card:hover img {
    transform: scale(1.1);
}
.card h3 {
    font-size: 22px;
    color: #333;
    margin: 0;
    margin-top: 10px;
    text-align: center;
}
.card.patient { border-top: 5px solid #27ae60; }
.card.doctor { border-top: 5px solid #2980b9; }
.card.admin { border-top: 5px solid #c0392b; }
@keyframes cardFloat {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-5px); }
}
.card { animation: cardFloat 3s ease-in-out infinite; }

@media(max-width: 768px) {
    .container {
        flex-direction: column;
        align-items: center;
        padding-top: 50px;
    }
}
</style>
</head>
<body>

<div class="container">
    <div class="card patient" onclick="window.location.href='patient/login_patient.php'">
        <img src="Assets/uploads/patient.png" alt="Patient">
        <h3>Patient Login</h3>
    </div>
    <div class="card doctor" onclick="window.location.href='doctor/login_doctor.php'">
        <img src="Assets/uploads/doctor-male.png" alt="Doctor">
        <h3>Doctor Login</h3>
    </div>
    <div class="card admin" onclick="window.location.href='admin/login.php'">
        <img src="Assets/uploads/admin-settings-male.png" alt="Admin">
        <h3>Admin Login</h3>
    </div>
</div>

</body>
</html>
