<?php
include('assets/include/dbconfig.php');
include('assets/include/navbar.php');

// Fetch all services
$services = mysqli_query($con, "SELECT * FROM services ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Our Services</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f8f9fa;
    margin:0; padding:0;
}

.services-container {
    max-width: 900px;
    margin: 50px auto;
}

.service-card {
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    overflow: hidden;
    transition: transform 0.3s;
}

.service-card:hover {
    transform: translateY(-5px);
}

.service-title {
    padding: 20px;
    font-size: 1.5rem;
    font-weight: 600;
    cursor: pointer;
    background: linear-gradient(90deg, #0d6efd, #6610f2);
    color: white;
    transition: background 0.3s;
}

.service-title:hover {
    background: linear-gradient(90deg, #6610f2, #0d6efd);
}

.service-description {
    display: none;
    padding: 20px;
    font-size: 1rem;
    color: #555;
    border-top: 1px solid #eee;
}
</style>
</head>
<body>

<div class="container services-container">
    <h2 class="mb-4 text-center">Our Services</h2>

    <?php while($row = mysqli_fetch_assoc($services)): ?>
    <div class="service-card">
        <div class="service-title" data-desc="<?php echo htmlspecialchars($row['description']); ?>">
            <?php echo $row['name']; ?>
        </div>
        <div class="service-description"></div>
    </div>
    <?php endwhile; ?>
</div>

<script>
const serviceTitles = document.querySelectorAll('.service-title');

serviceTitles.forEach(title => {
    title.addEventListener('click', () => {
        const descDiv = title.nextElementSibling;
        
        // Toggle description visibility
        if(descDiv.style.display === 'block'){
            descDiv.style.display = 'none';
        } else {
            // Close all other descriptions
            document.querySelectorAll('.service-description').forEach(d => d.style.display = 'none');

            // Show this one
            descDiv.style.display = 'block';
            descDiv.innerHTML = `<p>${title.dataset.desc}</p>`;
            descDiv.scrollIntoView({behavior: "smooth"});
        }
    });
});
</script>

</body>
</html>
