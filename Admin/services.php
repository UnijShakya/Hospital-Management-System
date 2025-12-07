<?php
include('assets/include/dbconfig.php');
include('assets/include/sidebar.php');

$msg = "";

// --- Add New Service ---
if (isset($_POST['add_service'])) {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $desc = mysqli_real_escape_string($con, $_POST['description']);
    if (mysqli_query($con, "INSERT INTO services (name, description) VALUES ('$name', '$desc')")) {
        $msg = "✅ Service added successfully!";
    } else {
        $msg = "❌ Database error: " . mysqli_error($con);
    }
}

// --- Update Service ---
if (isset($_POST['update_service'])) {
    $id = intval($_POST['service_id']);
    $name = mysqli_real_escape_string($con, $_POST['edit_name']);
    $desc = mysqli_real_escape_string($con, $_POST['edit_description']);
    if (mysqli_query($con, "UPDATE services SET name='$name', description='$desc' WHERE id=$id")) {
        $msg = "✏️ Service updated successfully!";
    } else {
        $msg = "❌ Update failed: " . mysqli_error($con);
    }
}

// --- Delete Service ---
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($con, "DELETE FROM services WHERE id=$id");
    header("Location: services.php");
    exit();
}

// --- Fetch Services ---
$services = mysqli_query($con, "SELECT * FROM services ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Services</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
body {
    background-color: #f5f6f8;
    font-family: 'Poppins', sans-serif;
}
.container {
    margin-left: 250px;
    padding: 40px 30px;
}
.card {
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.08);
}
h2 {
    font-weight: 600;
    color: #0d6efd;
}
.table th {
    background: #0d6efd;
    color: white;
    text-align: center;
}
.table td {
    vertical-align: middle;
}
.btn {
    border-radius: 8px;
}
</style>
</head>
<body>

<div class="container">
    <h2 class="mb-4"><i class="bi bi-gear-fill me-2"></i>Manage Services</h2>

    <?php if($msg): ?>
        <div class="alert alert-info"><?= $msg ?></div>
    <?php endif; ?>

    <!-- Add Service -->
    <div class="card mb-5">
        <div class="card-body">
            <h5 class="card-title mb-3"><i class="bi bi-plus-circle me-2 text-primary"></i>Add New Service</h5>
            <form method="post">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Service Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <button type="submit" name="add_service" class="btn btn-primary mt-3 px-4">
                    <i class="bi bi-save me-1"></i> Save Service
                </button>
            </form>
        </div>
    </div>

    <!-- Services Table -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-3"><i class="bi bi-list-ul me-2 text-primary"></i>All Services</h5>
            <table class="table table-bordered table-hover align-middle">
                <thead>
                    <tr>
                        <th style="width:60px;">#</th>
                        <th>Service Name</th>
                        <th>Description</th>
                        <th style="width:140px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($services) > 0): ?>
                        <?php $i=1; while($row = mysqli_fetch_assoc($services)): ?>
                        <tr>
                            <td class="text-center"><?= $i++ ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars(substr($row['description'], 0, 100)) ?>...</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-warning"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editModal"
                                    data-id="<?= $row['id'] ?>"
                                    data-name="<?= htmlspecialchars($row['name']) ?>"
                                    data-desc="<?= htmlspecialchars($row['description']) ?>">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger"
                                   onclick="return confirm('Delete this service?');">
                                   <i class="bi bi-trash3"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="text-center text-muted py-3">No services available.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Service Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-3">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Service</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form method="post">
        <div class="modal-body">
            <input type="hidden" name="service_id" id="edit_id">
            <div class="mb-3">
                <label class="form-label">Service Name</label>
                <input type="text" name="edit_name" id="edit_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="edit_description" id="edit_description" class="form-control" rows="3" required></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" name="update_service" class="btn btn-success">
                <i class="bi bi-check-circle me-1"></i> Update
            </button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Pass data to Edit Modal
const editModal = document.getElementById('editModal');
editModal.addEventListener('show.bs.modal', event => {
  const button = event.relatedTarget;
  const id = button.getAttribute('data-id');
  const name = button.getAttribute('data-name');
  const desc = button.getAttribute('data-desc');

  document.getElementById('edit_id').value = id;
  document.getElementById('edit_name').value = name;
  document.getElementById('edit_description').value = desc;
});
</script>
</body>
</html>
