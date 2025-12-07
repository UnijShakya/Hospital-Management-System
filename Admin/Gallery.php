<?php
include('db.php'); 
include('assets/include/sidebar.php'); 
$msg = '';

// Fetch categories
$categoryList = mysqli_query($con, "SELECT id, title FROM gallery_categories ORDER BY title ASC");

// Handle upload
if(isset($_POST['submit'])){
    $categoryName = trim($_POST['category']);
    $selectedCat = intval($_POST['existing_category']);
    
    if($categoryName == '' && $selectedCat == 0){
        $msg = "⚠ Please enter a category name or select an existing category!";
        $alertClass = "alert-warning";
    } elseif(isset($_FILES['image']) && $_FILES['image']['error'] == 0){

        // Determine category
        if($selectedCat > 0){
            $cat_id = $selectedCat;
        } else {
            $stmt = $con->prepare("SELECT id FROM gallery_categories WHERE title=? LIMIT 1");
            $stmt->bind_param("s", $categoryName);
            $stmt->execute();
            $stmt->store_result();
            if($stmt->num_rows > 0){
                $stmt->bind_result($cat_id);
                $stmt->fetch();
            } else {
                $stmtInsert = $con->prepare("INSERT INTO gallery_categories (title) VALUES (?)");
                $stmtInsert->bind_param("s", $categoryName);
                $stmtInsert->execute();
                $cat_id = $stmtInsert->insert_id;
            }
        }

        // Upload image
        $img_name = $_FILES['image']['name'];
        $tmp_name = $_FILES['image']['tmp_name'];
        $ext = pathinfo($img_name, PATHINFO_EXTENSION);
        $new_name = uniqid() . "." . $ext;
        $upload_dir = "uploads/gallery/";

        if(!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        if(move_uploaded_file($tmp_name, $upload_dir . $new_name)){
            $stmtImg = $con->prepare("INSERT INTO gallery (cat_id, image) VALUES (?, ?)");
            $stmtImg->bind_param("is", $cat_id, $new_name);
            $msg = $stmtImg->execute() ? "✅ Image uploaded successfully!" : "❌ Database error while inserting image!";
            $alertClass = "alert-success";
        } else {
            $msg = "❌ Failed to upload image!";
            $alertClass = "alert-danger";
        }

    } else {
        $msg = "⚠ Please select an image!";
        $alertClass = "alert-warning";
    }
}

// Handle delete
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    $res = mysqli_query($con, "SELECT image FROM gallery WHERE id=$id");
    if($res && mysqli_num_rows($res) > 0){
        $row = mysqli_fetch_assoc($res);
        if(file_exists("uploads/gallery/".$row['image'])) unlink("uploads/gallery/".$row['image']);
        mysqli_query($con, "DELETE FROM gallery WHERE id=$id");
        $msg = "✅ Image deleted successfully!";
        $alertClass = "alert-success";
    }
}

// Fetch images
$images = mysqli_query($con, "SELECT g.id, g.image, g.uploaded_at, c.title AS cat_title 
                              FROM gallery g 
                              JOIN gallery_categories c ON g.cat_id=c.id 
                              ORDER BY g.id DESC");
?>

<div class="main-content">
    <div class="container-fluid">

        <?php if($msg) echo "<div class='alert {$alertClass}'>{$msg}</div>"; ?>

        <!-- Upload Card -->
        <div class="card mb-4">
            <div class="card-header fw-semibold">📤 Upload Image</div>
            <div class="card-body">
                <form method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Create New Category</label>
                        <input type="text" name="category" class="form-control" placeholder="Enter new category">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Or Select Existing Category</label>
                        <select name="existing_category" class="form-select">
                            <option value="0">-- Select Category --</option>
                            <?php while($cat = mysqli_fetch_assoc($categoryList)): ?>
                                <option value="<?= $cat['id']; ?>"><?= htmlspecialchars($cat['title']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Select Image</label>
                        <input type="file" name="image" class="form-control" accept="image/*" required>
                    </div>

                    <button type="submit" name="submit" class="btn btn-primary w-100">Upload</button>
                </form>
            </div>
        </div>

        <!-- Images Table Card -->
        <div class="card">
            <div class="card-header fw-semibold">🖼 Uploaded Images</div>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Category</th>
                            <th>Image</th>
                            <th>Uploaded At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $count = 1; while($row = mysqli_fetch_assoc($images)): ?>
                        <tr>
                            <td><?= $count++; ?></td>
                            <td><?= htmlspecialchars($row['cat_title']); ?></td>
                            <td><img src="uploads/gallery/<?= $row['image']; ?>" class="table-img"></td>
                            <td><?= date('d M Y, H:i', strtotime($row['uploaded_at'])); ?></td>
                            <td>
                                <a href="?delete=<?= $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<style>
.main-content {
    margin-left: 260px; /* sidebar width */
    padding: 40px;
    min-height: 100vh;
    background: #f5f6f8;
}

.card {
    border-radius: 12px;
    box-shadow: 0 3px 12px rgba(0,0,0,0.05);
    border: 1px solid #e5e7eb;
}

.card:hover {
    box-shadow: 0 5px 18px rgba(0,0,0,0.08);
}

.card-header {
    background: #fff;
    border-bottom: 1px solid #e5e7eb;
    font-weight: 600;
    font-size: 1.1rem;
}

.form-control, .form-select {
    border-radius: 8px;
    border: 1px solid #d1d5db;
}

.form-control:focus, .form-select:focus {
    border-color: #4b5563;
    box-shadow: none;
}

.btn-primary {
    background: #444;
    border: none;
    border-radius: 6px;
}

.btn-primary:hover {
    background: #000;
}

.btn-danger {
    border-radius: 6px;
}

.table-img {
    width: 100px;
    height: 60px;
    object-fit: cover;
    border-radius: 6px;
}

.table-hover tbody tr:hover {
    background-color: #f9fafb;
}

.alert {
    border-radius: 10px;
    font-size: 15px;
    margin-bottom: 1.2rem;
    text-align: left;
}

@media(max-width: 991px){
    .main-content { margin-left: 0; padding: 20px; }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
