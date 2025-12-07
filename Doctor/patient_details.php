<?php
session_start();
include 'db.php';

// Make sure doctor is logged in
if (!isset($_SESSION['doctor_id'])) {
    die("Access denied. Please log in.");
}

$doctor_id = $_SESSION['doctor_id'];

// Fetch doctor name
$stmt = $con->prepare("SELECT doctor_name FROM doctors WHERE id = ?");
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$stmt->bind_result($doctor_name);
if (!$stmt->fetch()) {
    die("Doctor not found.");
}
$stmt->close();

// Fetch patients who booked this doctor with join
$stmt = $con->prepare("
    SELECT p.id AS patient_id, p.patient_name, p.contact_no, p.age, p.gender
    FROM appointment_slots a
    JOIN patients p ON a.patient_id = p.id
    WHERE a.doctor_id = ? AND a.status = 'booked'
    ORDER BY p.patient_name
");
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Patients - <?= htmlspecialchars($doctor_name) ?></title>
<?php include 'sidebar.php'; ?>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f5f6fa;
        margin: 0;
        padding: 0;
        display: flex;
    }

    aside {
        width: 250px;
    }

    .main-content {
        flex: 1;
        display: flex;
        justify-content: center;
        padding: 40px 20px;
        box-sizing: border-box;
        min-height: 100vh;
    }

    .content-wrapper {
        width: 100%;
        max-width: 900px;
    }

    h2 {
        color: #2f3640;
        margin-bottom: 20px;
        text-align: center;
    }

    table {
        border-collapse: collapse;
        width: 100%;
        background-color: #ffffff;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    th, td {
        text-align: left;
        padding: 12px;
        border-bottom: 1px solid #ddd;
    }

    th {
        background-color: #3b3b3bff;
        color: white;
    }

    tr:hover {
        background-color: #f1f1f1;
    }

    .view-btn {
        background-color: #3b3b3bff;
        color: white;
        padding: 6px 12px;
        border: none;
        border-radius: 4px;
        text-decoration: none;
        font-size: 14px;
        cursor: pointer;
    }

    .view-btn:hover {
        background-color: #0097e6;
    }

    p {
        font-size: 16px;
        color: #555;
        text-align: center;
    }
</style>
</head>
<body>

<div class="main-content">
    <div class="content-wrapper">
        <h2>Patients booked for Dr. <?= htmlspecialchars($doctor_name) ?></h2>

        <?php if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Patient ID</th>
                <th>Name</th>
                <th>Contact</th>
                <th>Age</th>
                <th>Gender</th>
                <th>Action</th>
            </tr>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['patient_id']) ?></td>
                <td><?= htmlspecialchars($row['patient_name']) ?></td>
                <td><?= htmlspecialchars($row['contact_no']) ?></td>
                <td><?= htmlspecialchars($row['age']) ?></td>
                <td><?= htmlspecialchars($row['gender']) ?></td>
                <td>
                    <a class="view-btn" href="view_patient.php?id=<?= $row['patient_id'] ?>">View Details</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
        <?php else: ?>
        <p>No patients have booked appointments for you yet.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
