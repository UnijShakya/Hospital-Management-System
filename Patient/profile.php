<?php
session_start();
require 'db.php';

// Ensure patient is logged in
if (!isset($_SESSION['patient_id'])) {
    die("Please log in to view your profile.");
}

$patient_id = $_SESSION['patient_id'];
$message = "";

// Fetch patient data
$stmt = $con->prepare("SELECT * FROM patients WHERE id=?");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = $_POST['patient_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $contact = $_POST['contact_no'] ?? '';
    $age = $_POST['age'] ?? null;
    $gender = $_POST['gender'] ?? 'Male';

    $stmt = $con->prepare("
        UPDATE patients
        SET patient_name=?, email=?, contact_no=?, age=?, gender=?
        WHERE id=?
    ");
    $stmt->bind_param("sssssi", $name, $email, $contact, $age, $gender, $patient_id);
    if ($stmt->execute()) {
        $message = "Profile updated successfully!";
        // Refresh patient data
        $stmt = $con->prepare("SELECT * FROM patients WHERE id=?");
        $stmt->bind_param("i", $patient_id);
        $stmt->execute();
        $patient = $stmt->get_result()->fetch_assoc();
    } else {
        $message = "Failed to update profile.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Profile</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php include 'patient_sidebar.php'; ?>
<style>
body {
    margin: 0;
    font-family: 'Poppins', sans-serif;
    background: #f0f4f8;
    display: flex;
}

/* Sidebar aware wrapper */
.content-wrapper {
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    padding: 20px;
}

/* Profile container */
.container {
    width: 100%;
    max-width: 500px;
    background: #fff;
    padding: 40px 30px;
    border-radius: 16px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

/* Heading */
h2 {
    text-align: center;
    margin-bottom: 30px;
    color: #2a9d8f;
}

/* Form Fields */
.field {
    margin-bottom: 20px;
}

.field label {
    font-weight: 600;
    display: block;
    margin-bottom: 8px;
    color: #555;
}

.field span {
    display: block;
    padding: 10px;
    background: #f2f2f2;
    border-radius: 8px;
    color: #333;
}

input, select {
    width: 100%;
    padding: 10px;
    border-radius: 8px;
    border: 1px solid #ccc;
    display: none; /* hidden initially */
}

/* Buttons */
button {
    padding: 12px 25px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

#editBtn {
    background: #2a9d8f;
    color: white;
}

#editBtn:hover {
    background: #21867a;
    transform: translateY(-2px);
}

#saveBtn {
    background: #f4a261;
    color: white;
    display: none; /* hidden initially */
}

#saveBtn:hover {
    background: #e76f51;
    transform: translateY(-2px);
}

/* Message */
.message {
    text-align: center;
    font-weight: 600;
    color: green;
    margin-bottom: 20px;
}
</style>
<script>
function enableEdit() {
    // Hide spans
    document.querySelectorAll('.field span').forEach(s => s.style.display = 'none');
    // Show input/select
    document.querySelectorAll('.field input, .field select').forEach(i => i.style.display = 'block');
    // Hide edit button, show save button
    document.getElementById('editBtn').style.display = 'none';
    document.getElementById('saveBtn').style.display = 'inline-block';
}
</script>
</head>
<body>
<div class="content-wrapper">
    <div class="container">
        <h2>My Profile</h2>

        <?php if($message) echo "<div class='message'>$message</div>"; ?>

        <form method="post">
            <div class="field">
                <label>Full Name</label>
                <span><?= htmlspecialchars($patient['patient_name']) ?></span>
                <input type="text" name="patient_name" value="<?= htmlspecialchars($patient['patient_name']) ?>" required>
            </div>

            <div class="field">
                <label>Email</label>
                <span><?= htmlspecialchars($patient['email']) ?></span>
                <input type="email" name="email" value="<?= htmlspecialchars($patient['email']) ?>" required>
            </div>

            <div class="field">
                <label>Contact No</label>
                <span><?= htmlspecialchars($patient['contact_no']) ?></span>
                <input type="text" name="contact_no" value="<?= htmlspecialchars($patient['contact_no']) ?>">
            </div>

            <div class="field">
                <label>Age</label>
                <span><?= htmlspecialchars($patient['age']) ?></span>
                <input type="number" name="age" value="<?= htmlspecialchars($patient['age']) ?>">
            </div>

            <div class="field">
                <label>Gender</label>
                <span><?= htmlspecialchars($patient['gender']) ?></span>
                <select name="gender">
                    <option value="Male" <?= $patient['gender']=='Male' ? 'selected' : '' ?>>Male</option>
                    <option value="Female" <?= $patient['gender']=='Female' ? 'selected' : '' ?>>Female</option>
                    <option value="Other" <?= $patient['gender']=='Other' ? 'selected' : '' ?>>Other</option>
                </select>
            </div>

            <div style="text-align:center;">
                <button type="button" id="editBtn" onclick="enableEdit()">Update Profile</button>
                <button type="submit" name="update_profile" id="saveBtn">Save Changes</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
