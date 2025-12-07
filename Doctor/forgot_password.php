<?php
session_start();
include('db.php'); // Database connection
$msg = '';

// Handle Forgot Password
if (isset($_POST['send_link'])) {
    $email = trim($_POST['email']);

    if ($email == '') {
        $msg = "⚠ Please enter your email!";
    } else {
        $stmt = $con->prepare("SELECT id FROM doctors WHERE email=? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $doctor = $result->fetch_assoc();
            $doctor_id = $doctor['id'];

            // Generate reset token
            $token = bin2hex(random_bytes(16));
            $expiry = date("Y-m-d H:i:s", strtotime("+1 hour")); // Token valid for 1 hour

            // Save token and expiry in DB
            $update = $con->prepare("UPDATE doctors SET reset_token=?, reset_expiry=? WHERE id=?");
            $update->bind_param("ssi", $token, $expiry, $doctor_id);
            $update->execute();

            // Generate localhost reset link
            $resetLink = "http://localhost/npl/Doctor/reset_password.php?token=" . $token;

            // Display the reset link for testing
            $msg = "✅ Password reset link: <a href='$resetLink' target='_blank'>$resetLink</a>";
        } else {
            $msg = "❌ Email not registered!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Forgot Password</title>
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
    <h3 class="text-center mb-4">Forgot Password</h3>

    <?php if($msg != ''): ?>
        <div class="alert alert-info"><?= $msg ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3">
            <label>Enter your registered Email</label>
            <input type="email" name="email" class="form-control" placeholder="Email" required>
        </div>
        <button type="submit" name="send_link" class="btn btn-primary w-100">Send Reset Link</button>
        <div class="mt-3 text-center">
            <a href="login.php">Back to Login</a>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
