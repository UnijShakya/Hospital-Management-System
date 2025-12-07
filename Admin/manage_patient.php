<?php
session_start();
require '../db.php';
include('assets/include/sidebar.php'); 

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$result = $con->query("SELECT * FROM patients ORDER BY id DESC");
?>

<div class="main-content">
    <div class="container-fluid">
        <h2 class="mb-4">👨‍⚕️ Manage Patients</h2>

        <div class="card shadow mb-4">
            <div class="card-body table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">ID</th>
                            <th>Patient Name</th>
                            <th>Email</th>
                            <th>Contact</th>
                            <th class="text-center">Age</th>
                            <th class="text-center">Gender</th>
                            <th>Created At</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="text-center"><?= $row['id'] ?></td>
                                <td><?= htmlspecialchars($row['patient_name']) ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td><?= htmlspecialchars($row['contact_no']) ?></td>
                                <td class="text-center"><?= $row['age'] ?></td>
                                <td class="text-center"><?= $row['gender'] ?></td>
                                <td><?= $row['created_at'] ?></td>
                                <td class="text-center">
                                    <a href="view_patient_details.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">View</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted">No patients found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.main-content {
    margin-left: 260px; /* Adjust for sidebar */
    padding: 40px;
    min-height: 100vh;
    background: #f5f6f8;
    font-family: 'Roboto', sans-serif;
}

.card {
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 3px 12px rgba(0,0,0,0.05);
}

.table-hover tbody tr:hover {
    background-color: #f9fafb;
}

.btn-primary {
    border-radius: 6px;
}

@media(max-width: 991px){
    .main-content { margin-left: 0; padding: 20px; }
}
</style>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
