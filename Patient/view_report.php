<?php
require 'db.php';

// Validate and fetch report ID
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) die("Invalid Report ID.");

// Fetch report details
$stmt = $con->prepare("
    SELECT 
        r.*, 
        p.patient_name AS p_name, p.age AS p_age, p.gender AS p_gender, p.contact_no AS p_contact, p.email AS p_email,
        d.doctor_name AS d_name, d.contact_no AS d_contact, d.clinic_address AS d_address, d.email AS d_email
    FROM patient_reports r
    JOIN patients p ON r.patient_id = p.id
    JOIN doctors d ON r.doctor_id = d.id
    WHERE r.id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$report = $stmt->get_result()->fetch_assoc();
if (!$report) die("Report not found.");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Report - <?= htmlspecialchars($report['p_name']) ?></title>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<style>
body {
    font-family: 'Segoe UI', sans-serif;
    background: #f2f2f2;
    margin: 0;
    padding: 20px;
}
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
}
.export-btn:hover { background: #0056b3; }

.a4-container {
    width: 210mm;
    min-height: 297mm;
    background: #fff;
    margin: auto;
    padding: 18mm 15mm;
    box-shadow: 0 0 8px rgba(0,0,0,0.15);
    box-sizing: border-box;
    position: relative;
}
.header {
    text-align: center;
    border-bottom: 2px solid #000;
    padding-bottom: 8px;
    margin-bottom: 25px;
}
.header h1 {
    margin: 0;
    font-size: 1.9rem;
    letter-spacing: 1px;
    text-transform: uppercase;
}
.header p {
    margin: 2px 0;
    font-size: 0.9rem;
}

.report-date {
    text-align: right;
    margin-bottom: 15px;
    font-weight: 500;
}

.personal-details {
    margin-bottom: 25px;
    font-size: 0.95rem;
    line-height: 1.7;
}
.personal-details h3 {
    margin-bottom: 8px;
    font-size: 1.05rem;
    text-decoration: underline;
}
.personal-details table {
    width: 100%;
    border-collapse: collapse;
}
.personal-details td {
    padding: 5px 10px 5px 0;
    vertical-align: top;
}
.personal-details td:first-child,
.personal-details td:nth-child(3) {
    width: 22%;
    font-weight: bold;
    color: #333;
}

.section-title {
    font-weight: bold;
    font-size: 1.05rem;
    margin-top: 25px;
    margin-bottom: 8px;
    text-decoration: underline;
}
.report-description, .medicine-section {
    font-size: 0.95rem;
    color: #222;
    line-height: 1.6;
    margin-bottom: 15px;
}
.footer {
    position: absolute;
    bottom: 25mm;
    left: 15mm;
    right: 15mm;
    text-align: right;
    font-size: 0.9rem;
    color: #555;
}
</style>
</head>
<body>

<button class="export-btn" id="downloadPDF">Download Report</button>

<div class="a4-container" id="reportContent">

    <div class="header">
        <h1>Nepal Hospital [NPLH]</h1>
        <p>Chhakupat, Lalitpur, Nepal</p>
        <p>Phone: +977 01 2345678 | Email: info@nepalhospital.com</p>
    </div>

    <div class="report-date">
        Date: <?= date('d-m-Y', strtotime($report['report_date'])) ?>
    </div>

    <!-- Patient & Doctor Details -->
    <div class="personal-details">
        <h3>Patient Information</h3>
        <table>
            <tr>
                <td>Name:</td>
                <td><?= htmlspecialchars($report['p_name']) ?></td>
                <td>DOB:</td>
                <td><?= htmlspecialchars($report['DOB'] ?? '-') ?></td>
            </tr>
            <tr>
                <td>Age:</td>
                <td><?= htmlspecialchars($report['p_age']) ?></td>
                <td>Gender:</td>
                <td><?= htmlspecialchars($report['p_gender']) ?></td>
            </tr>
            <tr>
                <td>Contact:</td>
                <td><?= htmlspecialchars($report['p_contact']) ?></td>
                <td>Blood Group:</td>
                <td><?= htmlspecialchars($report['blood_group'] ?? '-') ?></td>
            </tr>
            <tr>
                <td>Doctor:</td>
                <td><?= htmlspecialchars($report['d_name']) ?></td>
                <td>Doctor Contact:</td>
                <td><?= htmlspecialchars($report['d_contact']) ?></td>
            </tr>
            <tr>
                <td>Clinic Address:</td>
                <td colspan="3"><?= htmlspecialchars($report['d_address'] ?? '-') ?></td>
            </tr>
            <?php if (!empty($report['d_email'])): ?>
            <tr>
                <td>Doctor Email:</td>
                <td colspan="3"><?= htmlspecialchars($report['d_email']) ?></td>
            </tr>
            <?php endif; ?>
        </table>
    </div>

    <div class="section-title">Report Title:</div>
    <div><?= htmlspecialchars($report['report_title']) ?></div>

    <?php if (!empty($report['report_description'])): ?>
        <div class="section-title">Report Description:</div>
        <div class="report-description"><?= nl2br($report['report_description']) ?></div>
    <?php endif; ?>

    <?php if (!empty($report['medicine_prescription'])): ?>
        <div class="section-title">Medicine Prescription:</div>
        <div class="medicine-section"><?= nl2br($report['medicine_prescription']) ?></div>
    <?php endif; ?>

    <?php if (!empty($report['report_file'])): ?>
        <div class="section-title">Attached File:</div>
        <p><a href="<?= htmlspecialchars($report['report_file']) ?>" target="_blank">View File</a></p>
    <?php endif; ?>

    <div class="footer">
        <p>Doctor: <?= htmlspecialchars($report['d_name']) ?></p>
        <p>Signature: ________________________</p>
    </div>

</div>

<script>
document.getElementById('downloadPDF').addEventListener('click', () => {
    const element = document.getElementById("reportContent");
    html2pdf().set({
        margin: 10,
        filename: 'Report_<?= $id ?>.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, useCORS: true },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
    }).from(element).save();
});
</script>

</body>
</html>
