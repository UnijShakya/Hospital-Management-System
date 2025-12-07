<?php
session_start();
require 'db.php';

// Ensure patient is logged in
if (!isset($_SESSION['patient_id'])) {
    die("Please log in to view your appointments.");
}

$patient_id = $_SESSION['patient_id'];
$message = "";

// Handle appointment cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_id'])) {
    $cancel_id = intval($_POST['cancel_id']);
    $stmt = $con->prepare("SELECT id FROM appointment_slots WHERE id=? AND patient_id=? AND status='booked'");
    $stmt->bind_param("ii", $cancel_id, $patient_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $stmt = $con->prepare("UPDATE appointment_slots SET patient_id=NULL, status='available' WHERE id=?");
        $stmt->bind_param("i", $cancel_id);
        $stmt->execute();
        $message = "Appointment cancelled successfully.";
    } else {
        $message = "Invalid appointment or cannot cancel.";
    }
}

// Fetch appointments
$stmt = $con->prepare("
    SELECT a.id, a.appointment_date, a.appointment_time, a.status,
           d.doctor_name, d.clinic_address, d.contact_no, d.consultancy_fees
    FROM appointment_slots a
    JOIN doctors d ON a.doctor_id = d.id
    WHERE a.patient_id = ?
    ORDER BY a.appointment_date DESC, a.appointment_time DESC
");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Appointments</title>
<?php include 'patient_sidebar.php'; ?>

<style>
body {
    margin: 0;
    font-family: "Poppins", sans-serif;
    background: #f8f9fa;
    display: flex;
}

.main {
    flex: 1;
    padding: 40px 20px;
    margin-left: 230px;
}

.container {
    max-width: 1000px;
    margin: auto;
    background: #fff;
    padding: 25px 30px;
    border-radius: 8px;
    border: 1px solid #e0e0e0;
}

h2 {
    text-align: center;
    font-size: 24px;
    color: #333;
    margin-bottom: 25px;
}

p.message {
    text-align: center;
    color: #0d6efd;
    font-weight: 500;
    margin-bottom: 15px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
    font-size: 14px;
}

th, td {
    border: 1px solid #dee2e6;
    padding: 10px;
    text-align: center;
}

th {
    background: #f1f1f1;
    font-weight: 600;
}

.status-booked {
    color: #d63333;
    font-weight: 600;
}

.status-available {
    color: #198754;
    font-weight: 600;
}

.cancel-btn {
    background: #dc3545;
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 13px;
}

.cancel-btn:hover {
    background: #b02a37;
}

@media(max-width: 768px) {
    .main { margin-left: 0; padding: 20px; }
    th, td { font-size: 12px; padding: 8px; }
}
</style>
</head>
<body>

<div class="main">
    <div class="container">
        <h2>My Appointments</h2>

        <?php if ($message): ?>
            <p class="message"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <?php if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Doctor</th>
                <th>Clinic</th>
                <th>Contact</th>
                <th>Fees</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['doctor_name']) ?></td>
                <td><?= htmlspecialchars($row['clinic_address']) ?></td>
                <td><?= htmlspecialchars($row['contact_no']) ?></td>
                <td><?= htmlspecialchars($row['consultancy_fees']) ?></td>
                <td><?= htmlspecialchars($row['appointment_date']) ?></td>
                <td><?= htmlspecialchars($row['appointment_time']) ?></td>
                <td class="status-<?= htmlspecialchars($row['status']) ?>">
                    <?= ucfirst($row['status']) ?>
                </td>
                <td>
                    <?php if ($row['status'] === 'booked'): ?>
                        <form method="post" onsubmit="return confirm('Cancel this appointment?')">
                            <input type="hidden" name="cancel_id" value="<?= $row['id'] ?>">
                            <button type="submit" class="cancel-btn">Cancel</button>
                        </form>
                    <?php else: ?>
                        —
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
        <?php else: ?>
            <p style="text-align:center;">No appointments found.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
