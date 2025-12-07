<?php
include('assets/include/dbconfig.php');
$msg = "";

// ------------------- Handle Delete -------------------
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    $res = mysqli_query($con, "SELECT image FROM about_us WHERE id=$id");
    $row = mysqli_fetch_assoc($res);

    if($row['image'] && file_exists($row['image'])){
        unlink($row['image']); // delete image file
    }

    mysqli_query($con, "DELETE FROM about_us WHERE id=$id");
    $msg = "Entry deleted successfully!";
}

// ------------------- Handle Upload -------------------
if(isset($_POST['submit'])){
    $title = mysqli_real_escape_string($con, $_POST['title']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $imagePath = NULL;

    if(isset($_FILES['image']) && $_FILES['image']['name'] != ""){
        $targetDir = "uploads/about/";
        if(!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        $fileName = time() . "_" . basename($_FILES['image']['name']);
        $targetFilePath = $targetDir . $fileName;

        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
        $allowTypes = ['jpg','jpeg','png','gif'];

        if(in_array($fileType, $allowTypes)){
            if(move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)){
                $imagePath = $targetFilePath;
            } else {
                $msg = "Error uploading image.";
            }
        } else {
            $msg = "Only JPG, JPEG, PNG, GIF files are allowed.";
        }
    }

    if($msg == ""){
        $insert = mysqli_query($con, "INSERT INTO about_us (title, description, image) VALUES ('$title', '$description', '$imagePath')");
        if($insert){
            $msg = "About Us content uploaded successfully!";
        } else {
            $msg = "Database error: " . mysqli_error($con);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>About Us Admin</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="p-4">

<h2>About Us Management</h2>

<?php if($msg != "") echo "<div class='alert alert-info'>$msg</div>"; ?>

<!-- Upload Form -->
<form action="" method="post" enctype="multipart/form-data" class="mb-4">
    <div class="mb-3">
        <label for="title" class="form-label">Title</label>
        <input type="text" name="title" id="title" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea name="description" id="description" class="form-control" rows="5" required></textarea>
    </div>

    <div class="mb-3">
        <label for="image" class="form-label">Image (optional)</label>
        <input type="file" name="image" id="image" class="form-control">
    </div>

    <button type="submit" name="submit" class="btn btn-primary">Upload</button>
</form>

<hr>

<!-- Existing Entries -->
<h3>Existing About Us Entries</h3>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Description</th>
            <th>Image</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $res = mysqli_query($con, "SELECT * FROM about_us ORDER BY id DESC");
        while($row = mysqli_fetch_assoc($res)){
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>{$row['title']}</td>";
            echo "<td>{$row['description']}</td>";
            echo "<td>";
            if($row['image']) echo "<img src='admin/{$row['image']}' width='100'>";
            echo "</td>";
            echo "<td>
                    <a href='?delete={$row['id']}' class='btn btn-danger btn-sm' onclick=\"return confirm('Are you sure you want to delete this entry?')\">Delete</a>
                  </td>";
            echo "</tr>";
        }
        ?>
    </tbody>
</table>

</body>
</html>
