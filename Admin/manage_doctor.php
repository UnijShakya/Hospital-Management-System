<?php
include('db.php'); 
include('assets/include/sidebar.php'); 
$msg = '';

// Fetch all doctors
$doctorResult = mysqli_query($con, "
    SELECT d.id, d.doctor_name, d.clinic_address, d.consultancy_fees, d.contact_no, d.email, s.specialization_name, s.id as specialization_id
    FROM doctors d
    LEFT JOIN doctor_specialization s ON d.specialization_id = s.id
    ORDER BY d.id DESC
");

// Fetch specializations for edit dropdown
$specResult = mysqli_query($con, "SELECT * FROM doctor_specialization ORDER BY specialization_name ASC");

// Handle Edit Submission
if (isset($_POST['edit_submit'])) {
    $doctor_id = intval($_POST['doctor_id']);
    $specialization_id = intval($_POST['specialization_id']);
    $doctor_name = trim($_POST['doctor_name']);
    $clinic_address = trim($_POST['clinic_address']);
    $consultancy_fees = trim($_POST['consultancy_fees']);
    $contact_no = trim($_POST['contact_no']);

    $stmt = $con->prepare("
        UPDATE doctors SET 
            specialization_id=?, 
            doctor_name=?, 
            clinic_address=?, 
            consultancy_fees=?, 
            contact_no=?
        WHERE id=?
    ");
    $stmt->bind_param("issdsi", $specialization_id, $doctor_name, $clinic_address, $consultancy_fees, $contact_no, $doctor_id);

    if ($stmt->execute()) {
        $msg = "✅ Doctor updated successfully!";
        header("Location: manage_doctor.php");
        exit();
    } else {
        $msg = "❌ Error: " . $stmt->error;
    }
}
?>

<div class="main-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-12">

                <h2 class="text-center mb-4">📋 Manage Doctors</h2>

                <?php if($msg != ''): ?>
                    <div class="alert alert-info"><?= $msg ?></div>
                <?php endif; ?>

                <div class="card shadow mx-auto">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Specialization</th>
                                        <th>Doctor Name</th>
                                        <th>Clinic Address</th>
                                        <th>Fees</th>
                                        <th>Contact No</th>
                                        <th>Email</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $i = 1;
                                    mysqli_data_seek($doctorResult,0);
                                    while($row = mysqli_fetch_assoc($doctorResult)){ ?>
                                        <tr>
                                            <td><?= $i++ ?></td>
                                            <td><?= htmlspecialchars($row['specialization_name']) ?></td>
                                            <td><?= htmlspecialchars($row['doctor_name']) ?></td>
                                            <td><?= htmlspecialchars($row['clinic_address']) ?></td>
                                            <td><?= htmlspecialchars($row['consultancy_fees']) ?></td>
                                            <td><?= htmlspecialchars($row['contact_no']) ?></td>
                                            <td><?= htmlspecialchars($row['email']) ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id'] ?>">Edit</button>
                                            </td>
                                        </tr>

                                        <!-- Edit Modal -->
                                        <div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1" aria-hidden="true">
                                          <div class="modal-dialog">
                                            <div class="modal-content">
                                              <form method="post">
                                                  <div class="modal-header">
                                                    <h5 class="modal-title">Edit Doctor</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                  </div>
                                                  <div class="modal-body">
                                                      <input type="hidden" name="doctor_id" value="<?= $row['id'] ?>">
                                                      <div class="mb-3">
                                                          <label class="form-label fw-semibold">Specialization</label>
                                                          <select name="specialization_id" class="form-select" required>
                                                              <?php 
                                                              mysqli_data_seek($specResult, 0);
                                                              while($spec = mysqli_fetch_assoc($specResult)): ?>
                                                                  <option value="<?= $spec['id'] ?>" <?= $spec['id']==$row['specialization_id']?'selected':'' ?>>
                                                                      <?= htmlspecialchars($spec['specialization_name']) ?>
                                                                  </option>
                                                              <?php endwhile; ?>
                                                          </select>
                                                      </div>
                                                      <div class="mb-3">
                                                          <label class="form-label fw-semibold">Doctor Name</label>
                                                          <input type="text" name="doctor_name" class="form-control" value="<?= htmlspecialchars($row['doctor_name']) ?>" required>
                                                      </div>
                                                      <div class="mb-3">
                                                          <label class="form-label fw-semibold">Clinic Address</label>
                                                          <input type="text" name="clinic_address" class="form-control" value="<?= htmlspecialchars($row['clinic_address']) ?>" required>
                                                      </div>
                                                      <div class="mb-3">
                                                          <label class="form-label fw-semibold">Consultancy Fees</label>
                                                          <input type="number" name="consultancy_fees" class="form-control" value="<?= htmlspecialchars($row['consultancy_fees']) ?>" required>
                                                      </div>
                                                      <div class="mb-3">
                                                          <label class="form-label fw-semibold">Contact No</label>
                                                          <input type="text" name="contact_no" class="form-control" value="<?= htmlspecialchars($row['contact_no']) ?>" required>
                                                      </div>
                                                  </div>
                                                  <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" name="edit_submit" class="btn btn-primary">Save Changes</button>
                                                  </div>
                                              </form>
                                            </div>
                                          </div>
                                        </div>

                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<style>
.main-content {
    margin-left: 260px; /* sidebar width */
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

.table {
    border-radius: 8px;
    overflow: hidden;
}

.table thead {
    background: #f1f1f1;
    color: #111;
    font-weight: 600;
}

.table-hover tbody tr:hover {
    background-color: #f9fafb;
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

.modal-content {
    border-radius: 12px;
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
