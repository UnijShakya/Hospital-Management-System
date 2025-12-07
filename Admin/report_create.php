<?php
session_start();
require 'db.php';

// =================== AJAX HANDLERS ===================
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    $action = $_GET['action'];

    // Search patients
    if ($action === 'search_patient') {
        $q = trim($_GET['q'] ?? '');
        if ($q) {
            $stmt = $con->prepare("SELECT id, patient_name FROM patients WHERE patient_name LIKE CONCAT('%', ?, '%') LIMIT 10");
            $stmt->bind_param('s', $q);
            $stmt->execute();
            $res = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            echo json_encode(['success'=>true,'patients'=>$res]);
        } else echo json_encode(['success'=>false,'patients'=>[]]);
        exit;
    }

    // Search doctors
    if ($action === 'search_doctor') {
        $q = trim($_GET['q'] ?? '');
        if ($q) {
            $stmt = $con->prepare("SELECT id, doctor_name FROM doctors WHERE doctor_name LIKE CONCAT('%', ?, '%') LIMIT 10");
            $stmt->bind_param('s', $q);
            $stmt->execute();
            $res = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            echo json_encode(['success'=>true,'doctors'=>$res]);
        } else echo json_encode(['success'=>false,'doctors'=>[]]);
        exit;
    }

    // Get patient info
    if ($action === 'get_patient') {
        $id = intval($_GET['id'] ?? 0);
        if ($id) {
            $stmt = $con->prepare("SELECT age, contact_no FROM patients WHERE id=?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $res = $stmt->get_result()->fetch_assoc();
            echo json_encode(['success'=>(bool)$res,'patient'=>$res]);
        } else echo json_encode(['success'=>false]);
        exit;
    }

    // Get doctor info
    if ($action === 'get_doctor') {
        $id = intval($_GET['id'] ?? 0);
        if ($id) {
            $stmt = $con->prepare("SELECT contact_no FROM doctors WHERE id=?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $res = $stmt->get_result()->fetch_assoc();
            echo json_encode(['success'=>(bool)$res,'doctor'=>$res]);
        } else echo json_encode(['success'=>false]);
        exit;
    }

    echo json_encode(['success'=>false]);
    exit;
}

include 'assets/include/sidebar.php';

// =================== FORM SUBMISSION ===================
$msg = $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $report_date = $_POST['report_date'] ?: date('Y-m-d');
    $patient_id = (int)($_POST['patient_id'] ?? 0);
    $doctor_id = (int)($_POST['doctor_id'] ?? 0);
    $patient_name = trim($_POST['patient_name']);
    $age = (int)($_POST['age'] ?? NULL);
    $contact = trim($_POST['contact']);
    $address = trim($_POST['address']);
    $blood_group = trim($_POST['blood_group']);
    $doctor_name = trim($_POST['doctor_name']);
    $doctor_contact = trim($_POST['doctor_contact']);
    $report_title = trim($_POST['report_title']);
    $report_description = $_POST['report_description_html'] ?? '';
    $medicine_prescription = $_POST['medicine_prescription_html'] ?? '';

    $report_file = NULL;
    if (!empty($_FILES['report_file']['name'])) {
        $folder = "uploads/reports/";
        if (!file_exists($folder)) mkdir($folder, 0777, true);
        $filename = time() . "_" . basename($_FILES['report_file']['name']);
        $target = $folder . $filename;
        if (move_uploaded_file($_FILES['report_file']['tmp_name'], $target)) $report_file = $target;
    }

    $stmt = $con->prepare("
        INSERT INTO patient_reports 
        (report_date, patient_id, doctor_id, patient_name, age, contact, address, blood_group,
         doctor_name, doctor_contact, report_title, report_description, medicine_prescription, report_file)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("siisssssssssss", 
        $report_date, $patient_id, $doctor_id,
        $patient_name, $age, $contact, $address, $blood_group,
        $doctor_name, $doctor_contact, $report_title, $report_description,
        $medicine_prescription, $report_file
    );

    if ($stmt->execute()) $msg = "✅ Report created successfully.";
    else $error = "❌ Database Error: ".$stmt->error;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Patient Report</title>

    <!-- Quill Editor (Local/Offline) -->
    <link href="assets/quill/quill.snow.css" rel="stylesheet">
    <script src="assets/quill/quill.min.js"></script>

    <style>
    body {
        margin: 0;
        font-family: 'Segoe UI', sans-serif;
        background: #f5f7fa;
        display: flex;
    }
    .main { flex: 1; max-width: 900px; margin-left: 220px; padding: 30px; }
    .container {
        background: #fff; padding: 25px; border-radius: 10px;
        box-shadow: 0 6px 18px rgba(0,0,0,0.1);
        margin: 100px; margin-top: 20px;
    }
    h2 { margin-bottom: 20px; }
    label { font-weight: 600; display: block; margin-top: 15px; }
    input, select, textarea {
        width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px;
    }
    button {
        background: #007bff; color: #fff; border: none;
        padding: 10px 20px; border-radius: 6px; margin-top: 20px; cursor: pointer;
    }
    button:hover { background: #0056b3; }
    .msg { margin-bottom: 15px; padding: 10px; border-radius: 6px; }
    .success { background: #e6ffef; color: #0a7b3d; }
    .error { background: #ffeaea; color: #a40000; }
    .row { display: flex; gap: 15px; flex-wrap: wrap; }
    .col { flex: 1; position: relative; min-width: 150px; }
    .suggestions {
        border: 1px solid #ccc; max-height: 150px; overflow-y: auto;
        background: #fff; position: absolute; z-index: 20; width: 100%; display: none;
    }
    .suggestions div { padding: 8px; cursor: pointer; }
    .suggestions div:hover { background: #f0f0f0; }

    /* Quill custom box */
    .editor-box {
        background: #fff; border: 1px solid #ccc; border-radius: 6px;
    }
    .ql-toolbar.ql-snow {
        border-top-left-radius: 6px;
        border-top-right-radius: 6px;
    }
    .ql-container.ql-snow {
        border-bottom-left-radius: 6px;
        border-bottom-right-radius: 6px;
        min-height: 150px;
    }
    </style>
</head>
<body>
    <div class="main">
        <div class="container">
            <h2>🩺 Create Patient Report</h2>
            <?php if($msg): ?><div class="msg success"><?= $msg ?></div><?php endif; ?>
            <?php if($error): ?><div class="msg error"><?= $error ?></div><?php endif; ?>

            <form method="POST" enctype="multipart/form-data" onsubmit="prepareEditors()">
                <label>Report Date</label>
                <input type="date" name="report_date" value="<?= date('Y-m-d') ?>" required>

                <div class="row">
                    <div class="col">
                        <label>Patient Name</label>
                        <input type="text" id="patient_name" name="patient_name" placeholder="Type to search..." autocomplete="off" required>
                        <input type="hidden" id="patient_id" name="patient_id">
                        <div id="patient_suggestions" class="suggestions"></div>
                    </div>
                    <div class="col">
                        <label>Doctor Name</label>
                        <input type="text" id="doctor_name" name="doctor_name" placeholder="Type to search..." autocomplete="off" required>
                        <input type="hidden" id="doctor_id" name="doctor_id">
                        <div id="doctor_suggestions" class="suggestions"></div>
                    </div>
                </div>

                <div class="row">
                    <div class="col"><label>Age</label><input type="number" name="age" id="age" readonly></div>
                    <div class="col"><label>Contact</label><input type="text" name="contact" id="contact" readonly></div>
                </div>

                <label>Address</label>
                <input type="text" name="address">

                <label>Blood Group</label>
                <input type="text" name="blood_group">

                <div class="row">
                    <div class="col"><label>Doctor Contact</label><input type="text" name="doctor_contact" id="doctor_contact" readonly></div>
                </div>

                <label>Report Title</label>
                <input type="text" name="report_title" required>

                <label>Report Description</label>
                <div id="report_description" class="editor-box"></div>
                <input type="hidden" name="report_description_html" id="report_description_html">

                <label>Medicine Prescription</label>
                <div id="medicine_prescription" class="editor-box"></div>
                <input type="hidden" name="medicine_prescription_html" id="medicine_prescription_html">

                <label>Attach Report File (PDF/Image optional)</label>
                <input type="file" name="report_file" accept=".pdf,.jpg,.jpeg,.png">

                <button type="submit">Save Report</button>
            </form>
        </div>
    </div>

<script>
// ================= Initialize Offline Quill Editors =================
var quill1 = new Quill('#report_description', {
    theme: 'snow',
    modules: { toolbar: [['bold','italic','underline'],[{list:'ordered'},{list:'bullet'}],['link']] }
});
var quill2 = new Quill('#medicine_prescription', {
    theme: 'snow',
    modules: { toolbar: [['bold','italic','underline'],[{list:'ordered'},{list:'bullet'}],['link']] }
});

function prepareEditors(){
    document.getElementById('report_description_html').value = quill1.root.innerHTML;
    document.getElementById('medicine_prescription_html').value = quill2.root.innerHTML;
}

// ================= Patient autocomplete =================
const patientInput = document.getElementById('patient_name');
const patientIdInput = document.getElementById('patient_id');
const patientSuggestions = document.getElementById('patient_suggestions');

patientInput.addEventListener('input', async function() {
    const q = this.value;
    if (!q) { patientSuggestions.style.display = 'none'; return; }
    const res = await fetch(`report_create.php?action=search_patient&q=${encodeURIComponent(q)}`);
    const data = await res.json();
    patientSuggestions.innerHTML = '';
    if (data.success && data.patients.length) {
        data.patients.forEach(p => {
            const div = document.createElement('div');
            div.textContent = p.patient_name;
            div.dataset.id = p.id;
            div.addEventListener('click', async () => {
                patientInput.value = p.patient_name;
                patientIdInput.value = p.id;
                patientSuggestions.style.display = 'none';
                const resp = await fetch(`report_create.php?action=get_patient&id=${p.id}`);
                const json = await resp.json();
                if (json.success) {
                    document.getElementById('age').value = json.patient.age || '';
                    document.getElementById('contact').value = json.patient.contact_no || '';
                }
            });
            patientSuggestions.appendChild(div);
        });
        patientSuggestions.style.display = 'block';
    } else patientSuggestions.style.display = 'none';
});
document.addEventListener('click', e => {
    if (!patientSuggestions.contains(e.target) && e.target !== patientInput)
        patientSuggestions.style.display = 'none';
});

// ================= Doctor autocomplete =================
const doctorInput = document.getElementById('doctor_name');
const doctorIdInput = document.getElementById('doctor_id');
const doctorSuggestions = document.getElementById('doctor_suggestions');

doctorInput.addEventListener('input', async function() {
    const q = this.value;
    if (!q) { doctorSuggestions.style.display = 'none'; return; }
    const res = await fetch(`report_create.php?action=search_doctor&q=${encodeURIComponent(q)}`);
    const data = await res.json();
    doctorSuggestions.innerHTML = '';
    if (data.success && data.doctors.length) {
        data.doctors.forEach(d => {
            const div = document.createElement('div');
            div.textContent = d.doctor_name;
            div.dataset.id = d.id;
            div.addEventListener('click', async () => {
                doctorInput.value = d.doctor_name;
                doctorIdInput.value = d.id;
                doctorSuggestions.style.display = 'none';
                const resp = await fetch(`report_create.php?action=get_doctor&id=${d.id}`);
                const json = await resp.json();
                if (json.success) document.getElementById('doctor_contact').value = json.doctor.contact_no || '';
            });
            doctorSuggestions.appendChild(div);
        });
        doctorSuggestions.style.display = 'block';
    } else doctorSuggestions.style.display = 'none';
});
document.addEventListener('click', e => {
    if (!doctorSuggestions.contains(e.target) && e.target !== doctorInput)
        doctorSuggestions.style.display = 'none';
});
</script>
</body>
</html>
