<?php
require 'db.php';

$id = intval($_GET['id'] ?? 0);
$stmt = $con->prepare("SELECT * FROM patient_reports WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$report = $stmt->get_result()->fetch_assoc();
if (!$report) die("Report not found.");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Patient Report - <?= htmlspecialchars($report['patient_name']) ?></title>

<!-- ✅ PDF Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<style>
body {
    font-family: 'Segoe UI', sans-serif;
    background: #f5f5f5;
    padding: 30px 0;
    margin: 0;
    text-align: center;
}

/* ✅ A4 Letterhead Container */
.a4-container {
    width: 210mm;
    min-height: 280mm;  /* was 297mm */
    background: #fff;
    margin: 0 auto;
    padding: 15mm 15mm; /* reduced padding */
    box-shadow: 0 0 8px rgba(0,0,0,0.15);
    box-sizing: border-box;
    position: relative;
    text-align: left;
    overflow: hidden; /* prevents content overflow */
}


/* Header - Hospital Info */
.header {
    text-align: center;
    border-bottom: 2px solid #000;
    padding-bottom: 10px;
    margin-bottom: 20px;
}
.header h1 {
    margin: 0;
    font-size: 1.8rem;
    text-transform: uppercase;
}
.header p {
    margin: 4px 0;
    font-size: 0.9rem;
}

/* Report Date */
.report-date {
    text-align: right;
    font-weight: bold;
    margin-bottom: 20px;
}

/* Patient Info */
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

/* Content Blocks */
.report-description, .medicine-prescription {
    line-height: 1.6;
    font-size: 0.95rem;
    color: #333;
}
.report-description p, .medicine-prescription p {
    margin-bottom: 8px;
}
.report-description ul, .medicine-prescription ul {
    margin: 5px 0 5px 25px;
}
.report-description ol, .medicine-prescription ol {
    margin: 5px 0 5px 25px;
}

/* Footer - Signature */
.footer {
    position: absolute;
    bottom: 20mm;
    left: 20mm;
    right: 20mm;
    text-align: right;
    font-size: 0.9rem;
    color: #555;
}

/* Export Button */
.export-btn {
    position: fixed;       /* fixed on screen */
    top: 20px;             /* distance from top */
    right: 20px;           /* distance from right */
    background: #007bff;
    color: #fff;
    border: none;
    padding: 10px 20px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 15px;
    z-index: 9999;         /* on top of everything */
    transition: 0.3s;
}
.export-btn:hover {
    background: #0056b3;
}


</style>
</head>
<body>

<!-- ✅ Export Button -->
<button class="export-btn" id="downloadPDF">Download Report</button>

<!-- Report Container -->
<div class="a4-container" id="reportContent">
    <div class="header">
        <h1>[NPLH] Nepal Hospital</h1>
        <p>Chhakupat, Lalitpur, Nepal</p>
        <p>Phone: +977 01 2345678 | Email: unijshakya29@gmail.com</p>
    </div>

    <div class="report-date">
        Date: <?= date('d-m-Y', strtotime($report['report_date'])) ?>
    </div>

    <div class="personal-details">
        <div class="row">
            <span><strong>Name:</strong> <?= htmlspecialchars($report['patient_name']) ?></span>
            <span><strong>DOB:</strong> <?= htmlspecialchars($report['dob'] ?? '-') ?></span>
        </div>
        <div class="row">
            <span><strong>Age:</strong> <?= htmlspecialchars($report['age']) ?></span>
            <span><strong>Gender:</strong> <?= htmlspecialchars($report['gender'] ?? '-') ?></span>
        </div>
        <div class="row">
            <span><strong>Contact:</strong> <?= htmlspecialchars($report['contact']) ?></span>
            <span><strong>Address:</strong> <?= htmlspecialchars($report['address']) ?></span>
        </div>
        <div class="row">
            <span><strong>Blood Group:</strong> <?= htmlspecialchars($report['blood_group'] ?? '-') ?></span>
            <span><strong>Doctor:</strong> <?= htmlspecialchars($report['doctor_name'] ?? '-') ?></span>
        </div>
    </div>

    <div class="section-title">Report Title:</div>
    <div style="font-weight:600; font-size:1rem; margin-bottom:10px;">
        <?= htmlspecialchars($report['report_title']) ?>
    </div>

    <div class="section-title">Report Description:</div>
    <div class="report-description">
        <?= $report['report_description'] // rich HTML ?>
    </div>

    <?php if (!empty($report['medicine_prescription'])): ?>
    <div class="section-title">Medicine Prescription:</div>
    <div class="medicine-prescription">
        <?= $report['medicine_prescription'] // rich HTML ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($report['report_file'])): ?>
    <div class="section-title">Attached Report File:</div>
    <p><a href="<?= htmlspecialchars($report['report_file']) ?>" target="_blank">View File</a></p>
    <?php endif; ?>

    <div class="footer">
        <p>Doctor: <?= htmlspecialchars($report['doctor_name'] ?? 'N/A') ?></p>
        <p>Signature: ______________________</p>
    </div>
</div>

<!-- ✅ PDF Export Script -->
<script>
document.getElementById("downloadPDF").addEventListener("click", function () {
    const element = document.getElementById("reportContent");

    const opt = {
        margin:       [5, 5, 5, 5], // smaller top/right/bottom/left margins
        filename:     'Patient_Report_<?= $id ?>.pdf',
        image:        { type: 'jpeg', quality: 1 },
        html2canvas:  { scale: 2, useCORS: true },
        jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' },
        pagebreak:    { mode: ['avoid-all', 'css', 'legacy'] } // 🚫 prevent auto-breaks
    };

    html2pdf().set(opt).from(element).save();
});
</script>
