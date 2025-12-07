<?php
session_start();
require 'db.php';

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $pass  = $_POST['password'] ?? '';

    if ($email === '' || $pass === '') {
        $err = "Please enter both email and password.";
    } else {
        $stmt = $con->prepare("SELECT id, username, password_hash FROM admins WHERE email = ? LIMIT 1");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->bind_result($id, $username, $hash);
        if ($stmt->fetch() && password_verify($pass, $hash)) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $id;
            $_SESSION['admin_user'] = $username;
            header('Location: admin_dashboard.php');
            exit;
        } else {
            $err = "Invalid email or password.";
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
<title>Admin Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
/* ===== General ===== */
body {
  margin: 0;
  height: 100vh;
  font-family: "Poppins", sans-serif;
  background: url('uploads/login.jpg') no-repeat center center fixed;
  background-size: cover;
  display: flex;
  justify-content: center;
  align-items: center;
}

/* Subtle overlay */
body::before {
  content: "";
  position: fixed;
  inset: 0;
  background: rgba(255, 255, 255, 0.6);
  backdrop-filter: blur(3px);
}

/* ===== Login Box ===== */
.login-card {
  position: relative;
  z-index: 1;
  background: #ffffffdd;
  padding: 40px 35px;
  border-radius: 12px;
  box-shadow: 0 6px 20px rgba(0,0,0,0.1);
  width: 100%;
  max-width: 380px;
  text-align: center;
}

/* ===== Header ===== */
.login-card h2 {
  font-weight: 700;
  font-size: 22px;
  margin-bottom: 6px;
  color: #1e293b;
}
.login-card p {
  font-size: 14px;
  color: #64748b;
  margin-bottom: 25px;
}

/* ===== Messages ===== */
.error-box {
  background: #fee2e2;
  color: #991b1b;
  border: 1px solid #fecaca;
  padding: 10px;
  border-radius: 8px;
  font-size: 13px;
  margin-bottom: 16px;
}

/* ===== Input Fields ===== */
.input-group {
  margin-bottom: 16px;
  text-align: left;
}
.input-group label {
  display: block;
  font-size: 13px;
  color: #475569;
  margin-bottom: 5px;
}
.input-group input {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #cbd5e1;
  border-radius: 8px;
  font-size: 14px;
  outline: none;
}
.input-group input:focus {
  border-color: #2563eb;
  box-shadow: 0 0 0 2px rgba(37,99,235,0.15);
}

/* ===== Button ===== */
button {
  width: 100%;
  padding: 10px;
  background: #2563eb;
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  font-size: 15px;
  transition: background 0.2s ease;
}
button:hover { background: #1d4ed8; }

/* ===== Links ===== */
.actions {
  margin-top: 14px;
  font-size: 13px;
  color: #475569;
}
.actions a {
  color: #2563eb;
  text-decoration: none;
  font-weight: 500;
}
.actions a:hover { text-decoration: underline; }
</style>
</head>
<body>
  <div class="login-card">
    <h2>Admin Login</h2>
    <p>Sign in to continue</p>

    <?php if ($err): ?>
      <div class="error-box"><?= htmlspecialchars($err) ?></div>
    <?php endif; ?>

    <form method="POST" autocomplete="off">
      <div class="input-group">
        <label>Email</label>
        <input type="email" name="email" required>
      </div>
      <div class="input-group">
        <label>Password</label>
        <input type="password" name="password" required>
      </div>
      <button type="submit">Login</button>
    </form>

    <div class="actions">
      <a href="forgot_password.php">Forgot Password?</a> |
      <a href="register_admin.php">Register</a>
    </div>
  </div>
</body>
</html>
