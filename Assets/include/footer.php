<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
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

</body>
</html>