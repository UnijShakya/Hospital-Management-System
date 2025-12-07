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
        $stmt = $con->prepare("SELECT id, doctor_name, password FROM doctors WHERE email=? LIMIT 1");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->bind_result($id, $doctor_name, $hash);
        if ($stmt->fetch()) {
            if (password_verify($pass, $hash)) {
                $_SESSION['doctor_id'] = $id;
                $_SESSION['doctor_name'] = $doctor_name;
                header('Location: doctor_dashboard.php');
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

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Doctor Login | NPL Hospital</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            margin: 0;
            background-color: #f0f2f5;
        }

        .login-container {
            display: flex;
            min-height: 100vh;
        }

        /* Left image panel */
        .login-left {
            flex: 1;
            position: relative;
        }

        .login-left img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .login-left .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.35);
        }

        .login-left .info {
            position: absolute;
            bottom: 30px;
            left: 30px;
            color: #fff;
            max-width: 300px;
        }

        .login-left .info h1 {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .login-left .info p {
            font-size: 1rem;
            line-height: 1.5;
        }

        /* Right login form */
        .login-right {
            flex: 1;
            background-color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 50px 30px;
        }

        .login-card {
            width: 100%;
            max-width: 400px;
        }

        .login-card h2 {
            margin-bottom: 10px;
            color: #333;
            font-weight: 600;
            font-size: 1.8rem;
        }

        .login-card p {
            margin-bottom: 25px;
            color: #555;
            font-size: 0.95rem;
        }

        .login-card input {
            width: 100%;
            padding: 14px 15px;
            margin-bottom: 15px;
            border-radius: 10px;
            border: 1px solid #ccc;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .login-card input:focus {
            border-color: #4e73df;
            box-shadow: 0 0 8px rgba(78, 115, 223, 0.3);
            outline: none;
        }

        .login-card button {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 10px;
            background-color: #4e73df;
            color: #fff;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .login-card button:hover {
            background-color: #375ab7;
        }

        .login-card .links {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
            font-size: 0.85rem;
        }

        .login-card .links a {
            color: #4e73df;
            text-decoration: none;
        }

        .login-card .links a:hover {
            text-decoration: underline;
        }

        .alert {
            padding: 10px 12px;
            border-radius: 6px;
            margin-bottom: 15px;
            font-size: 0.9rem;
            background-color: #f8d7da;
            color: #842029;
            border: 1px solid #f5c2c7;
            text-align: center;
        }

        @media (max-width: 900px) {
            .login-container {
                flex-direction: column;
            }

            .login-left,
            .login-right {
                flex: unset;
                width: 100%;
                min-height: 300px;
            }

            .login-left .info {
                position: static;
                color: #fff;
                padding: 20px;
                max-width: none;
                background: rgba(0, 0, 0, 0.35);
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-left">
            <img src="../assets/uploads/doctor_login.jpg" alt="Doctor Image">
            <div class="overlay"></div>
            <div class="info">
                <h1>NPL Hospital</h1>
                <p>Welcome to NPL Hospital Management System. Access your doctor account securely.</p>
            </div>
        </div>

        <div class="login-right">
            <div class="login-card">
                <h2>Login In</h2>
                <p>Enter your credentials to access your account</p>

                <?php if ($err) echo "<div class='alert'>" . htmlspecialchars($err) . "</div>"; ?>

                <form method="post" autocomplete="off">
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <button type="submit"><i class="fas fa-sign-in-alt"></i> Login In</button>
                </form>

                <div class="links">
                    <a href="forgot_doctor.php">Forgot Password?</a>
                    <a href="register_doctor.php">Sign Up</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
