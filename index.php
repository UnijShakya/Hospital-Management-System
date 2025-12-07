<?php
include('assets/include/dbconfig.php');
include('assets/include/navbar.php');

$msg = '';

// Fetch banners
$banners = [];
$result = mysqli_query($con, "SELECT * FROM banners ORDER BY id DESC");
while($row = mysqli_fetch_assoc($result)){
    $banners[] = $row['image'];
}

// Fetch services
$services = [];
$servicesRes = mysqli_query($con, "SELECT * FROM services ORDER BY id ASC");
while($row = mysqli_fetch_assoc($servicesRes)){
    $services[] = $row;
}

// Fetch gallery categories and images
$categories = mysqli_query($con, "SELECT * FROM gallery_categories ORDER BY title ASC");
$images = mysqli_query($con, "SELECT g.id, g.image, g.cat_id, c.title AS cat_title 
                              FROM gallery g 
                              JOIN gallery_categories c ON g.cat_id=c.id
                              ORDER BY g.id DESC");

// Contact form handling
if(isset($_POST['submit'])){
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $subject = mysqli_real_escape_string($con, $_POST['subject']);
    $message = mysqli_real_escape_string($con, $_POST['message']);
    $token = bin2hex(random_bytes(16));

    $query = "INSERT INTO contact_messages (name,email,subject,message,token)
              VALUES ('$name','$email','$subject','$message','$token')";
    if(mysqli_query($con,$query)){
        $msg = "Message sent successfully! Check your reply later using: 
                <a href='view_reply.php?token=$token'>View Your Reply</a>";
    } else {
        $msg = "Error sending message!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Hospital Frontend</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
<style>
body { font-family: 'Poppins', sans-serif; margin:0; background:#f1f3f6; }

/* Banner */
.carousel-inner img { width:100%; height:500px; object-fit:cover; border-radius:15px; box-shadow:0 5px 20px rgba(0,0,0,0.2);}
.carousel-caption { background:rgba(0,0,0,0.5); border-radius:15px; padding:20px 30px; max-width:500px; }
.carousel-caption h5 { font-size:2rem; font-weight:700; color:#fff;}
.carousel-caption p { font-size:1.2rem; color:#fff; margin-bottom:15px;}
.carousel-caption .btn-primary { border-radius:50px; padding:12px 25px; font-weight:600; }

/* Services Section */
.services-section {
    max-width: 1100px;
    margin: 60px auto;
}
.service-card {
    border-radius: 15px;
    transition: all 0.3s ease;
    background: #fff;
    cursor: pointer;
}
.service-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
}
.service-card .icon i {
    transition: transform 0.3s ease;
}
.service-card:hover .icon i {
    transform: scale(1.1);
}
.modal-content {
    border-radius: 15px;
}



/* Gallery */
.gallery-container { max-width:900px; margin:60px auto; }
.filter-btns { text-align:center; margin-bottom:30px; }
.filter-btns button { background:#007BFF; color:#fff; border:none; padding:10px 18px; margin:5px; border-radius:6px; cursor:pointer; transition:all 0.3s ease; font-weight:500;}
.filter-btns button:hover, .filter-btns button.active { background:#0056b3; transform:scale(1.05);}
.gallery-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(180px,1fr)); gap:20px; }
.gallery-item { width:100%; height:230px; object-fit:cover; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.1); cursor:pointer; transition:all 0.5s ease; opacity:1; transform:scale(1);}
.gallery-item.hide { opacity:0; transform:scale(0.8); pointer-events:none; }
.gallery-item:hover { transform:scale(1.05); box-shadow:0 8px 20px rgba(0,0,0,0.2); }

/* Contact */
.contact-full { width:100%; padding:60px 20px; background-color:#f8f9fa; }
.contact-full .row { align-items:center; }
.contact-full img { max-width:100%; border-radius:15px; box-shadow:0 8px 20px rgba(0,0,0,0.1);}
.contact-full h2 { margin-bottom:30px; color:#0d6efd; }
.contact-full .form-control { border-radius:12px; margin-bottom:15px; padding:12px; }
.contact-full .btn-primary { background:#0d6efd; border:none; border-radius:50px; width:100%; padding:12px 20px; font-weight:600; transition:all 0.3s; }
.contact-full .btn-primary:hover { background:#6610f2; transform:scale(1.05); color:#fff; }

/* Footer */
.footer { background:#272727; color:#fff; padding:50px 0; }
.footer a { color:#fff; text-decoration:none; transition:color 0.3s;}
.footer a:hover { color:#ffc107; }
.footer .social-icons i { font-size:1.5rem; margin-right:15px; transition: transform 0.3s, color 0.3s; }
.footer .social-icons i:hover { color:#ffc107; transform:scale(1.2); }
.footer-title { color:#ffbc03ff; font-weight:600; margin-bottom:20px; position:relative; }
.footer-title::after { content:''; width:50px; height:3px; background:#0d6efd; display:block; margin-top:5px; border-radius:3px; }
.footer-links li { margin-bottom:10px; transition: transform 0.3s, color 0.3s; }
.footer-links li a { color:#fff; font-weight:500; }
.footer-links li:hover a { color:#0d6efd; }
.footer-links li:hover i { transform:translateX(5px); color:#0d6efd; }
.footer-links i { margin-right:8px; transition: transform 0.3s, color 0.3s; }

</style>
</head>
<body>

<!-- Banner Carousel -->
<div class="container-fluid p-0 mt-3">
<?php if(count($banners) > 0): ?>
    <div id="bannerCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="3000">
        <div class="carousel-indicators">
            <?php foreach($banners as $index => $img): ?>
                <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="<?= $index ?>" <?= $index==0?'class="active"':'' ?>></button>
            <?php endforeach; ?>
        </div>
        <div class="carousel-inner">
            <?php foreach($banners as $index => $img): ?>
            <div class="carousel-item <?= $index==0?'active':'' ?>">
                <img src="admin/<?= htmlspecialchars($img) ?>" class="d-block w-100" alt="Banner">
                <div class="carousel-caption d-none d-md-block text-start" style="left:50px; right:auto;">
                    <h5>Welcome to Our Hospital</h5>
                    <p>We care for your health with excellence.</p>
                    <a href="patient/login_patient.php" class="btn btn-primary btn-lg shadow">Book Appointment</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>
<?php endif; ?>
</div>

<!-- Services Section -->
<div class="container services-section">
    <h2 class="mb-5 text-center text-primary">Our Services</h2>

    <div class="row g-4">
        <?php foreach($services as $index => $service): ?>
            <div class="col-lg-4 col-md-6">
                <div class="card service-card border-0 shadow-sm h-100"
                     data-bs-toggle="modal" data-bs-target="#serviceModal<?= $index ?>">
                    <div class="card-body text-center p-4">
                        <div class="icon mb-3" style="font-size:40px; color:#0d6efd;">
                            <i class="bi bi-heart-pulse"></i>
                        </div>
                        <h5 class="card-title fw-semibold text-primary">
                            <?= htmlspecialchars($service['name']) ?>
                        </h5>
                        <p class="card-text text-muted mt-3" style="font-size:0.95rem;">
                            <?= htmlspecialchars(substr($service['description'], 0, 100)) ?>...
                        </p>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="serviceModal<?= $index ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content border-0 shadow-lg">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title"><?= htmlspecialchars($service['name']) ?></h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <p class="text-muted mb-0" style="font-size:1rem; line-height:1.6;">
                                <?= nl2br(htmlspecialchars($service['description'])) ?>
                            </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>



<!-- Gallery Section -->
<div class="gallery-container">
    <h2 class="text-center mb-4 text-primary">Gallery</h2>
    <div class="filter-btns">
        <button class="filter-btn active" onclick="filterImages('all')">All</button>
        <?php while($cat = mysqli_fetch_assoc($categories)): ?>
            <button class="filter-btn" onclick="filterImages('cat-<?= $cat['id'] ?>')"><?= htmlspecialchars($cat['title']) ?></button>
        <?php endwhile; ?>
    </div>
    <div class="gallery-grid">
        <?php while($img = mysqli_fetch_assoc($images)): ?>
            <img src="admin/uploads/gallery/<?= htmlspecialchars($img['image']) ?>" class="gallery-item cat-<?= $img['cat_id'] ?>">
        <?php endwhile; ?>
    </div>
</div>

<!-- Contact Section -->
<div class="contact-full">
    <div class="container">
        <div class="row">
            <div class="col-md-6 text-center mb-4 mb-md-0">
                <img src="assets/uploads/contact.png" alt="Contact Avatar">
            </div>
            <div class="col-md-6">
                <h2>Contact Us</h2>
                <?php if($msg) echo "<div class='alert alert-success'>$msg</div>"; ?>
                <form action="" method="POST">
                    <input type="text" name="name" class="form-control" placeholder="Your Name" required>
                    <input type="email" name="email" class="form-control" placeholder="Your Email" required>
                    <input type="text" name="subject" class="form-control" placeholder="Subject" required>
                    <textarea name="message" class="form-control" rows="5" placeholder="Type your message..." required></textarea>
                    <button type="submit" name="submit" class="btn btn-primary">Send Message</button>
                </form>
            </div>
        </div>
    </div>
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
<script>
function toggleDesc(card){
    const desc = card.querySelector('.service-desc');
    desc.style.display = desc.style.display === 'block' ? 'none' : 'block';
}

// Gallery filter
function filterImages(category) {
    const items = document.querySelectorAll('.gallery-item');
    const buttons = document.querySelectorAll('.filter-btn');
    buttons.forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');

    items.forEach(item => {
        if(category==='all' || item.classList.contains(category)){
            item.classList.remove('hide');
        } else {
            item.classList.add('hide');
        }
    });
}
</script>
</body>
</html>
