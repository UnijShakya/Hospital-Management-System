<?php
include('assets/include/dbconfig.php'); 
include('assets/include/sidebar.php');  
$msg = "";

// Folder to store banners
$targetDir = "uploads/";
if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

// Upload Banner
if(isset($_POST['submit'])){
    $fileName = basename($_FILES["banner"]["name"]);
    $targetFilePath = $targetDir . time() . "_" . $fileName;
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
    $allowTypes = ['jpg','jpeg','png','gif'];

    if(in_array(strtolower($fileType), $allowTypes)){
        if(move_uploaded_file($_FILES["banner"]["tmp_name"], $targetFilePath)){
            $insert = mysqli_query($con, "INSERT INTO banners (image) VALUES ('$targetFilePath')");
            $msg = $insert ? "✅ Banner uploaded successfully!" : "❌ Database error: ".mysqli_error($con);
            $alertClass = $insert ? "alert-success" : "alert-danger";
        } else { 
            $msg = "❌ Error uploading file. Check folder permissions."; 
            $alertClass = "alert-danger";
        }
    } else { 
        $msg = "⚠ Only JPG, JPEG, PNG, GIF files are allowed."; 
        $alertClass = "alert-warning";
    }
}

// Remove Banner
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    $res = mysqli_query($con, "SELECT image FROM banners WHERE id=$id");
    if($res && mysqli_num_rows($res) > 0){
        $row = mysqli_fetch_assoc($res);
        if(file_exists($row['image'])) unlink($row['image']);
        mysqli_query($con, "DELETE FROM banners WHERE id=$id");
        $msg = "✅ Banner removed successfully!";
        $alertClass = "alert-success";
    }
}

// Fetch banners
$banners = mysqli_query($con, "SELECT * FROM banners ORDER BY id DESC");
?>

<div class="main-content">
    <div class="container-fluid">

        <h2 class="mb-4">Upload Banner</h2>

        <?php if($msg) echo "<div class='alert {$alertClass}'>{$msg}</div>"; ?>

        <!-- Upload Card -->
        <div class="card mb-4">
            <div class="card-header fw-semibold">📤 Upload New Banner</div>
            <div class="card-body">
                <form method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Select Banner Image</label>
                        <input type="file" name="banner" class="form-control" accept="image/*" required>
                    </div>
                    <button type="submit" name="submit" class="btn btn-primary w-100">Upload</button>
                </form>
            </div>
        </div>

        <!-- Banners Table -->
        <div class="card">
            <div class="card-header fw-semibold">🖼 Uploaded Banners</div>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Banner</th>
                            <th>Uploaded At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $count = 1; while($row = mysqli_fetch_assoc($banners)): ?>
                        <tr>
                            <td><?= $count++; ?></td>
                            <td><img src="<?= $row['image']; ?>" class="table-img"></td>
                            <td><?= date('d M Y, H:i', strtotime($row['uploaded_at'])); ?></td>
                            <td>
                                <a href="?delete=<?= $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Remove</a>
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

.card-header {
    background: #fff;
    border-bottom: 1px solid #e5e7eb;
    font-weight: 600;
    font-size: 1.1rem;
}

.form-control {
    border-radius: 8px;
    border: 1px solid #d1d5db;
}

.form-control:focus {
    border-color: #4b5563;
    box-shadow: none;
}

.btn-primary {
    border-radius: 6px;
}

.btn-primary:hover {
    background: #000;
}

.btn-danger {
    border-radius: 6px;
}

.table-img {
    width: 150px;
    height: auto;
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
