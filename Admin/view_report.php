<?php
require '../db.php';

$report_id = intval($_GET['report_id'] ?? 0);
if (!$report_id) die("Invalid report ID");

$stmt = $con->prepare("
    SELECT r.id, r.patient_id, r.doctor_id, r.report_title, r.report_description,
           r.medicine_prescription, r.report_file, r.report_date,
           p.patient_name, p.age, p.gender, p.contact_no AS patient_contact,
           d.doctor_name, d.contact_no AS doctor_contact
    FROM patient_reports r
    JOIN patients p ON r.patient_id = p.id
    JOIN doctors d ON r.doctor_id = d.id
    WHERE r.id = ?
");
$stmt->bind_param("i", $report_id);
$stmt->execute();
$report = $stmt->get_result()->fetch_assoc();
if (!$report) die("Report not found");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Patient Report - <?= htmlspecialchars($report['patient_name']) ?></title>

<!-- Local Quill CSS for rich content -->
<link href="../Admin/assets/quill/quill.snow.css" rel="stylesheet">

<!-- PDF Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<style>
body {
    font-family: 'Segoe UI', sans-serif;
    background: #f5f5f5;
    padding: 30px 0;
    margin: 0;
    text-align: center;
}

/* A4 Container */
.a4-container {
    width: 210mm;
    min-height: 280mm;
    background: #fff;
    margin: 0 auto;
    padding: 15mm;
    box-shadow: 0 0 8px rgba(0,0,0,0.15);
    box-sizing: border-box;
    position: relative;
    text-align: left;
    overflow: hidden;
}

/* Header */
.header {
    text-align: center;
    border-bottom: 2px solid #000;
    padding-bottom: 10px;
    margin-bottom: 20px;
}
.header h1 { margin: 0; font-size: 1.8rem; text-transform: uppercase; }
.header p { margin: 4px 0; font-size: 0.9rem; }

/* Report Date */
.report-date {
    text-align: right;
    font-weight: bold;
    margin-bottom: 20px;
}

/* Patient & Doctor Info */
.personal-details {
    width: 100%;
    margin-bottom: 25px;
    border-bottom: 1px solid #000;
    padding-bottom: 12px;
    font-size: 0.95rem;
}
.personal-details .row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 5px;
    flex-wrap: wrap;
}
.personal-details .row span {
    width: 48%;
}

/* Section Titles */
.section-title {
    font-weight: bold;
    font-size: 1.1rem;
    margin-top: 20px;
    margin-bottom: 8px;
    text-decoration: underline;
}

/* Rich Content */
.report-description, .medicine-prescription {
    line-height: 1.6;
    font-size: 0.95rem;
    color: #333;
}
.report-description p, .medicine-prescription p { margin-bottom: 8px; }
.report-description ul, .medicine-prescription ul { margin: 5px 0 5px 25px; }
.report-description ol, .medicine-prescription ol { margin: 5px 0 5px 25px; }

/* Footer */
.footer {
    position: absolute;
    bottom: 20mm;
    left: 15mm;
    right: 15mm;
    text-align: right;
    font-size: 0.9rem;
    color: #555;
}

/* Export Button */
.export-btn {
    position: fixed;
    top: 20px;
    right: 20px;
    background: #007bff;
    color: #fff;
    border: none;
    padding: 10px 20px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 15px;
    z-index: 9999;
    transition: 0.3s;
}
.export-btn:hover { background: #0056b3; }
</style>
</head>
<body>

<button class="export-btn" id="downloadPDF">Download Report</button>

<div class="a4-container" id="reportContent">

    <div class="header">
        <h1>[NPLH] Nepal Hospital</h1>
        <p>Chhakupat, Lalitpur, Nepal</p>
        <p>Phone: +977 01 2345678 | Email: unijshakya29@gmail.com</p>
    </div>

    <div class="report-date">
        Date: <?= !empty($report['report_date']) ? date('d-m-Y', strtotime($report['report_date'])) : '-' ?>
    </div>

    <div class="personal-details">
        <div class="row">
            <span><strong>Patient Name:</strong> <?= htmlspecialchars($report['patient_name']) ?></span>
            <span><strong>Age:</strong> <?= htmlspecialchars($report['age']) ?></span>
        </div>
        <div class="row">
            <span><strong>Gender:</strong> <?= htmlspecialchars($report['gender'] ?? '-') ?></span>
            <span><strong>Contact:</strong> <?= htmlspecialchars($report['patient_contact'] ?? '-') ?></span>
        </div>
        <div class="row">
            <span><strong>Doctor Name:</strong> <?= htmlspecialchars($report['doctor_name'] ?? '-') ?></span>
            <span><strong>Doctor Contact:</strong> <?= htmlspecialchars($report['doctor_contact'] ?? '-') ?></span>
        </div>
    </div>

    <div class="section-title">Report Title:</div>
    <div style="font-weight:600; font-size:1rem; margin-bottom:10px;">
        <?= htmlspecialchars($report['report_title']) ?>
    </div>

    <div class="section-title">Report Description:</div>
    <div class="report-description">
        <?= $report['report_description'] ?>
    </div>

    <?php if (!empty($report['medicine_prescription'])): ?>
    <div class="section-title">Medicine Prescription:</div>
    <div class="medicine-prescription">
        <?= $report['medicine_prescription'] ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($report['report_file'])): ?>
    <div class="section-title">Attached Report File:</div>
    <p><a href="../uploads/reports/<?= htmlspecialchars($report['report_file']) ?>" target="_blank">View File</a></p>
    <?php endif; ?>

    <div class="footer">
        <p>Doctor Signature: _______________________</p>
    </div>

</div>

<script>
document.getElementById("downloadPDF").addEventListener("click", function () {
    const element = document.getElementById("reportContent");

    const opt = {
        margin:       [5, 5, 5, 5],
        filename:     'Patient_Report_<?= $report_id ?>.pdf',
        image:        { type: 'jpeg', quality: 1 },
        html2canvas:  { scale: 2, useCORS: true },
        jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' },
        pagebreak:    { mode: ['avoid-all', 'css', 'legacy'] }
    };

    html2pdf().set(opt).from(element).save();
});
</script>

</body>
</html>
