<?php
session_start();
require 'db.php';
$msg = '';

// Make sure doctor is logged in
if (!isset($_SESSION['doctor_id'])) {
    die("Please log in as a doctor to access this page.");
}

$doctor_id = $_SESSION['doctor_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['appointment_date'] ?? '';
    $start_time = $_POST['start_time'] ?? '';
    $end_time = $_POST['end_time'] ?? '';
    $break_start = $_POST['break_start'] ?? '';
    $break_end = $_POST['break_end'] ?? '';
    $slot_duration = intval($_POST['slot_duration'] ?? 30);

    if (!$date || !$start_time || !$end_time || !$slot_duration) {
        $msg = "❌ All fields are required.";
    } elseif (strtotime($start_time) >= strtotime($end_time)) {
        $msg = "❌ Start time must be before end time.";
    } elseif ($break_start && $break_end && strtotime($break_start) >= strtotime($break_end)) {
        $msg = "❌ Break start time must be before break end time.";
    } else {
        // Delete existing slots for this doctor/date
        $con->query("DELETE FROM appointment_slots WHERE doctor_id=$doctor_id AND appointment_date='$date'");

        // Store break record
        $stmt = $con->prepare("
            INSERT INTO doctor_breaks (doctor_id, break_date, break_start, break_end)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("isss", $doctor_id, $date, $break_start, $break_end);
        $stmt->execute();

        // Generate slots
        $start = strtotime($start_time);
        $end = strtotime($end_time);
        $break_s = strtotime($break_start);
        $break_e = strtotime($break_end);

        while ($start < $end) {
            $slot_time = date('H:i:s', $start);
            $next = strtotime("+$slot_duration minutes", $start);

            // Skip break time
            if ($break_start && $break_end && $start >= $break_s && $start < $break_e) {
                $start = $next;
                continue;
            }

            if ($next <= $end) {
                $stmt = $con->prepare("
                    INSERT INTO appointment_slots (doctor_id, appointment_date, appointment_time, status)
                    VALUES (?, ?, ?, 'available')
                ");
                $stmt->bind_param("iss", $doctor_id, $date, $slot_time);
                $stmt->execute();
            }

            $start = $next;
        }

        $msg = "✅ Schedule created successfully !";
    }
}

// Fetch schedules for logged-in doctor
$schedules = $con->query("
    SELECT b.id AS break_id, b.break_date, 
           MIN(a.appointment_time) AS start_time, MAX(a.appointment_time) AS end_time,
           b.break_start, b.break_end
    FROM doctor_breaks b
    LEFT JOIN appointment_slots a ON a.doctor_id = b.doctor_id AND a.appointment_date = b.break_date
    WHERE b.doctor_id = $doctor_id
    GROUP BY b.id
    ORDER BY b.break_date DESC
");

// Handle deletion
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    // Delete breaks
    $con->query("DELETE FROM doctor_breaks WHERE id=$delete_id");
    // Delete slots associated with this date and doctor
    $con->query("DELETE FROM appointment_slots WHERE doctor_id=$doctor_id AND appointment_date=(SELECT break_date FROM doctor_breaks WHERE id=$delete_id)");
    header("Location: " . basename(__FILE__));
    exit();
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Doctor Schedule</title>
    <style>
   
/* ======= General Layout ======= */
body {
    margin: 0;
    font-family: "Poppins", sans-serif;
    background: #f0f4f8;
    display: flex;
}

/* Sidebar (already included via sidebar.php) */
.sidebar {
    width: 250px;
    background: linear-gradient(180deg, #2a9d8f, #228176);
    color: #fff;
    min-height: 100vh;
}

/* ======= Main Area ======= */
.main {
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    padding: 50px 20px;
    box-sizing: border-box;
}

/* ======= Schedule Container ======= */
.container {
    width: 750px;
    background: #ffffff;
    padding: 35px;
    border-radius: 16px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
    animation: fadeIn 0.5s ease-in-out;
}

/* ======= Headings ======= */
h2 {
    text-align: center;
    color: #2a9d8f;
    font-size: 28px;
    margin-bottom: 25px;
    font-weight: 600;
}

h3 {
    color: #333;
    font-size: 20px;
    text-align: center;
    margin-top: 35px;
    border-bottom: 2px solid #2a9d8f;
    display: inline-block;
    padding-bottom: 5px;
}

/* ======= Form Styling ======= */
form {
    background: #f9fafc;
    padding: 20px;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
}

label {
    display: block;
    margin-top: 12px;
    font-weight: 500;
    color: #333;
}

input, button {
    width: 100%;
    padding: 10px 12px;
    margin-top: 6px;
    border: 1px solid #cfd9e3;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.3s ease;
    box-sizing: border-box;
}

input:focus {
    outline: none;
    border-color: #2a9d8f;
    box-shadow: 0 0 5px rgba(42, 157, 143, 0.3);
}

/* ======= Button Styling ======= */
button {
    background: linear-gradient(90deg, #2a369dff, #212886ff);
    color: #fff;
    border: none;
    font-weight: 600;
    margin-top: 20px;
    transition: all 0.3s ease;
    cursor: pointer;
}

button:hover {
    background: linear-gradient(90deg, #21862eff, #459d2aff);
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(42, 157, 143, 0.3);
}

/* ======= Message ======= */
.msg {
    text-align: center;
    margin-top: 15px;
    font-weight: bold;
    font-size: 15px;
}
.success {
    color: #1b9c85;
}
.error {
    color: #e63946;
}

/* ======= Table Styling ======= */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.05);
}

th, td {
    padding: 12px 10px;
    text-align: center;
}

th {
    background: linear-gradient(90deg, #2a329dff, #212886ff);
    color: white;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

tr:nth-child(even) {
    background-color: #f9f9f9;
}

tr:hover {
    background-color: #f1fdf9;
    transition: 0.2s;
}

/* ======= Action Link ======= */
a.delete {
    color: #e63946;
    text-decoration: none;
    font-weight: bold;
    transition: 0.2s;
}

a.delete:hover {
    text-decoration: underline;
    color: #b91d2f;
}

/* ======= Animation ======= */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* ======= Responsive ======= */
@media (max-width: 900px) {
    .container {
        width: 95%;
        padding: 20px;
    }
}


    </style>

    <?php include 'sidebar.php' ?>
</head>

<body>
    <div class="main">
        <div class="container">
            <h2>Create Your Schedule</h2>
            <form method="POST">
                <label>Appointment Date:</label>
                <input type="date" name="appointment_date" required>

                <label>Working Start Time:</label>
                <input type="time" name="start_time" required>

                <label>Working End Time:</label>
                <input type="time" name="end_time" required>

                <label>Break Start Time:</label>
                <input type="time" name="break_start">

                <label>Break End Time:</label>
                <input type="time" name="break_end">

                <label>Slot Duration (minutes):</label>
                <input type="number" name="slot_duration" value="30" min="5" required>

                <button type="submit">Generate Schedule</button>
            </form>

            <?php if ($msg): ?>
            <p class="msg <?= str_contains($msg, '✅') ? 'success' : 'error' ?>">
                <?= htmlspecialchars($msg) ?>
            </p>
            <?php endif; ?>

            <h3>Your Generated Schedules</h3>
            <table>
                <tr>
                    <th>Date</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Break Start</th>
                    <th>Break End</th>
                    <th>Action</th>
                </tr>
                <?php while ($row = $schedules->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['break_date'] ?></td>
                    <td><?= $row['start_time'] ?? 'N/A' ?></td>
                    <td><?= $row['end_time'] ?? 'N/A' ?></td>
                    <td><?= $row['break_start'] ?: '-' ?></td>
                    <td><?= $row['break_end'] ?: '-' ?></td>
                    <td><a class="delete" href="?delete_id=<?= $row['break_id'] ?>"
                            onclick="return confirm('Are you sure you want to delete this schedule?')">Delete</a></td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</body>

</html>