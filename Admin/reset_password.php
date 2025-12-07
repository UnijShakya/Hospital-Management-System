<?php
session_start();
require 'db.php';

$token = $_GET['token'] ?? $_POST['token'] ?? '';
$err = '';
$success = '';

if ($token === '') {
    $err = "Invalid or missing token.";
} else {
    $stmt = $con->prepare("SELECT id, reset_expires FROM admins WHERE reset_token = ? LIMIT 1");
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $stmt->bind_result($id, $expires);
    if (!$stmt->fetch()) {
        $err = "Invalid token.";
    } else {
        if (strtotime($expires) < time()) {
            $err = "Token expired. Please request a new reset.";
        }
    }
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $err === '') {
    $pass = $_POST['password'] ?? '';
    $pass2 = $_POST['confirm_password'] ?? '';
    if ($pass === '' || $pass2 === '') {
        $err = "Enter both password fields.";
    } elseif ($pass !== $pass2) {
        $err = "Passwords don't match.";
    } elseif (strlen($pass) < 8) {
        $err = "Password must be at least 8 characters.";
    } else {
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $upd = $con->prepare("UPDATE admins SET password_hash = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
        $upd->bind_param('si', $hash, $id);
        if ($upd->execute()) {
            $success = "Password updated. You can now <a href='login.php'>login</a>.";
        } else {
            $err = "Unable to update password.";
        }
        $upd->close();
    }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Reset Password</title>
<style>
* { box-sizing: border-box; margin:0; padding:0; }
body {
  font-family: 'Segoe UI', Roboto, sans-serif;
  background: linear-gradient(135deg, #dbeafe, #f0f9ff);
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  color: #1e293b;
}

.reset-card {
  background: #fff;
  padding: 40px 30px;
  border-radius: 14px;
  box-shadow: 0 8px 25px rgba(0, 60, 130, 0.15);
  width: 100%;
  max-width: 420px;
  animation: fadeIn 0.6s ease;
}

.reset-card h2 {
  text-align: center;
  margin-bottom: 6px;
  font-size: 24px;
  font-weight: 700;
  color: #0f172a;
}

.reset-card p {
  text-align: center;
  font-size: 14px;
  margin-bottom: 22px;
  color: #475569;
}

.error-box {
  background: #fee2e2;
  color: #991b1b;
  padding: 12px 14px;
  border-radius: 8px;
  font-size: 13px;
  margin-bottom: 18px;
  border: 1px solid #fecaca;
}

.success-box {
  background: #d1fae5;
  color: #065f46;
  padding: 12px 14px;
  border-radius: 8px;
  font-size: 13px;
  margin-bottom: 18px;
  border: 1px solid #a7f3d0;
}

form { display: flex; flex-direction: column; gap: 18px; }

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

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(12px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
</head>
<body>
  <div class="reset-card">
    <h2>Reset Password</h2>

    <?php if ($err): ?>
      <div class="error-box"><?=htmlspecialchars($err)?></div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="success-box"><?= $success ?></div>
    <?php elseif ($err === ''): ?>
      <form method="post" autocomplete="off">
        <input type="hidden" name="token" value="<?=htmlspecialchars($token)?>">
        <div class="input-group">
          <input name="password" type="password" required placeholder=" ">
          <label>New Password</label>
        </div>
        <div class="input-group">
          <input name="confirm_password" type="password" required placeholder=" ">
          <label>Confirm Password</label>
        </div>
        <button type="submit">Set New Password</button>
      </form>
      <div class="actions">
        <a href="login.php">Back to Login</a>
      </div>
    <?php endif; ?>

<script>
document.querySelector('form')?.addEventListener('submit', function(e){
  if (document.getElementById('p1')?.value !== document.getElementById('p2')?.value) {
    e.preventDefault();
    alert('Passwords do not match.');
  }
});
</script>
  </div>
</body>
</html>
