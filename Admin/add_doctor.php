<?php
include('db.php'); 
include('assets/include/sidebar.php'); 
$msg = '';
$alertClass = '';

// Fetch doctor specializations
$specResult = mysqli_query($con, "SELECT * FROM doctor_specialization ORDER BY specialization_name ASC");

// Handle form submission
if (isset($_POST['submit'])) {
    $specialization_id = intval($_POST['specialization_id']);
    $doctor_name = trim($_POST['doctor_name']);
    $clinic_address = trim($_POST['clinic_address']);
    $consultancy_fees = trim($_POST['consultancy_fees']);
    $contact_no = trim($_POST['contact_no']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($doctor_name == '' || $clinic_address == '' || $consultancy_fees == '' || $contact_no == '' || $email == '' || $password == '' || $confirm_password == '') {
        $msg = "⚠ Please fill all fields!";
        $alertClass = "alert-warning";
    } elseif ($password !== $confirm_password) {
        $msg = "⚠ Passwords do not match!";
        $alertClass = "alert-warning";
    } else {
        $stmt = $con->prepare("SELECT id FROM doctors WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $msg = "⚠ Email already exists!";
            $alertClass = "alert-warning";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $con->prepare("INSERT INTO doctors (specialization_id, department_id, doctor_name, clinic_address, consultancy_fees, contact_no, email, password) VALUES (?, 0, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issdsss", $specialization_id, $doctor_name, $clinic_address, $consultancy_fees, $contact_no, $email, $hashed_password);
            if ($stmt->execute()) {
                $msg = "✅ Doctor added successfully!";
                $alertClass = "alert-success";
            } else {
                $msg = "❌ Error: " . $stmt->error;
                $alertClass = "alert-danger";
            }
        }
    }
}
?>

<div class="main-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="card">
                    <div class="card-header">
                        ➕ Add Doctor
                    </div>
                    <div class="card-body">

                        <?php if($msg != ''): ?>
                            <div class="alert <?= $alertClass; ?>"><?= $msg; ?></div>
                        <?php endif; ?>

                        <form method="post">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Specialization</label>
                                <select name="specialization_id" class="form-select" required>
                                    <option value="">-- Select Specialization --</option>
                                    <?php while($row = mysqli_fetch_assoc($specResult)): ?>
                                        <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['specialization_name']) ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Doctor Name</label>
                                <input type="text" name="doctor_name" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Clinic Address</label>
                                <input type="text" name="clinic_address" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Consultancy Fees</label>
                                <input type="number" name="consultancy_fees" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Contact No</label>
                                <input type="text" name="contact_no" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Confirm Password</label>
                                <input type="password" name="confirm_password" class="form-control" required>
                            </div>

                            <button type="submit" name="submit" class="btn btn-primary">Add Doctor</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.main-content {
    margin-left: 260px; /* Adjust to your sidebar width */
    padding: 40px;
    min-height: 100vh;
    background: #f5f6f8;
}

.card {
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    box-shadow: 0 3px 12px rgba(0,0,0,0.05);
}

.card:hover {
    box-shadow: 0 5px 18px rgba(0,0,0,0.08);
}

.card-header {
    background: #fff;
    border-bottom: 1px solid #e5e7eb;
    font-weight: 600;
    font-size: 1.1rem;
    padding: 16px 20px;
}

.form-control, .form-select {
    border-radius: 8px;
    border: 1px solid #d1d5db;
}

.form-control:focus, .form-select:focus {
    border-color: #4b5563;
    box-shadow: none;
}

.btn-primary {
    background: #444;
    border: none;
    border-radius: 6px;
}

.btn-primary:hover {
    background: #000;
}

.alert {
    border-radius: 10px;
    font-size: 15px;
    margin-bottom: 1.2rem;
}

@media (max-width: 991px) {
    .main-content {
        margin-left: 0;
        padding: 20px;
    }
}
</style>
