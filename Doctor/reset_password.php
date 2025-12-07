<?php
session_start();
include('db.php');
$msg = '';

// Check if token is provided
if (!isset($_GET['token'])) {
    die("❌ Invalid reset link.");
}

$token = $_GET['token'];

// Verify token exists and not expired
$stmt = $con->prepare("SELECT id, reset_expiry FROM doctors WHERE reset_token=? LIMIT 1");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows != 1) {
    die("❌ Invalid or expired reset link.");
}

$doctor = $result->fetch_assoc();
$doctor_id = $doctor['id'];
$expiry = $doctor['reset_expiry'];

// Check token expiry
if (strtotime($expiry) < time()) {
    die("❌ Reset link has expired.");
}

// Handle password reset
if (isset($_POST['reset_password'])) {
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if ($password == '' || $confirm_password == '') {
        $msg = "⚠ Please enter both fields.";
    } elseif ($password !== $confirm_password) {
        $msg = "⚠ Passwords do not match!";
    } else {
        // Hash the new password
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        // Update password and clear reset token
        $update = $con->prepare("UPDATE doctors SET password=?, reset_token=NULL, reset_expiry=NULL WHERE id=?");
        $update->bind_param("si", $hashed, $doctor_id);

        if ($update->execute()) {
            $msg = "✅ Password reset successfully! <a href='login.php'>Login</a>";
        } else {
            $msg = "❌ Error: " . $update->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reset Password</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<style>
body {
    background: #f8f9fa;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}
.card {
    width: 100%;
    max-width: 400px;
    padding: 20px;
}
</style>
</head>
<body>

<div class="card shadow">
    <h3 class="text-center mb-4">Reset Password</h3>

    <?php if($msg != ''): ?>
        <div class="alert alert-info"><?= $msg ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3">
            <label>New Password</label>
            <input type="password" name="password" class="form-control" placeholder="Enter new password" required>
        </div>
        <div class="mb-3">
            <label>Confirm Password</label>
            <input type="password" name="confirm_password" class="form-control" placeholder="Confirm password" required>
        </div>
        <button type="submit" name="reset_password" class="btn btn-primary w-100">Reset Password</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
