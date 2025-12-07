<?php
session_start();
require '../db.php';
include('assets/include/sidebar.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Validate Patient ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<h4>Invalid patient ID</h4>";
    exit;
}

$patient_id = intval($_GET['id']);

// Fetch Patient Details
$stmt = $con->prepare("SELECT * FROM patients WHERE id = ?");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$patient = $stmt->get_result()->fetch_assoc();

if (!$patient) {
    echo "<h4>Patient not found</h4>";
    exit;
}

// Fetch Appointment History
$query = "
    SELECT s.*, d.doctor_name, spec.specialization_name
    FROM appointment_slots s
    LEFT JOIN doctors d ON s.doctor_id = d.id
    LEFT JOIN doctor_specialization spec ON d.specialization_id = spec.id
    WHERE s.patient_id = ?
    ORDER BY s.appointment_date DESC, s.appointment_time ASC
";
$appointments = $con->prepare($query);
$appointments->bind_param("i", $patient_id);
$appointments->execute();
$app_result = $appointments->get_result();

// Fetch Patient Reports
$report_stmt = $con->prepare("
    SELECT pr.*, d.doctor_name, d.contact_no as doctor_contact
    FROM patient_reports pr
    LEFT JOIN doctors d ON pr.doctor_id = d.id
    WHERE pr.patient_id = ?
    ORDER BY pr.report_date DESC
");
$report_stmt->bind_param("i", $patient_id);
$report_stmt->execute();
$report_result = $report_stmt->get_result();
?>

<div class="main-content">
    <div class="container-fluid">
        <h2 class="mb-4">👤 Patient Details</h2>

        <!-- Patient Info -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="mb-3 text-primary"><?= htmlspecialchars($patient['patient_name']) ?></h5>
                <div class="row">
                    <div class="col-md-6 mb-2"><strong>Email:</strong> <?= htmlspecialchars($patient['email']) ?></div>
                    <div class="col-md-6 mb-2"><strong>Contact:</strong> <?= htmlspecialchars($patient['contact_no']) ?>
                    </div>
                    <div class="col-md-6 mb-2"><strong>Age:</strong> <?= htmlspecialchars($patient['age']) ?></div>
                    <div class="col-md-6 mb-2"><strong>Gender:</strong> <?= htmlspecialchars($patient['gender']) ?>
                    </div>
                    <div class="col-md-6 mb-2"><strong>Created At:</strong>
                        <?= htmlspecialchars($patient['created_at']) ?></div>
                </div>
            </div>
        </div>

        <!-- Appointment Slots -->
        <h4 class="mb-3">🗓 Appointment Slot History</h4>
        <div class="card shadow mb-4">
            <div class="card-body table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">#</th>
                            <th>Doctor</th>
                            <th>Specialization</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($app_result->num_rows > 0): ?>
                        <?php $i = 1; while ($row = $app_result->fetch_assoc()): ?>
                        <tr>
                            <td class="text-center"><?= $i++ ?></td>
                            <td><?= htmlspecialchars($row['doctor_name'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($row['specialization_name'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($row['appointment_date']) ?></td>
                            <td><?= htmlspecialchars($row['appointment_time']) ?></td>
                            <td>
                                <?php
                                        $status = ucfirst($row['status'] ?? 'available');
                                        $badge = match($status) {
                                            'Booked' => 'success',
                                            'Cancelled' => 'danger',
                                            'Available' => 'secondary',
                                            default => 'warning'
                                        };
                                    ?>
                                <span class="badge bg-<?= $badge ?>"><?= htmlspecialchars($status) ?></span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">No appointment slots found.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Patient Reports -->
        <h4 class="mb-3">📄 Patient Reports</h4>
        <div class="card shadow mb-4">
            <div class="card-body table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">#</th>
                            <th>Date</th>
                            <th>Title</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($report_result->num_rows > 0): ?>
                        <?php $j = 1; while ($report = $report_result->fetch_assoc()): ?>
                        <tr>
                            <td class="text-center"><?= $j++ ?></td>
                            <td><?= htmlspecialchars($report['report_date']) ?></td>
                            <td><?= htmlspecialchars($report['report_title']) ?></td>
                            <td class="text-center">
                                <a href="view_report.php?report_id=<?= $report['id'] ?>" class="btn btn-sm btn-primary"
                                    target="_blank">View</a>
                            </td>

                        </tr>
                        <?php endwhile; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted">No reports found for this patient.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>




        <div class="mt-4">
            <a href="manage_patient.php" class="btn btn-secondary">← Back to Patients</a>
        </div>
    </div>
</div>

<style>
.main-content {
    margin-left: 260px;
    padding: 40px;
    min-height: 100vh;
    background: #f5f6f8;
    font-family: 'Roboto', sans-serif;
}

.card {
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 3px 12px rgba(0, 0, 0, 0.05);
}

.badge {
    font-size: 0.85rem;
    padding: 6px 10px;
    border-radius: 6px;
}

.table-hover tbody tr:hover {
    background-color: #f9fafb;
}

@media(max-width: 991px) {
    .main-content {
        margin-left: 0;
        padding: 20px;
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>