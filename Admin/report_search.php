<?php
require 'db.php';

// ===================== HANDLE DELETE =====================
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $stmt = $con->prepare("DELETE FROM patient_reports WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: report_search.php");
    exit();
}

// ===================== HANDLE SEARCH =====================
$search = trim($_GET['search'] ?? '');
if ($search === '') {
    $stmt = $con->prepare("SELECT * FROM patient_reports ORDER BY report_date DESC");
} else {
    $stmt = $con->prepare("SELECT * FROM patient_reports WHERE patient_name LIKE CONCAT('%', ?, '%') ORDER BY report_date DESC");
    $stmt->bind_param("s", $search);
}
$stmt->execute();
$reports = $stmt->get_result();

// ===================== INCLUDE SIDEBAR =====================
include 'assets/include/sidebar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Patient Reports</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
body {
    font-family:'Segoe UI',sans-serif;
    margin-left:260px;
    padding:20px;
    background:#f8f9fa;
}
table {
    background:#fff;
}
th {
    background:#0d6efd;
    color:#fff;
    text-align:center;
}
td {
    text-align:center;
    vertical-align:middle;
}
tr:hover {
    background:#f1f3f5;
}
.action-icons a {
    margin: 0 5px;
    font-size: 1.1rem;
}
</style>
</head>
<body>

<div class="container-fluid">
    <h3>Patient Reports</h3>

    <form method="GET" class="mb-3 w-50">
        <input type="text" name="search" class="form-control" placeholder="Search patient name" value="<?=htmlspecialchars($search)?>">
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Patient Name</th>
                    <th>Age</th>
                    <th>Gender</th>
                    <th>Contact</th>
                    <th>Address</th>
                    <th>Doctor</th>
                    <th>Report Title</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($reports->num_rows == 0): ?>
                    <tr><td colspan="10">No reports found.</td></tr>
                <?php else: $i=1; while($r = $reports->fetch_assoc()): ?>
                <tr>
                    <td><?=$i++?></td>
                    <td><?=htmlspecialchars($r['patient_name'])?></td>
                    <td><?=htmlspecialchars($r['age'])?></td>
                    <td><?=htmlspecialchars($r['gender'])?></td>
                    <td><?=htmlspecialchars($r['contact'])?></td>
                    <td><?=htmlspecialchars($r['address'])?></td>
                    <td><?=htmlspecialchars($r['doctor_name'])?></td>
                    <td><?=htmlspecialchars($r['report_title'])?></td>
                    <td><?=htmlspecialchars($r['report_date'])?></td>
                    <td class="action-icons">
                        <a href="report_view.php?id=<?=$r['id']?>" title="View" class="text-primary"><i class="bi bi-eye"></i></a>
                        <a href="report_edit.php?id=<?=$r['id']?>" title="Edit" class="text-warning"><i class="bi bi-pencil-square"></i></a>
                        <a href="?delete_id=<?=$r['id']?>" title="Delete" class="text-danger" onclick="return confirm('Delete this report?')"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
                <?php endwhile; endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
