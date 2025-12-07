<?php
include('db.php'); 
include('assets/include/sidebar.php'); // your sidebar
$msg = '';
$alertClass = '';

// Handle Add Specialization
if (isset($_POST['submit'])) {
    $name = trim($_POST['specialization_name']);
    if ($name == '') {
        $msg = "⚠ Please enter specialization name!";
        $alertClass = "alert-warning";
    } else {
        $sql = "INSERT INTO doctor_specialization (specialization_name) VALUES (?)";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("s", $name);
        if ($stmt->execute()) {
            $msg = "✅ Specialization added successfully!";
            $alertClass = "alert-success";
        } else {
            $msg = "❌ Error: " . $stmt->error;
            $alertClass = "alert-danger";
        }
    }
}

// Handle Delete Specialization
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $sql = "DELETE FROM doctor_specialization WHERE id=?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $msg = "🗑 Specialization deleted successfully!";
        $alertClass = "alert-info";
    } else {
        $msg = "❌ Error deleting specialization!";
        $alertClass = "alert-danger";
    }
}

// Fetch all specializations
$result = mysqli_query($con, "SELECT * FROM doctor_specialization ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Doctor Specialization Management</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    background: #f5f6f8;
    font-family: "Poppins", sans-serif;
    margin: 0;
}

/* Layout with sidebar */
.main-content {
    margin-left: 260px; /* adjust if your sidebar width differs */
    padding: 40px;
    min-height: 100vh;
}

/* Card Styling */
.card {
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    box-shadow: 0 3px 12px rgba(0, 0, 0, 0.05);
    transition: all 0.2s ease-in-out;
}
.card:hover {
    box-shadow: 0 5px 18px rgba(0, 0, 0, 0.08);
}

.card-header {
    background: #fff;
    border-bottom: 1px solid #e5e7eb;
    font-weight: 600;
    font-size: 1.1rem;
    padding: 16px 20px;
}

/* Form */
form .form-control {
    border-radius: 8px;
    border: 1px solid #d1d5db;
}
form .form-control:focus {
    border-color: #4b5563;
    box-shadow: none;
}

/* Buttons */
.btn-primary {
    background: #444;
    border: none;
    border-radius: 6px;
    transition: all 0.2s ease;
}
.btn-primary:hover {
    background: #000;
}

.btn-danger {
    border-radius: 6px;
}

/* Table */
.table {
    border-radius: 8px;
    overflow: hidden;
}
.table thead {
    background: #f1f1f1;
    color: #111;
    font-weight: 600;
}
.table-hover tbody tr:hover {
    background-color: #f9fafb;
}

/* Alerts */
.alert {
    border-radius: 10px;
    font-size: 15px;
    margin-bottom: 1.2rem;
}

@media (max-width: 991px) {
    .main-content {
        margin-left: 0;
        padding: 20px;
    }
}
</style>
</head>
<body>

<div class="main-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="card">
                    <div class="card-header">
                        ⚕ Manage Doctor Specializations
                    </div>
                    <div class="card-body">

                        <?php if($msg != ''): ?>
                            <div class="alert <?= $alertClass; ?>"><?= $msg; ?></div>
                        <?php endif; ?>

                        <!-- Add Form -->
                        <form method="post" class="mb-4">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Specialization Name</label>
                                <input type="text" name="specialization_name" class="form-control" placeholder="e.g. Cardiology" required>
                            </div>
                            <button type="submit" name="submit" class="btn btn-primary">Add Specialization</button>
                        </form>

                        <!-- List Specializations -->
                        <h6 class="fw-semibold mb-3">📋 Existing Specializations</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th width="60">#</th>
                                        <th>Specialization Name</th>
                                        <th width="130" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $i = 1;
                                    if (mysqli_num_rows($result) > 0) {
                                        while($row = mysqli_fetch_assoc($result)) { ?>
                                            <tr>
                                                <td><?php echo $i++; ?></td>
                                                <td><?php echo htmlspecialchars($row['specialization_name']); ?></td>
                                                <td class="text-center">
                                                    <a href="?delete=<?php echo $row['id']; ?>" 
                                                       class="btn btn-danger btn-sm"
                                                       onclick="return confirm('Are you sure you want to delete this specialization?');">
                                                       Delete
                                                    </a>
                                                </td>
                                            </tr>
                                    <?php } } else { ?>
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-3">
                                                No specializations found.
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
