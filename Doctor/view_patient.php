<?php
session_start();
include 'db.php';

if (!isset($_SESSION['doctor_id'])) {
    die("Access denied. Please log in.");
}

$doctor_id = $_SESSION['doctor_id'];
$patient_id = intval($_GET['id'] ?? 0);
if ($patient_id <= 0) die("Invalid patient ID.");

$stmt = $con->prepare("SELECT * FROM patients WHERE id = ?");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$patient = $stmt->get_result()->fetch_assoc();
if (!$patient) die("Patient not found.");

$stmt = $con->prepare("
    SELECT appointment_date, appointment_time, status
    FROM appointment_slots
    WHERE doctor_id = ? AND patient_id = ?
    ORDER BY appointment_date DESC, appointment_time DESC
");
$stmt->bind_param("ii", $doctor_id, $patient_id);
$stmt->execute();
$appointments = $stmt->get_result();

$stmt = $con->prepare("
    SELECT id, report_date, report_title, report_file
    FROM patient_reports
    WHERE patient_id = ? AND doctor_id = ?
    ORDER BY report_date DESC
");
$stmt->bind_param("ii", $patient_id, $doctor_id);
$stmt->execute();
$reports = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Patient Details - <?= htmlspecialchars($patient['patient_name']) ?></title>
<?php include 'sidebar.php'; ?>
<style>
/* Main content layout */
body {
    font-family: 'Roboto', sans-serif;
    background-color: #f5f6f8;
    margin: 0;
    padding: 0;
    display: flex;
    min-height: 100vh;
}

aside {
    width: 250px;
}

/* Centered main content */
.main-content {
    flex: 1;
    display: flex;
    justify-content: center;
    padding: 40px 20px;
    min-height: 100vh;
    
}

.content-wrapper {
    width: 100%;
    max-width: 950px;
}

/* Cards */
.card {
    background-color: #fff;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 3px 12px rgba(0,0,0,0.05);
    padding: 25px;
    margin-bottom: 30px;
}

/* Headings */
h2, h3, {
    color: #2c3e50;
    margin-bottom: 20px;
}

h3, h4 {
    border-bottom: 2px solid #2e2e2eff;
    padding-bottom: 5px;
}

/* Patient info rows */
.detail-row {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    margin-bottom: 15px;
}

.label {
    font-weight: 600;
    color: #555;
    flex: 1;
    min-width: 120px;
}

.value {
    color: #2d3436;
    flex: 2;
}

/* Tables */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
    box-shadow: 0 3px 6px rgba(0,0,0,0.05);
    border-radius: 6px;
    overflow: hidden;
}

th, td {
    padding: 12px 15px;
    border-bottom: 1px solid #e1e1e1;
    text-align: left;
}

th {
    background-color: #2b2b2bff;
    color: #fff;
    font-weight: 600;
    text-transform: uppercase;
}

tr:hover {
    background-color: #f9fafb;
    transition: 0.3s;
}

/* Buttons */
.btn {
    font-size: 14px;
    padding: 8px 16px;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    text-decoration: none;
    transition: 0.3s;
}

.btn-primary {
    background-color: #3498db;
    color: #fff;
}

.btn-primary:hover {
    background-color: #2980b9;
}

.btn-secondary {
    background-color: #2d3436;
    color: #fff;
}

.btn-secondary:hover {
    background-color: #636e72;
}

/* Badges */
.badge {
    font-size: 0.85rem;
    padding: 6px 10px;
    border-radius: 6px;
}

.badge.bg-success { background-color: #28a745; color: #fff; }
.badge.bg-danger { background-color: #dc3545; color: #fff; }
.badge.bg-secondary { background-color: #6c757d; color: #fff; }
.badge.bg-warning { background-color: #ffc107; color: #212529; }

/* Responsive */
@media(max-width: 991px) {
    .main-content {
        margin-left: 0;
        padding: 20px;
    }
}

</style>
</head>
<body>

<div class="main-content">
    <div class="content-wrapper">
        <h2>Patient Details</h2>

        <div class="detail-row"><div class="label">Name:</div><div class="value"><?= htmlspecialchars($patient['patient_name']) ?></div></div>
        <div class="detail-row"><div class="label">Email:</div><div class="value"><?= htmlspecialchars($patient['email']) ?></div></div>
        <div class="detail-row"><div class="label">Contact:</div><div class="value"><?= htmlspecialchars($patient['contact_no']) ?></div></div>
        <div class="detail-row"><div class="label">Age:</div><div class="value"><?= htmlspecialchars($patient['age']) ?></div></div>
        <div class="detail-row"><div class="label">Gender:</div><div class="value"><?= htmlspecialchars($patient['gender']) ?></div></div>

        <h3>Appointment History</h3>
        <?php if ($appointments->num_rows > 0): ?>
        <table>
            <tr><th>Date</th><th>Time</th><th>Status</th></tr>
            <?php while($row = $appointments->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['appointment_date']) ?></td>
                <td><?= htmlspecialchars($row['appointment_time']) ?></td>
                <td><?= htmlspecialchars(ucfirst($row['status'])) ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
        <?php else: ?>
        <p>No appointments found for this patient.</p>
        <?php endif; ?>

        <h3>Patient Reports</h3>
        <?php if ($reports->num_rows > 0): ?>
        <table>
            <tr><th>Date</th><th>Title</th><th>Action</th></tr>
            <?php while($row = $reports->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['report_date']) ?></td>
                <td><?= htmlspecialchars($row['report_title']) ?></td>
                <td>
                    <a class="btn" href="view_report.php?id=<?= $row['id'] ?>">View</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
        <?php else: ?>
        <p>No reports found for this patient.</p>
        <?php endif; ?>

        <a class="back-btn" href="patient_details.php">Back to Patients</a>
    </div>
</div>

</body>
</html>
