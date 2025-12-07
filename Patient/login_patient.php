<?php
session_start();
require 'db.php';

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $pass  = $_POST['password'] ?? '';

    if (!$email || !$pass) {
        $err = "Email and password required.";
    } else {
        $stmt = $con->prepare("SELECT id, patient_name, password FROM patients WHERE email=? LIMIT 1");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->bind_result($id, $patient_name, $hash);
        if ($stmt->fetch()) {
            if (password_verify($pass, $hash)) {
                $_SESSION['patient_id'] = $id;
                $_SESSION['patient_name'] = $patient_name;
                header('Location: patient_dashboard.php');
                exit;
            } else {
                $err = "Invalid credentials.";
            }
        } else {
            $err = "Invalid credentials.";
        }
        $stmt->close();
    }
}
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Patient Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    font-family: "Segoe UI", Arial, sans-serif;
    background: #f5f5f5;
    display: flex; justify-content: center; align-items: center;
    min-height: 100vh; color: #333;
}
.auth-page { width:100%; max-width:420px; padding:20px; }
.auth-card {
    background:#fff; border-radius:12px; box-shadow:0 6px 20px rgba(0,0,0,0.12);
    padding:30px; animation:fadeIn .5s ease;
}
.auth-header { text-align:center; margin-bottom:20px; }
.auth-header h2 { font-size:1.6rem; color:#4e73df; }
.auth-header p { font-size:.95rem; color:#666; margin-top:4px; }
.form-group { margin-bottom:15px; }
.form-group label { font-size:.9rem; color:#444; display:block; margin-bottom:6px; }
.form-group input {
    width:100%; padding:10px 12px; border:1px solid #ccc; border-radius:8px;
    font-size:.95rem; transition:.2s;
}
.form-group input:focus { border-color:#4e73df; outline:none; box-shadow:0 0 0 3px rgba(78,115,223,0.2);}
.form-actions { display:flex; justify-content:space-between; gap:10px; flex-wrap:wrap; }
.btn { flex:1; display:inline-block; padding:10px 16px; border-radius:8px; font-size:.95rem; font-weight:500; text-align:center; cursor:pointer; border:none; transition:background .2s ease, transform .1s ease;}
.btn-primary { background:#4e73df; color:#fff; }
.btn-primary:hover { background:#375ab7; transform:translateY(-1px);}
.btn-danger { background:#dc3545; color:#fff; }
.btn-danger:hover { background:#b02a37; transform:translateY(-1px);}
.btn-link { flex-basis:100%; text-align:center; margin-top:10px; background:transparent; color:#4e73df; }
.btn-link:hover { text-decoration:underline; }
.alert { padding:12px 15px; border-radius:6px; margin-bottom:15px; font-size:.9rem; }
.alert-error { background:#f8d7da; color:#842029; border:1px solid #f5c2c7; }
.alert-success { background:#d1e7dd; color:#0f5132; border:1px solid #badbcc; }
@keyframes fadeIn { from {opacity:0; transform:translateY(-10px);} to {opacity:1; transform:translateY(0);} }
</style>
</head>
<body>
<div class="auth-page">
  <div class="auth-card">
    <div class="auth-header">
      <h2>Patient Login</h2>
      <p>Enter your email and password</p>
    </div>

    <?php if($err) echo "<div class='alert alert-error'>".htmlspecialchars($err)."</div>"; ?>
    <?php if(!empty($_SESSION['success'])){ echo "<div class='alert alert-success'>".htmlspecialchars($_SESSION['success'])."</div>"; unset($_SESSION['success']); } ?>

    <form method="post" autocomplete="off">
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" required>
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" required>
      </div>

      <div class="form-actions">
        <button class="btn btn-primary" type="submit">Login</button>
        <a class="btn btn-danger" href="register_patient.php">Sign Up</a>
      </div>
      <a class="btn btn-link" href="forgot_patient.php">Forgot password?</a>
    </form>
  </div>
</div>
</body>
</html>
