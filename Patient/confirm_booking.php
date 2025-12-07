<?php
require 'db.php';

$slot_id = $_POST['slot_id'] ?? '';
$patient_id = $_POST['patient_id'] ?? '';

if ($slot_id && $patient_id) {
    $stmt = $con->prepare("UPDATE appointment_slots 
                           SET status='booked', patient_id=? 
                           WHERE id=? AND status='available'");
    $stmt->bind_param("ii", $patient_id, $slot_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "<script>alert('🎉 Appointment booked successfully!'); window.location='book_appointment.php';</script>";
    } else {
        echo "<script>alert('⚠️ Slot already booked.'); history.back();</script>";
    }
} else {
    echo "<script>alert('Please select a slot and patient.'); history.back();</script>";
}
?>
