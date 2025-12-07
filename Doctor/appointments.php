<?php
include 'db.php';
include 'sidebar.php';

// Replace with dynamic doctor ID (from session or request)
$doctor_id = 1;

// Fetch doctor's name
$stmt = $con->prepare("SELECT doctor_name FROM doctors WHERE id = ?");
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$stmt->bind_result($doctor_name);
$stmt->fetch();
$stmt->close();

// Handle selected date from GET, default today
$selected_date = $_GET['date'] ?? date('Y-m-d');

// Fetch appointments from selected date onwards
$sql = "
    SELECT 
        a.id AS appointment_id,
        a.appointment_date,
        a.appointment_time,
        a.status,
        p.patient_name
    FROM appointment_slots a
    LEFT JOIN patients p ON a.patient_id = p.id
    WHERE a.doctor_id = ?
      AND a.appointment_date >= ?
    ORDER BY a.appointment_date ASC, a.appointment_time ASC
";
$stmt = $con->prepare($sql);
$stmt->bind_param("is", $doctor_id, $selected_date);
$stmt->execute();
$result = $stmt->get_result();

$appointments = [];
while($row = $result->fetch_assoc()){
    $appointments[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Appointments for Dr. <?= htmlspecialchars($doctor_name) ?></title>
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 20px;
        background-color: #f9f9f9;
    }
    .content {
        margin-left: 220px;
        padding: 20px;
    }
    h2 { color: #333; margin-bottom: 20px; }
    .filter-form {
        margin-bottom: 20px;
    }
    .filter-form input[type="date"] {
        padding: 5px 10px;
        font-size: 16px;
    }
    .filter-form button {
        padding: 6px 12px;
        font-size: 16px;
        cursor: pointer;
    }
    table {
        border-collapse: collapse;
        width: 100%;
        background-color: #fff;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        border-radius: 8px;
        overflow: hidden;
    }
    th, td {
        border: 1px solid #ddd;
        padding: 12px;
        text-align: left;
    }
    th {
        background-color: #f0f0f0ff;
        color: black;
        cursor: pointer;
    }
    tr:hover { background-color: #f1f1f1; }
    .status-available { color: green; font-weight: bold; }
    .status-booked { color: red; font-weight: bold; }
</style>
</head>
<body>

<div class="content">
    <h2>Appointments for Dr. <?= htmlspecialchars($doctor_name) ?></h2>

    <form method="GET" class="filter-form">
        <label>Select date: </label>
        <input type="date" name="date" value="<?= htmlspecialchars($selected_date) ?>">
        <button type="submit">Filter</button>
    </form>

    <table id="appointmentsTable">
        <thead>
            <tr>
                <th>Appointment ID</th>
                <th>Patient Name</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($appointments)): ?>
                <?php foreach($appointments as $row): ?>
                    <tr>
                        <td><?= $row['appointment_id'] ?></td>
                        <td><?= htmlspecialchars($row['patient_name'] ?? '---') ?></td>
                        <td><?= $row['appointment_date'] ?></td>
                        <td><?= $row['appointment_time'] ?></td>
                        <td class="status-<?= $row['status'] ?>"><?= ucfirst($row['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align:center;">No appointments found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    // Sorting by clicking headers
    document.querySelectorAll('th').forEach((header, index) => {
        header.addEventListener('click', () => {
            const table = header.closest('table');
            const tbody = table.querySelector('tbody');
            Array.from(tbody.querySelectorAll('tr'))
                 .sort((a, b) => {
                     const cellA = a.children[index].innerText.toLowerCase();
                     const cellB = b.children[index].innerText.toLowerCase();
                     return cellA.localeCompare(cellB, undefined, {numeric: true});
                 })
                 .forEach(tr => tbody.appendChild(tr));
        });
    });
</script>

</body>
</html>
