<?php
include('assets/include/dbconfig.php');
include('assets/include/navbar.php');

// Fetch the latest About Us entry
$res = mysqli_query($con, "SELECT * FROM about_us ORDER BY id DESC LIMIT 1");
$row = mysqli_fetch_assoc($res);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>About Us</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f1f3f6;
    margin:0; padding:0;
}

/* Hero Section */
.hero {
    width: 100%;
    height: 400px;
    position: relative;
    overflow: hidden;
}

.hero img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    filter: brightness(0.7);
}

.hero h1 {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-size: 3rem;
    font-weight: 700;
    text-align: center;
    text-shadow: 2px 2px 10px rgba(0,0,0,0.7);
}

/* About Section */
.about-section {
    padding: 60px 15px;
    max-width: 1200px;
    margin: -100px auto 30px auto;
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.1);
}

.about-title {
    font-size: 2.5rem;
    font-weight: 700;
    background: linear-gradient(90deg, #0d6efd, #6610f2);
    background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 40px;
    text-align: center;
}

.about-description {
    font-size: 1.1rem;
    line-height: 1.8;
    color: #555;
}

.about-image {
    border-radius: 20px;
    object-fit: cover;
    width: 100%;
    height: 100%;
}

/* Highlight Cards */
.highlight-cards {
    margin-top: 50px;
}

.card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.1);
    transition: transform 0.4s, box-shadow 0.4s;
}

.card:hover {
    transform: translateY(-10px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.15);
}

.card h5 {
    color: #0d6efd;
    margin-top: 15px;
    font-weight: 600;
}

.card p {
    font-size: 0.95rem;
    color: #555;
}
/* Footer Styles */
.footer {
    background: #272727;
    color: #fff;
    padding: 50px 0;
    font-family: 'Poppins', sans-serif;
}
.footer a {
    color: #fff;
    text-decoration: none;
    transition: color 0.3s;
}
.footer a:hover {
    color: #ffc107;
}
.footer .social-icons i {
    font-size: 1.5rem;
    margin-right: 15px;
    transition: transform 0.3s, color 0.3s;
}
.footer .social-icons i:hover {
    color: #ffc107;
    transform: scale(1.2);
}
.footer-title {
    color: #ffbc03ff;
    font-weight: 600;
    margin-bottom: 20px;
    position: relative;
}
.footer-title::after {
    content: '';
    width: 50px;
    height: 3px;
    background: #0d6efd;
    display: block;
    margin-top: 5px;
    border-radius: 3px;
}
.footer-links li {
    margin-bottom: 10px;
    transition: transform 0.3s, color 0.3s;
}
.footer-links li a {
    color: #fff;
    font-weight: 500;
}
.footer-links li:hover a {
    color: #0d6efd;
}
.footer-links li:hover i {
    transform: translateX(5px);
    color: #0d6efd;
}
.footer-links i {
    margin-right: 8px;
    transition: transform 0.3s, color 0.3s;
}

</style>
</head>
<body>

<!-- Hero Banner -->
<div class="hero">
    <img src="assets/uploads/aboutus.jpg" alt="Hero Image">
    <h1>About Our Hospital</h1>
</div>

<!-- About Section -->
<div class="container about-section">
    <?php if($row): ?>
        <h2 class="about-title"><?php echo $row['title']; ?></h2>
        <div class="row align-items-center">
            <?php if($row['image']): ?>
            <div class="col-md-6 mb-4">
                <img src="admin/<?php echo $row['image']; ?>" alt="About Image" class="about-image">
            </div>
            <div class="col-md-6">
                <p class="about-description"><?php echo nl2br($row['description']); ?></p>
            </div>
            <?php else: ?>
            <div class="col-12">
                <p class="about-description"><?php echo nl2br($row['description']); ?></p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Highlights -->
        <div class="row highlight-cards text-center">
            <div class="col-md-4 mb-4">
                <div class="card p-4">
                    <i class="bi bi-hospital" style="font-size: 2.5rem;"></i>
                    <h5>24/7 Services</h5>
                    <p>Round the clock medical assistance for our patients.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card p-4">
                    <i class="bi bi-people" style="font-size: 2.5rem;"></i>
                    <h5>Experienced Staff</h5>
                    <p>Team of highly trained doctors and nurses.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card p-4">
                    <i class="bi bi-heart-pulse" style="font-size: 2.5rem;"></i>
                    <h5>Advanced Equipment</h5>
                    <p>State-of-the-art medical technology for better care.</p>
                </div>
            </div>
        </div>
    <?php else: ?>
        <p class="text-center">No About Us content available.</p>
    <?php endif; ?>
</div>
<!-- Footer -->
<footer class="footer mt-5">
    <div class="container">
        <div class="row text-center text-md-start">
            <div class="col-md-4 mb-4">
                <h5 class="footer-title">Quick Links</h5>
                <ul class="list-unstyled footer-links">
                    <li><i class="bi bi-chevron-right"></i><a href="aboutus.php">About Us</a></li>
                    <li><i class="bi bi-chevron-right"></i><a href="services.php">Services</a></li>
                    <li><i class="bi bi-chevron-right"></i><a href="contact.php">Contact</a></li>
                    <li><i class="bi bi-chevron-right"></i><a href="gallery.php">Gallery</a></li>
                </ul>
            </div>
            <div class="col-md-4 mb-4">
                <h5 class="footer-title">Contact Us</h5>
                <p>Chhakupat<br>Lalitpur, Nepal<br>Phone: 9865466744</p>
            </div>
            <div class="col-md-4 mb-4">
                <h5 class="footer-title">Follow Us</h5>
                <div class="d-flex justify-content-center justify-content-md-start gap-3 social-icons">
                    <a href="#"><i class="bi bi-facebook"></i></a>
                    <a href="#"><i class="bi bi-twitter"></i></a>
                    <a href="#"><i class="bi bi-instagram"></i></a>
                    <a href="#"><i class="bi bi-linkedin"></i></a>
                </div>
            </div>
        </div>
        <hr class="border-light">
        <div class="text-center mt-3">&copy; <?= date("Y") ?> Your Hospital. All Rights Reserved.</div>
    </div>
</footer>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
