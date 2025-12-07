<?php
session_start();
require 'db.php';

$msg = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = trim($_POST['identifier'] ?? '');

    if ($input === '') {
        $err = "Enter email or contact.";
    } else {
        $stmt = $con->prepare("SELECT id, email FROM patients WHERE email = ? OR contact_no = ? LIMIT 1");
        $stmt->bind_param('ss', $input, $input);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $stmt->close();

        if ($row) {
            $id    = $row['id'];
            $email = $row['email'];

            $token   = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', time() + 3600);

            $upd = $con->prepare("UPDATE patients SET reset_token = ?, reset_expiry = ? WHERE id = ?");
            $upd->bind_param('ssi', $token, $expires, $id);
            if ($upd->execute()) {
                $upd->close();

                // For demo: clickable reset link
                $resetLink = "http://localhost/npl/Patient/reset_patient.php?token=$token";
                $msg = "Reset link generated. (For demo only) <a href='" . htmlspecialchars($resetLink) . "' target='_blank'>Click here to reset password</a>";
            } else {
                $err = "Unable to set reset token.";
            }
        } else {
            $err = "No account found with that email/contact.";
        }
    }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Patient Forgot Password</title>
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
.forgot-card {
  background: #fff;
  padding: 40px 30px;
  border-radius: 14px;
  box-shadow: 0 8px 25px rgba(0, 60, 130, 0.15);
  width: 100%;
  max-width: 420px;
  animation: fadeIn 0.6s ease;
}
.forgot-card h2 { text-align: center; margin-bottom: 6px; font-size: 24px; font-weight: 700; color: #0f172a; }
.forgot-card p { text-align: center; font-size: 14px; margin-bottom: 22px; color: #475569; }
.error-box { background: #fee2e2; color: #991b1b; padding: 12px 14px; border-radius: 8px; font-size: 13px; margin-bottom: 18px; border: 1px solid #fecaca; }
.success-box { background: #d1fae5; color: #065f46; padding: 12px 14px; border-radius: 8px; font-size: 13px; margin-bottom: 18px; border: 1px solid #a7f3d0; }
form { display: flex; flex-direction: column; gap: 18px; }
.input-group { position: relative; }
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
.actions { text-align: center; margin-top: 12px; font-size: 13px; color: #475569; }
.actions a { color: #2563eb; text-decoration: none; font-weight: 500; }
.actions a:hover { text-decoration: underline; }
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(12px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
</head>
<body>
  <div class="forgot-card">
    <h2>Forgot Password</h2>
    <p>Enter your email or contact to reset password</p>

    <?php if ($err): ?>
      <div class="error-box"><?=htmlspecialchars($err)?></div>
    <?php endif; ?>

    <?php if ($msg): ?>
      <div class="success-box"><?= $msg ?></div>
    <?php endif; ?>

    <form method="post" autocomplete="off">
      <div class="input-group">
        <input name="identifier" required placeholder=" ">
        <label>Email or Contact</label>
      </div>
      <button type="submit">Send Reset Link</button>
    </form>

    <div class="actions">
      <a href="login_patient.php">Back to Login</a>
    </div>
  </div>
</body>
</html>
