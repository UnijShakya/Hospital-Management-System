<?php
session_start();
include('db.php');

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_name = trim($_POST['patient_name'] ?? '');
    $email        = strtolower(trim($_POST['email'] ?? ''));
    $contact_no   = trim($_POST['contact_no'] ?? '');
    $password     = $_POST['password'] ?? '';
    $confirm      = $_POST['confirm_password'] ?? '';

    // Validation
    if (!$patient_name || !$email || !$contact_no || !$password || !$confirm) {
        $errors[] = "All fields are required.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email.";
    if ($password !== $confirm) $errors[] = "Passwords do not match.";
    if (strlen($password) < 8) $errors[] = "Password must be at least 8 characters.";

    if (empty($errors)) {
        // Check existing email or contact_no
        $stmt = $con->prepare("SELECT id FROM patients WHERE email=? OR contact_no=? LIMIT 1");
        $stmt->bind_param('ss', $email, $contact_no);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "Email or contact number already registered.";
        } else {
            // Insert patient
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $ins = $con->prepare("INSERT INTO patients (patient_name,email,contact_no,password) VALUES (?,?,?,?)");
            $ins->bind_param('ssss', $patient_name,$email,$contact_no,$hash);
            if ($ins->execute()) {
                $_SESSION['success'] = "Patient account created. You can now log in.";
                header('Location: login_patient.php');
                exit;
            } else {
                $errors[] = "Registration failed: ".$con->error;
            }
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Patient Register</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background: #f8f9fa; display: flex; justify-content: center; align-items: center; height: 100vh; }
.card { width: 100%; max-width: 420px; padding: 20px; border-radius: 12px; box-shadow: 0 8px 20px rgba(0,0,0,0.15); }
</style>
</head>
<body>
<div class="card">
    <h3 class="text-center mb-4">Patient Sign Up</h3>

    <?php if(!empty($errors)): ?>
        <div class="alert alert-danger"><?php echo implode('<br>', array_map('htmlspecialchars', $errors)); ?></div>
    <?php endif; ?>

    <form method="post" autocomplete="off">
        <div class="mb-3">
            <label>Patient Name</label>
            <input name="patient_name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Contact Number</label>
            <input name="contact_no" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Confirm Password</label>
            <input type="password" name="confirm_password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Sign Up</button>
        <p class="mt-2 text-center"><a href="login_patient.php">Already have an account?</a></p>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
