<?php
// Get current page filename
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NPL Hospital</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

  <style>
    /* Navbar Styling */
    .navbar {
      background: #ffffff;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    /* Brand / Logo */
    .navbar-brand {
      font-weight: bold;
      font-size: 1.6rem;
      color: #007bff !important;
      letter-spacing: 1px;
    }

    /* Nav Links */
    .nav-link {
      color: #333 !important;
      font-weight: 500;
      margin-left: 15px;
      transition: 0.3s;
    }

    .nav-link:hover, .nav-link.active {
      color: #007bff !important;
    }

    /* Appointment Button */
    .btn-appointment {
      background: #007bff;
      color: #fff;
      font-weight: bold;
      border-radius: 25px;
      padding: 8px 20px;
      margin-left: 20px;
      transition: all 0.3s;
    }

    /* Active Appointment Button */
    .btn-appointment.active {
      background: #0056b3 !important;
      color: #fff !important;
    }

    .btn-appointment:hover {
      background: #0056b3;
      transform: scale(1.05);
      box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }

    /* Mobile Menu */
    .navbar-toggler {
      border: none;
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg sticky-top">
  <div class="container">
    <!-- Hospital Logo / Name -->
    <a class="navbar-brand" href="index.php">NPL | Hospital</a>

    <!-- Mobile Toggle -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
            data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" 
            aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Navbar Links -->
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link <?php if($currentPage=='index.php') echo 'active'; ?>" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link <?php if($currentPage=='aboutus.php') echo 'active'; ?>" href="aboutus.php">About Us</a></li>
        <li class="nav-item"><a class="nav-link <?php if($currentPage=='services.php') echo 'active'; ?>" href="services.php">Services</a></li>
        <li class="nav-item"><a class="nav-link <?php if($currentPage=='contact.php') echo 'active'; ?>" href="contact.php">Contact</a></li>
        <li class="nav-item"><a class="nav-link <?php if($currentPage=='login.php') echo 'active'; ?>" href="Login.php">Login</a></li>
        <li class="nav-item">
          <a class="btn btn-appointment <?php if($currentPage=='patient/login_patient.php') echo 'active'; ?>" href="patient/login_patient.php">Book Appointment</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
