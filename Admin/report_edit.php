<?php
session_start();
require 'db.php';
include 'assets/include/sidebar.php';

// =================== FETCH REPORT ===================
$id = intval($_GET['id'] ?? 0);
$stmt = $con->prepare("SELECT * FROM patient_reports WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$report = $stmt->get_result()->fetch_assoc();
if (!$report) die("Report not found.");

// =================== HANDLE UPDATE ===================
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
    $report_description = $_POST['report_description'] ?? '';
    $medicine_prescription = $_POST['medicine_prescription'] ?? '';

    // File upload
    $report_file = $report['report_file'];
    if (!empty($_FILES['report_file']['name'])) {
        $folder = "uploads/reports/";
        if (!file_exists($folder)) mkdir($folder, 0777, true);
        $filename = time() . "_" . basename($_FILES['report_file']['name']);
        $target = $folder . $filename;
        if (move_uploaded_file($_FILES['report_file']['tmp_name'], $target)) {
            $report_file = $target;
        }
    }

    $stmt = $con->prepare("
        UPDATE patient_reports SET
        report_date=?, patient_id=?, doctor_id=?, patient_name=?, age=?, contact=?, address=?, blood_group=?,
        doctor_name=?, doctor_contact=?, report_title=?, report_description=?, medicine_prescription=?, report_file=?
        WHERE id=?
    ");
    $stmt->bind_param("siisssssssssssi",
        $report_date, $patient_id, $doctor_id,
        $patient_name, $age, $contact, $address, $blood_group,
        $doctor_name, $doctor_contact, $report_title, $report_description,
        $medicine_prescription, $report_file, $id
    );

    if ($stmt->execute()) {
        $msg = "✅ Report updated successfully.";
        // Refresh report data
        $stmt->close();
        $stmt = $con->prepare("SELECT * FROM patient_reports WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $report = $stmt->get_result()->fetch_assoc();
    } else {
        $error = "❌ Database Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Patient Report</title>
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<style>
body { margin:0; font-family:'Segoe UI',sans-serif; background:#f5f7fa; display:flex; }
.main { flex:1; max-width:900px; margin-left:220px; padding:30px; }
.container { background:#fff; padding:25px; border-radius:10px; box-shadow:0 6px 18px rgba(0,0,0,0.1); margin:100px; margin-top:20px; }
h2 { margin-bottom:20px; }
label { font-weight:600; display:block; margin-top:15px; }
input, select, textarea { width:100%; padding:10px; border:1px solid #ccc; border-radius:6px; font-size:14px; }
button { background:#007bff; color:#fff; border:none; padding:10px 20px; border-radius:6px; margin-top:20px; cursor:pointer; }
button:hover { background:#0056b3; }
.msg { margin-bottom:15px; padding:10px; border-radius:6px; }
.success { background:#e6ffef; color:#0a7b3d; }
.error { background:#ffeaea; color:#a40000; }
.row { display:flex; gap:15px; flex-wrap:wrap; }
.row .col { flex:1; position:relative; min-width:150px; }
.suggestions { border:1px solid #ccc; max-height:150px; overflow-y:auto; background:#fff; position:absolute; z-index:20; width:100%; display:none; }
.suggestions div { padding:8px; cursor:pointer; }
.suggestions div:hover { background:#f0f0f0; }
#report_description_editor, #medicine_prescription_editor { height:150px; }
</style>
<script src="https://cdn.quilljs.com/1.3.7/quill.js"></script>
</head>
<body>
<div class="main">
<div class="container">
<h2>✏️ Edit Patient Report</h2>
<?php if($msg): ?><div class="msg success"><?= $msg ?></div><?php endif; ?>
<?php if($error): ?><div class="msg error"><?= $error ?></div><?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <label>Report Date</label>
    <input type="date" name="report_date" value="<?=htmlspecialchars($report['report_date'])?>" required>

    <div class="row">
        <div class="col">
            <label>Patient Name</label>
            <input type="text" id="patient_name" name="patient_name" value="<?=htmlspecialchars($report['patient_name'])?>" autocomplete="off" required>
            <input type="hidden" id="patient_id" name="patient_id" value="<?=$report['patient_id']?>">
            <div id="patient_suggestions" class="suggestions"></div>
        </div>
        <div class="col">
            <label>Doctor Name</label>
            <input type="text" id="doctor_name" name="doctor_name" value="<?=htmlspecialchars($report['doctor_name'])?>" autocomplete="off" required>
            <input type="hidden" id="doctor_id" name="doctor_id" value="<?=$report['doctor_id']?>">
            <div id="doctor_suggestions" class="suggestions"></div>
        </div>
    </div>

    <div class="row">
        <div class="col"><label>Age</label><input type="number" name="age" id="age" value="<?=htmlspecialchars($report['age'])?>"></div>
        <div class="col"><label>Contact</label><input type="text" name="contact" id="contact" value="<?=htmlspecialchars($report['contact'])?>"></div>
    </div>

    <label>Address</label>
    <input type="text" name="address" id="address" value="<?=htmlspecialchars($report['address'])?>">

    <label>Blood Group</label>
    <input type="text" name="blood_group" id="blood_group" value="<?=htmlspecialchars($report['blood_group'])?>">

    <div class="row">
        <div class="col"><label>Doctor Contact</label><input type="text" name="doctor_contact" id="doctor_contact" value="<?=htmlspecialchars($report['doctor_contact'])?>"></div>
    </div>

    <label>Report Title</label>
    <input type="text" name="report_title" value="<?=htmlspecialchars($report['report_title'])?>" required>

    <label>Report Description</label>
    <div id="report_description_editor"><?= $report['report_description'] ?></div>
    <input type="hidden" name="report_description" id="report_description">

    <label>Medicine Prescription</label>
    <div id="medicine_prescription_editor"><?= $report['medicine_prescription'] ?></div>
    <input type="hidden" name="medicine_prescription" id="medicine_prescription">

    <label>Replace Report File (optional)</label>
    <input type="file" name="report_file" accept=".pdf,.jpg,.jpeg,.png">
    <?php if($report['report_file']): ?>
        <p>Current File: <a href="<?=$report['report_file']?>" target="_blank">View</a></p>
    <?php endif; ?>

    <button type="submit">Save Changes</button>
    <a href="report_search.php" class="btn" style="margin-left:10px; background:#6c757d; color:#fff; padding:10px 20px; border-radius:6px; text-decoration:none;">Cancel</a>
</form>
</div>
</div>

<script>
// ==================== Patient Autocomplete ====================
const patientInput=document.getElementById('patient_name');
const patientIdInput=document.getElementById('patient_id');
const patientSuggestions=document.getElementById('patient_suggestions');
patientInput.addEventListener('input',async function(){
    const q=this.value;if(!q){patientSuggestions.style.display='none';return;}
    const res=await fetch(`report_create.php?action=search_patient&q=${encodeURIComponent(q)}`);
    const data=await res.json();
    patientSuggestions.innerHTML='';
    if(data.success&&data.patients.length){
        data.patients.forEach(p=>{
            const div=document.createElement('div');
            div.textContent=p.patient_name;
            div.dataset.id=p.id;
            div.addEventListener('click',async()=>{
                patientInput.value=p.patient_name;
                patientIdInput.value=p.id;
                patientSuggestions.style.display='none';
                const resp=await fetch(`report_create.php?action=get_patient&id=${p.id}`);
                const json=await resp.json();
                if(json.success){
                    document.getElementById('age').value=json.patient.age||'';
                    document.getElementById('contact').value=json.patient.contact_no||'';
                }
            });
            patientSuggestions.appendChild(div);
        });
        patientSuggestions.style.display='block';
    } else patientSuggestions.style.display='none';
});

// ==================== Doctor Autocomplete ====================
const doctorInput=document.getElementById('doctor_name');
const doctorIdInput=document.getElementById('doctor_id');
const doctorSuggestions=document.getElementById('doctor_suggestions');
doctorInput.addEventListener('input',async function(){
    const q=this.value;if(!q){doctorSuggestions.style.display='none';return;}
    const res=await fetch(`report_create.php?action=search_doctor&q=${encodeURIComponent(q)}`);
    const data=await res.json();
    doctorSuggestions.innerHTML='';
    if(data.success&&data.doctors.length){
        data.doctors.forEach(d=>{
            const div=document.createElement('div');
            div.textContent=d.doctor_name;
            div.dataset.id=d.id;
            div.addEventListener('click',async()=>{
                doctorInput.value=d.doctor_name;
                doctorIdInput.value=d.id;
                doctorSuggestions.style.display='none';
                const resp=await fetch(`report_create.php?action=get_doctor&id=${d.id}`);
                const json=await resp.json();
                if(json.success) document.getElementById('doctor_contact').value=json.doctor.contact_no||'';
            });
            doctorSuggestions.appendChild(div);
        });
        doctorSuggestions.style.display='block';
    } else doctorSuggestions.style.display='none';
});

// ==================== Quill Editors ====================
var quillDesc=new Quill('#report_description_editor',{theme:'snow',modules:{toolbar:[[{header:[1,2,false]}],['bold','italic','underline','strike'],[{list:'ordered'},{list:'bullet'}],['link','image','code-block'],['clean']]}});
var quillMed=new Quill('#medicine_prescription_editor',{theme:'snow',modules:{toolbar:[[{header:[1,2,false]}],['bold','italic','underline','strike'],[{list:'ordered'},{list:'bullet'}],['link','image','code-block'],['clean']]}});
document.querySelector('form').onsubmit=function(){
    document.getElementById('report_description').value=quillDesc.root.innerHTML;
    document.getElementById('medicine_prescription').value=quillMed.root.innerHTML;
};
</script>
</body>
</html>
