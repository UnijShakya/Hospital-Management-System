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

    if ($username === '' || $email === '' || $contact === '' || $pass === '' || $pass2 === '') {
        $errors[] = "All fields are required.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email.";
    if ($pass !== $pass2) $errors[] = "Passwords do not match.";
    if (strlen($pass) < 8) $errors[] = "Password must be at least 8 characters.";

    if (empty($errors)) {
        $stmt = $con->prepare("SELECT id FROM admins WHERE email = ? OR contact = ? LIMIT 1");
        $stmt->bind_param('ss', $email, $contact);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "Email or contact already registered.";
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $ins = $con->prepare("INSERT INTO admins (username, email, contact, password_hash) VALUES (?, ?, ?, ?)");
            $ins->bind_param('ssss', $username, $email, $contact, $hash);
            if ($ins->execute()) {
                $_SESSION['success'] = "Admin account created. You can now log in.";
                header('Location: login.php');
                exit;
            } else {
                $errors[] = "Registration failed: " . $con->error;
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
<title>Admin Register</title>
<style>
/* ==========================
   Admin Register Page (Hospital Blue Theme)
========================== */

* { box-sizing: border-box; margin: 0; padding: 0; }
body {
  font-family: 'Segoe UI', Roboto, sans-serif;
  background: linear-gradient(135deg, #dbeafe, #f0f9ff);
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  color: #1e293b;
}

/* Card container */
.register-card {
  background: #fff;
  padding: 40px 30px;
  border-radius: 14px;
  box-shadow: 0 8px 25px rgba(0, 60, 130, 0.15);
  width: 100%;
  max-width: 420px;
  animation: fadeIn 0.6s ease;
}

/* Heading */
.register-card h2 {
  text-align: center;
  margin-bottom: 6px;
  font-size: 24px;
  font-weight: 700;
  color: #0f172a;
}
.register-card p {
  text-align: center;
  font-size: 14px;
  margin-bottom: 22px;
  color: #475569;
}

/* Error box */
.error-box {
  background: #fee2e2;
  color: #991b1b;
  padding: 12px 14px;
  border-radius: 8px;
  font-size: 13px;
  margin-bottom: 18px;
  border: 1px solid #fecaca;
}
.error-box li { margin-left: 18px; }

/* Form */
form { display: flex; flex-direction: column; gap: 18px; }

/* Floating labels */
.input-group {
  position: relative;
}
.input-group input {
  width: 100%;
  padding: 14px 12px;
  border: 1px solid #cbd5e1;
  border-radius: 10px;
  font-size: 14px;
  background: transparent;
  outline: none;
}
.input-group label {
  position: absolute;
  top: 50%;
  left: 12px;
  transform: translateY(-50%);
  font-size: 14px;
  color: #64748b;
  pointer-events: none;
  transition: 0.2s ease all;
}
.input-group input:focus {
  border-color: #2563eb;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
}
.input-group input:focus + label,
.input-group input:not(:placeholder-shown) + label {
  top: -6px;
  left: 10px;
  font-size: 12px;
  background: #fff;
  padding: 0 4px;
  color: #2563eb;
}

/* Button */
button {
  padding: 14px;
  background: linear-gradient(90deg, #2563eb, #1d4ed8);
  color: #fff;
  border: none;
  border-radius: 10px;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}
button:hover {
  background: linear-gradient(90deg, #1d4ed8, #2563eb);
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(29, 78, 216, 0.25);
}

/* Footer actions */
.actions {
  text-align: center;
  margin-top: 12px;
  font-size: 13px;
  color: #475569;
}
.actions a {
  color: #2563eb;
  text-decoration: none;
  font-weight: 500;
}
.actions a:hover { text-decoration: underline; }

/* Animation */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(12px); }
  to   { opacity: 1; transform: translateY(0); }
}
</style>
</head>
<body>
  <div class="register-card">
    <h2>Admin Register</h2>
    <p>Create your hospital admin account</p>

    <?php if (!empty($errors)): ?>
      <div class="error-box">
        <ul>
          <?php foreach ($errors as $e) echo "<li>".htmlspecialchars($e)."</li>"; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="post" autocomplete="off">
      <div class="input-group">
        <input name="username" required placeholder=" " value="<?=htmlspecialchars($username ?? '')?>">
        <label>Username</label>
      </div>
      <div class="input-group">
        <input name="email" type="email" required placeholder=" " value="<?=htmlspecialchars($email ?? '')?>">
        <label>Email</label>
      </div>
      <div class="input-group">
        <input name="contact" required placeholder=" " value="<?=htmlspecialchars($contact ?? '')?>">
        <label>Contact</label>
      </div>
      <div class="input-group">
        <input name="password" type="password" id="pass" required placeholder=" ">
        <label>Password</label>
      </div>
      <div class="input-group">
        <input name="confirm_password" type="password" id="pass2" required placeholder=" ">
        <label>Confirm Password</label>
      </div>
      <button type="submit">Register</button>
    </form>

    <div class="actions">
      Already have an account? <a href="login.php">Login here</a>
    </div>
  </div>

<script>
document.querySelector('form').addEventListener('submit', function(e){
  if (document.getElementById('pass').value !== document.getElementById('pass2').value) {
    e.preventDefault();
    alert('Passwords do not match.');
  }
});
</script>
</body>
</html>
