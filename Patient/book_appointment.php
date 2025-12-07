<?php
session_start();
require 'db.php';

// Make sure patient is logged in
if (!isset($_SESSION['patient_id'])) {
    die("Please log in as a patient to book an appointment.");
}

$patient_id = $_SESSION['patient_id'];
$doctor_id = $_GET['doctor_id'] ?? '';
$date = $_GET['date'] ?? '';
$slots = [];

// Fetch appointment slots if doctor and date are selected
if ($doctor_id && $date) {
    $stmt = $con->prepare("
        SELECT s.*, d.doctor_name 
        FROM appointment_slots s 
        JOIN doctors d ON s.doctor_id = d.id
        WHERE s.doctor_id=? AND s.appointment_date=?
        ORDER BY appointment_time
    ");
    $stmt->bind_param("is", $doctor_id, $date);
    $stmt->execute();
    $slots = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Fetch doctors with specialization for dropdown
$doctor_res = $con->query("
    SELECT d.id, d.doctor_name, s.specialization_name 
    FROM doctors d
    LEFT JOIN doctor_specialization s ON d.specialization_id = s.id
    ORDER BY d.doctor_name
");
?>

<!DOCTYPE html>
<html>
<head>
    <?php include 'patient_sidebar.php'; ?>
    <title>Book Appointment</title>
    <style>
        
/* ===== Layout ===== */
body {
    margin: 0;
    font-family: "Poppins", sans-serif;
    background: #f0f4f8;
    display: flex;
}


/* Main content beside sidebar */
.main {
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    padding: 50px 20px;
    box-sizing: border-box;
}

/* ===== Container ===== */
.container {
    width: 700px;
    background: #fff;
    padding: 35px;
    border-radius: 16px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
    animation: fadeIn 0.5s ease-in-out;
    text-align: center;
}

/* ===== Headings ===== */
h2 {
    color: #2a9d8f;
    font-size: 26px;
    font-weight: 600;
    margin-bottom: 25px;
}

/* ===== Form Styling ===== */
form {
    background: #f9fafc;
    padding: 20px;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    margin-bottom: 20px;
}

label {
    display: block;
    text-align: left;
    margin-top: 12px;
    font-weight: 500;
    color: #333;
}

select, input[type="date"] {
    width: 100%;
    padding: 10px 12px;
    margin-top: 6px;
    border: 1px solid #cfd9e3;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.3s ease;
    box-sizing: border-box;
}

select:focus, input:focus {
    outline: none;
    border-color: #2a9d8f;
    box-shadow: 0 0 5px rgba(42, 157, 143, 0.3);
}

/* ===== Buttons ===== */
button {
    margin-top: 15px;
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    background: linear-gradient(90deg, #2a9d8f, #21867a);
    color: white;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s ease;
}

button:hover {
    background: linear-gradient(90deg, #21867a, #2a9d8f);
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(42, 157, 143, 0.3);
}

/* ===== Slot Grid ===== */
.slot-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
    gap: 12px;
    justify-content: center;
    margin-top: 20px;
}

.slot {
    padding: 12px;
    border-radius: 8px;
    background: #d4edda;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s ease;
}

.slot:hover {
    background: #bde3c8;
    transform: scale(1.05);
}

.booked {
    background: #f8d7da;
    color: #555;
    cursor: not-allowed;
}

.selected {
    background: #2a9d8f;
    color: #fff;
    transform: scale(1.08);
}

/* ===== Animation ===== */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(15px); }
    to { opacity: 1; transform: translateY(0); }
}

/* ===== Responsive ===== */
@media (max-width: 900px) {
    .container {
        width: 95%;
        padding: 25px;
    }
}


    </style>
</head>
<body>
<div class="container">
    <h2>Book Appointment</h2>
    
    <!-- Doctor & Date Selection -->
    <form method="GET">
        <label>Doctor:</label>
        <select name="doctor_id" required>
            <option value="">-- Select Doctor --</option>
            <?php while ($row = $doctor_res->fetch_assoc()): 
                $sel = $row['id'] == $doctor_id ? 'selected' : '';
                $spec = $row['specialization_name'] ? " ({$row['specialization_name']})" : '';
            ?>
                <option value="<?= $row['id'] ?>" <?= $sel ?>><?= $row['doctor_name'] ?><?= $spec ?></option>
            <?php endwhile; ?>
        </select>

        <label>Date:</label>
        <input type="date" name="date" value="<?= htmlspecialchars($date) ?>" required>
        
        <button type="submit">View Slots</button>
    </form>

    <?php if ($slots): ?>
    <!-- Appointment Slots -->
    <form method="POST" action="confirm_booking.php" id="bookingForm">
        <input type="hidden" name="slot_id" id="slot_id">
        <input type="hidden" name="patient_id" value="<?= $patient_id ?>">

        <div class="slot-grid">
            <?php foreach ($slots as $s): ?>
                <div class="slot <?= $s['status']=='booked'?'booked':'' ?>" 
                     data-id="<?= $s['id'] ?>" 
                     data-time="<?= date('h:i A', strtotime($s['appointment_time'])) ?>">
                    <?= date('h:i A', strtotime($s['appointment_time'])) ?>
                </div>
            <?php endforeach; ?>
        </div>

        <button type="submit">Confirm Booking</button>
    </form>
    <?php endif; ?>
</div>

<script>
const slots = document.querySelectorAll('.slot');
let selected = null;

slots.forEach(slot => {
    if (!slot.classList.contains('booked')) {
        slot.addEventListener('click', () => {
            if (selected) selected.classList.remove('selected');
            slot.classList.add('selected');
            selected = slot;
            document.getElementById('slot_id').value = slot.dataset.id;
        });
    }
});
</script>
</body>
</html>
