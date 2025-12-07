<?php
session_start();
require 'db.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = strtolower(trim($_POST['email'] ?? ''));
    $contact  = trim($_POST['contact'] ?? '');
    $pass     = $_POST['password'] ?? '';
    $pass2    = $_POST['confirm_password'] ?? '';

    if (!$username || !$email || !$contact || !$pass || !$pass2) {
        $errors[] = "All fields are required.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email.";
    if ($pass !== $pass2) $errors[] = "Passwords do not match.";
    if (strlen($pass) < 8) $errors[] = "Password must be at least 8 characters.";

    if (empty($errors)) {
        $stmt = $con->prepare("SELECT id FROM doctors WHERE email=? OR contact=? LIMIT 1");
        $stmt->bind_param('ss', $email, $contact);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "Email or contact already registered.";
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $ins = $con->prepare("INSERT INTO doctors (username,email,contact,password_hash) VALUES (?,?,?,?)");
            $ins->bind_param('ssss', $username,$email,$contact,$hash);
            if ($ins->execute()) {
                $_SESSION['success'] = "doctor account created. You can now log in.";
                header('Location: login_doctor.php');
                exit;
            } else {
                $errors[] = "Registration failed: ".$con->error;
            }
        }
        $stmt->close();
    }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>doctor Register</title>
<link rel="stylesheet" href="assets/css/auth-styles.css">
<style> 
    /* ===============================
   Global Reset
================================= */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: "Segoe UI", Arial, sans-serif;
  background: #f5f5f5;  /* light neutral background */
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  color: #333;
}

/* ===============================
   Auth Page Layout
================================= */
.auth-page {
  width: 100%;
  max-width: 420px;
  padding: 20px;
}

.auth-card {
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 8px 20px rgba(0,0,0,0.15);
  padding: 30px;
  animation: fadeIn 0.5s ease;
}

.auth-header {
  text-align: center;
  margin-bottom: 20px;
}

.auth-header h2 {
  font-size: 1.6rem;
  color: #4e73df;
}

.auth-header p {
  font-size: 0.95rem;
  color: #666;
  margin-top: 4px;
}

/* ===============================
   Form Styles
================================= */
.form-group {
  margin-bottom: 15px;
}

.form-group label {
  font-size: 0.9rem;
  color: #444;
  display: block;
  margin-bottom: 6px;
}

.form-group input {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #ccc;
  border-radius: 8px;
  font-size: 0.95rem;
  transition: 0.2s;
}

.form-group input:focus {
  border-color: #4e73df;
  outline: none;
  box-shadow: 0 0 0 3px rgba(78,115,223,0.2);
}

/* ===============================
   Buttons
================================= */
.btn {
  display: inline-block;
  padding: 10px 16px;
  border-radius: 8px;
  font-size: 0.95rem;
  font-weight: 500;
  text-decoration: none;
  cursor: pointer;
  transition: background 0.2s ease, transform 0.1s ease;
}

.btn-primary {
  background: #4e73df;
  color: #fff;
  border: none;
}

.btn-primary:hover {
  background: #375ab7;
  transform: translateY(-1px);
}

.btn-link {
  background: transparent;
  color: #4e73df;
  border: none;
  margin-left: 8px;
}

.btn-link:hover {
  text-decoration: underline;
}

/* ===============================
   Alerts
================================= */
.alert {
  padding: 12px 15px;
  border-radius: 6px;
  margin-bottom: 15px;
  font-size: 0.9rem;
}

.alert-error {
  background: #f8d7da;
  color: #842029;
  border: 1px solid #f5c2c7;
}

.alert-success {
  background: #d1e7dd;
  color: #0f5132;
  border: 1px solid #badbcc;
}

/* ===============================
   Animations
================================= */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}

</style>
</head>
<body>
<div class="auth-page">
  <div class="auth-card">
    <div class="auth-header">
      <h2>Doctor Sign Up</h2>
      <p>Create your account</p>
    </div>

    <?php if(!empty($errors)): ?>
      <div class="alert alert-error"><?php echo implode('<br>',array_map('htmlspecialchars',$errors)); ?></div>
    <?php endif; ?>

    <form method="post" autocomplete="off">
      <div class="form-group"><label>Username</label><input name="username" required></div>
      <div class="form-group"><label>Email</label><input type="email" name="email" required></div>
      <div class="form-group"><label>Contact</label><input name="contact" required></div>
      <div class="form-group"><label>Password</label><input type="password" name="password" required></div>
      <div class="form-group"><label>Confirm Password</label><input type="password" name="confirm_password" required></div>

      <div class="form-actions">
        <button class="btn btn-primary" type="submit">Sign Up</button>
        <a class="btn btn-link" href="login_doctor.php">Already have an account?</a>
      </div>
    </form>
  </div>
</div>
</body>
</html>
