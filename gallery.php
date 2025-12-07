<?php
include('db.php');
include('assets/include/navbar.php'); // Keep navbar as-is
// Fetch categories
$categories = mysqli_query($con, "SELECT * FROM gallery_categories ORDER BY title ASC");
// Fetch all images with category id
$images = mysqli_query($con, "SELECT g.id, g.image, g.cat_id, c.title AS cat_title 
                              FROM gallery g 
                              JOIN gallery_categories c ON g.cat_id=c.id
                              ORDER BY g.id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gallery</title>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<style>
body {
    font-family: 'Roboto', sans-serif;
    background: #f5f7fa;
    margin: 0;
    color: #333;
    
}

/* Container */
.container {
    max-width: 900px;
    margin: 0 auto;
}

/* Heading */
h1 {
    text-align: center;
    margin-bottom: 30px;
    color: #007BFF;
}

/* Filter Buttons */
.filter-btns {
    text-align: center;
    margin-bottom: 30px;
}
.filter-btns button {
    background: #007BFF;
    color: #fff;
    border: none;
    padding: 10px 18px;
    margin: 5px;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 500;
}
.filter-btns button:hover, .filter-btns button.active {
    background: #0056b3;
    transform: scale(1.05);
}

/* Gallery grid */
.gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 20px;
}
.gallery-item {
    width: 100%;
    height: 230px;
    object-fit: cover;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    cursor: pointer;
    transition: all 0.5s ease;
    opacity: 1;
    transform: scale(1);
}
.gallery-item.hide {
    opacity: 0;
    transform: scale(0.8);
    pointer-events: none;
}
.gallery-item:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 20px rgba(0,0,0,0.2);
}

/* Responsive */
@media(max-width:600px){
    .gallery-grid { grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); }
    .gallery-item { height: 180px; }
}
</style>
</head>
<body>

<div class="container mt-4">
    <h1>Gallery</h1>

    <!-- Filter Buttons -->
    <div class="filter-btns">
        <button class="filter-btn active" onclick="filterImages(event, 'all')">All</button>
        <?php while($cat = mysqli_fetch_assoc($categories)): ?>
            <button class="filter-btn" onclick="filterImages(event, 'cat-<?= $cat['id'] ?>')"><?= htmlspecialchars($cat['title']) ?></button>
        <?php endwhile; ?>
    </div>

    <!-- Gallery Grid -->
    <div class="gallery-grid">
        <?php while($img = mysqli_fetch_assoc($images)): ?>
            <img src="http://localhost/npl/Admin/uploads/gallery/<?= htmlspecialchars($img['image']) ?>" 
                 class="gallery-item cat-<?= $img['cat_id'] ?>" alt="<?= htmlspecialchars($img['cat_title']) ?>">
        <?php endwhile; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function filterImages(event, category) {
    const items = document.querySelectorAll('.gallery-item');
    const buttons = document.querySelectorAll('.filter-btn');

    // Highlight active button
    buttons.forEach(btn => btn.classList.remove('active'));
    event.currentTarget.classList.add('active');

    // Show/hide images
    items.forEach(item => {
        if(category === 'all' || item.classList.contains(category)){
            item.classList.remove('hide');
        } else {
            item.classList.add('hide');
        }
    });
}
</script>

</body>
</html>
