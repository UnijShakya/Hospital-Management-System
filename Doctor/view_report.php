<?php
require 'db.php';

// Validate report ID
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) die("Invalid Report ID.");

// Fetch full report details
$stmt = $con->prepare("SELECT * FROM patient_reports WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$report = $stmt->get_result()->fetch_assoc();
if (!$report) die("Report not found.");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Report - <?= htmlspecialchars($report['patient_name']) ?></title>

<!-- PDF Download Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<style>
body {
    font-family: 'Segoe UI', sans-serif;
    background: #f4f6f8;
    margin: 0;
    padding: 20px 0;
}
.a4-container {
    width: 210mm;
    min-height: 280mm;
    background: #fff;
    margin: 0 auto;
    padding: 20mm;
    box-sizing: border-box;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    position: relative;
}

/* Header */
.header {
    text-align: center;
    margin-bottom: 20px;
    border-bottom: 2px solid #000;  /* <-- line under hospital details */
    padding-bottom: 10px;
}
.header h1 { margin: 0; font-size: 1.8rem; font-weight: 700; text-transform: uppercase; }
.header p { margin: 3px 0; font-size: 0.9rem; color: #555; }

/* Report Date */
.report-date { text-align: right; font-weight: 600; margin-bottom: 15px; }

/* Personal Details */
.personal-details {
    display: flex;
    flex-wrap: wrap;
    gap: 15px 40px;
    margin-bottom: 25px;
}
.personal-details .item {
    min-width: 180px;
    font-size: 0.95rem;
}
.personal-details .item strong { color: #1a3c6e; }

/* Section Titles */
.section-title {
    font-weight: 600;
    font-size: 1rem;
    margin-top: 20px;
    margin-bottom: 8px;
    border-bottom: 1px solid #ddd;
    padding-bottom: 4px;
}

/* Report Description */
.report-description {
    line-height: 1.6;
    font-size: 0.95rem;
    color: #333;
    min-height: 120px;
    padding: 10px;
    background: #fdfdfd;
    border-radius: 6px;
}

/* Footer */
.footer {
    position: absolute;
    bottom: 20mm;
    left: 20mm;
    right: 20mm;
    text-align: right;
    font-size: 0.9rem;
    color: #555;
}

/* PDF Button */
.export-btn {
    position: fixed;
    top: 20px;
    right: 20px;
    background: #007bff;
    color: #fff;
    border: none;
    padding: 10px 18px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    z-index: 9999;
}
.export-btn:hover { background: #0056b3; }
</style>
</head>
<body>

<button class="export-btn" id="downloadPDF">Download PDF</button>

<div class="a4-container" id="reportContent">

    <!-- Header -->
    <div class="header">
        <h1>NPLH - Nepal Hospital</h1>
        <p>Chhakupat, Lalitpur, Nepal</p>
        <p>Phone: +977 01 2345678 | Email: unijshakya29@gmail.com</p>
    </div>

    <!-- Report Date -->
    <div class="report-date">
        Date: <?= date('d-m-Y', strtotime($report['report_date'])) ?>
    </div>
<div class="section-title" style="margin-top:10px;">Patient Information</div>
    <!-- Personal Details -->
    <div class="personal-details">
        <div class="item"><strong>Name:</strong> <?= htmlspecialchars($report['patient_name']) ?></div>
        <div class="item"><strong>DOB:</strong> <?= htmlspecialchars($report['DOB'] ?? '-') ?></div>
        <div class="item"><strong>Age:</strong> <?= htmlspecialchars($report['age']) ?></div>
        <div class="item"><strong>Gender:</strong> <?= htmlspecialchars($report['gender']) ?></div>
        <div class="item"><strong>Contact:</strong> <?= htmlspecialchars($report['contact']) ?></div>
        <div class="item"><strong>Blood Group:</strong> <?= htmlspecialchars($report['blood_group'] ?? '-') ?></div>
        <div class="item"><strong>Doctor:</strong> <?= htmlspecialchars($report['doctor_name']) ?></div>
    </div>

    <!-- Report Title -->
    <div class="section-title">Report Title:</div>
    <div><?= htmlspecialchars($report['report_title']) ?></div>

    <!-- Report Description (Quill format HTML) -->
    <div class="section-title">Report Description:</div>
    <div class="report-description">
        <?= $report['report_description'] ?>
    </div>

    <!-- Attached File -->
    <?php if (!empty($report['report_file'])): ?>
    <div class="section-title">Attached File:</div>
    <p><a href="<?= htmlspecialchars($report['report_file']) ?>" target="_blank">View File</a></p>
    <?php endif; ?>

    <!-- Footer -->
    <div class="footer">
        <p>Doctor: <?= htmlspecialchars($report['doctor_name']) ?></p>
        <p>Signature: ________________________</p>
    </div>
</div>

<script>
document.getElementById("downloadPDF").addEventListener("click", function () {
    const element = document.getElementById("reportContent");
    html2pdf().set({
        margin: [5,5,5,5],
        filename: 'Report_<?= $id ?>.pdf',
        image: { type: 'jpeg', quality: 1 },
        html2canvas: { scale: 2, useCORS: true },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
        pagebreak: { mode: ['avoid-all','css','legacy'] }
    }).from(element).save();
});
</script>

</body>
</html>
