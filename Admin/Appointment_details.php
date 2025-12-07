<?php
include 'db.php';
include('assets/include/sidebar.php'); 

$selected_doctor = $_POST['doctor_id'] ?? '';
$selected_date = $_POST['appointment_date'] ?? '';

// Fetch doctors with specialization
$doctorResult = $con->query("
    SELECT d.id, d.doctor_name, s.specialization_name
    FROM doctors d
    LEFT JOIN doctor_specialization s ON d.specialization_id = s.id
    ORDER BY d.doctor_name ASC
");
$doctors = [];
while($row = $doctorResult->fetch_assoc()){
    $doctors[] = $row;
}

// Fetch slots for selected doctor + date
$slots_data = [];
$doctor_name = '';
$doctor_specialization = '';
if ($selected_doctor){
    // Get doctor info
    $stmt = $con->prepare("
        SELECT d.doctor_name, s.specialization_name 
        FROM doctors d 
        LEFT JOIN doctor_specialization s ON d.specialization_id = s.id 
        WHERE d.id=?
    ");
    $stmt->bind_param("i", $selected_doctor);
    $stmt->execute();
    $info = $stmt->get_result()->fetch_assoc();
    $doctor_name = $info['doctor_name'] ?? '';
    $doctor_specialization = $info['specialization_name'] ?? '';
    $stmt->close();

    if ($selected_date){
        $stmt = $con->prepare("SELECT * FROM appointment_slots WHERE doctor_id=? AND appointment_date=? ORDER BY appointment_time ASC");
        $stmt->bind_param("is", $selected_doctor, $selected_date);
        $stmt->execute();
        $slots_data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin - Appointments</title>
<style>
body {
    font-family: Arial, sans-serif;
    background:#f4f6f8;
    margin:0;
    padding:0;
}

/* Content wrapper to center next to sidebar */
.content-wrapper {
    margin-left: 220px; /* adjust to your sidebar width */
    padding: 40px 20px;
    min-height: 100vh;
}

/* Main container */
.container {
    max-width: 900px;
    margin: auto;
    margin-left: 150px;
    background: #fff;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

/* Form controls */
select, input[type=date] {
    padding: 10px;
    width: 100%;
    margin-top: 10px;
    border-radius: 6px;
    border:1px solid #ccc;
    font-size: 15px;
}

/* Table styles */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 25px;
}

th, td {
    padding: 12px;
    border: 1px solid #ddd;
    text-align: left;
}

th {
    background-color: #f2f2f2;
    font-weight: 600;
}

tr:hover {background-color: #f9f9f9;}

.status-available { color: green; font-weight: bold;}
.status-booked { color: red; font-weight: bold;}
.status-break { color: orange; font-weight: bold;}
</style>
</head>
<body>

<div class="content-wrapper">
    <div class="container">
        <h2 style="text-align:center; margin-bottom:25px;">Doctor Appointment Slots</h2>

        <form method="post">
            <label>Select Doctor:</label>
            <select name="doctor_id" required onchange="this.form.submit()">
                <option value="">-- Select Doctor --</option>
                <?php foreach($doctors as $doc): ?>
                    <option value="<?= $doc['id'] ?>" <?= $selected_doctor == $doc['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($doc['doctor_name']) ?> (<?= htmlspecialchars($doc['specialization_name'] ?? '-') ?>)
                    </option>
                <?php endforeach; ?>
            </select>

            <?php if($selected_doctor): ?>
                <label>Select Date:</label>
                <input type="date" name="appointment_date" value="<?= htmlspecialchars($selected_date) ?>" onchange="this.form.submit()" required>
            <?php endif; ?>
        </form>

        <?php if($slots_data): ?>
            <h3 style="margin-top:25px; text-align:center;">
                Appointments for <?= htmlspecialchars($doctor_name) ?> (<?= htmlspecialchars($doctor_specialization) ?>) on <?= $selected_date ?>
            </h3>
            <table>
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Status</th>
                        <th>Patient Name</th>
                        <th>Patient Contact</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($slots_data as $slot): 
                        $status_class = $slot['status'];
                        $status_text = ucfirst($slot['status']);
                        $patient_name = '-';
                        $patient_contact = '-';

                        // Fetch patient info if booked
                        if($slot['status']=='booked' && !empty($slot['patient_id'])){
                            $pid = $slot['patient_id'];
                            $pstmt = $con->prepare("SELECT patient_name, contact_no FROM patients WHERE id=?");
                            $pstmt->bind_param("i", $pid);
                            $pstmt->execute();
                            $pinfo = $pstmt->get_result()->fetch_assoc();
                            $pstmt->close();
                            if($pinfo){
                                $patient_name = $pinfo['patient_name'];
                                $patient_contact = $pinfo['contact_no'];
                            }
                        }

                        // Mark break slots
                        if($slot['status']=='available' && empty($slot['patient_id'])){
                            if(strpos(strtolower($slot['appointment_time']), 'break') !== false || $slot['patient_name']=='BREAK'){
                                $status_class = 'break';
                                $status_text = 'Break';
                            }
                        }
                    ?>
                    <tr>
                        <td><?= $slot['appointment_time'] ?></td>
                        <td class="status-<?= $status_class ?>"><?= $status_text ?></td>
                        <td><?= htmlspecialchars($patient_name) ?></td>
                        <td><?= htmlspecialchars($patient_contact) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
