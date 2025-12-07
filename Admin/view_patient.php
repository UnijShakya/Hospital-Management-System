<?php
session_start();
require '../db.php'; // adjust path if needed

// ---------- Auth ----------
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// ---------- Validate ID ----------
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $error = "Invalid patient request!";
} else {
    $id = intval($_GET['id']);

    // Fetch patient and doctor name (if doctor_id is set)
    $stmt = $con->prepare("
        SELECT p.*, d.doctor_name
        FROM patients p
        LEFT JOIN doctors d ON p.doctor_id = d.id
        WHERE p.id = ?
        LIMIT 1
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $patient = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$patient) {
        $error = "Patient not found!";
    } else {
        // Fetch patient history entries
        // Expected patient_history table columns: id, patient_id, visit_date (datetime or date), note (text), attachment (varchar path)
        $histStmt = $con->prepare("SELECT * FROM patient_history WHERE patient_id = ? ORDER BY visit_date DESC");
        $histStmt->bind_param("i", $id);
        $histStmt->execute();
        $history = $histStmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $histStmt->close();
    }
}

// ---------- Handle downloads ----------
// 1) Word (.doc) download: simple approach — send HTML with Word headers
if (isset($_GET['download']) && $_GET['download'] === 'doc' && isset($patient) && !$error) {
    $filename = "patient_{$patient['id']}_history.doc";
    header("Content-Type: application/msword; charset=UTF-8");
    header("Content-Disposition: attachment; filename=\"$filename\"");
    // Minimal inline CSS that Word will accept
    echo "<html><head><meta charset='utf-8'><style>
        body{font-family: Arial, sans-serif; font-size:14px;}
        h1,h2{margin:0 0 10px 0;}
        table{border-collapse:collapse;width:100%}
        td,th{border:1px solid #ccc;padding:8px;vertical-align:top}
    </style></head><body>";
    echo "<h1>Patient History</h1>";
    echo "<h2>Patient: " . htmlspecialchars($patient['patient_name']) . " (ID: " . $patient['id'] . ")</h2>";
    echo "<p><strong>Email:</strong> " . htmlspecialchars($patient['email']) . "<br>";
    echo "<strong>Contact:</strong> " . htmlspecialchars($patient['contact_no']) . "<br>";
    echo "<strong>Age:</strong> " . htmlspecialchars($patient['age']) . " &nbsp; <strong>Gender:</strong> " . htmlspecialchars($patient['gender']) . "<br>";
    if (!empty($patient['doctor_name'])) echo "<strong>Doctor:</strong> " . htmlspecialchars($patient['doctor_name']) . "<br>";
    echo "<strong>Created:</strong> " . htmlspecialchars($patient['created_at']) . "</p>";

    echo "<h3>History</h3>";
    if (empty($history)) {
        echo "<p>No history entries found.</p>";
    } else {
        echo "<table><thead><tr><th>Visit Date</th><th>Notes</th><th>Attachment</th></tr></thead><tbody>";
        foreach ($history as $h) {
            $att = $h['attachment'] ? htmlspecialchars(basename($h['attachment'])) : '—';
            echo "<tr>";
            echo "<td>" . htmlspecialchars($h['visit_date']) . "</td>";
            echo "<td>" . nl2br(htmlspecialchars($h['note'])) . "</td>";
            echo "<td>" . $att . "</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
    }
    echo "</body></html>";
    exit();
}

// 2) PDF download (server-side using Dompdf if available) OR fallback to print
if (isset($_GET['download']) && $_GET['download'] === 'pdf' && isset($patient) && !$error) {
    // Try Dompdf
    if (class_exists('\\Dompdf\\Dompdf')) {
        // build HTML content (same as printable HTML below) and render PDF
        ob_start();
        ?>
        <!doctype html>
        <html>
        <head>
            <meta charset="utf-8">
            <style>
                body{font-family: DejaVu Sans, Arial, sans-serif; font-size:12px; color:#111}
                .header{margin-bottom:20px}
                h1{font-size:20px;margin:0}
                table{width:100%;border-collapse:collapse}
                th,td{border:1px solid #ddd;padding:8px;text-align:left;vertical-align:top}
                .meta p{margin:4px 0}
            </style>
        </head>
        <body>
            <div class="header">
                <h1>Patient History</h1>
                <div class="meta">
                    <p><strong>Patient:</strong> <?= htmlspecialchars($patient['patient_name']) ?> (ID: <?= $patient['id'] ?>)</p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($patient['email']) ?> | <strong>Contact:</strong> <?= htmlspecialchars($patient['contact_no']) ?></p>
                    <p><strong>Doctor:</strong> <?= htmlspecialchars($patient['doctor_name'] ?? '—') ?> | <strong>Created:</strong> <?= htmlspecialchars($patient['created_at']) ?></p>
                </div>
            </div>

            <h3>History</h3>
            <?php if (empty($history)): ?>
                <p>No history entries found.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr><th style="width:140px">Visit Date</th><th>Notes</th><th style="width:160px">Attachment</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($history as $h): ?>
                        <tr>
                            <td><?= htmlspecialchars($h['visit_date']) ?></td>
                            <td><?= nl2br(htmlspecialchars($h['note'])) ?></td>
                            <td>
                                <?php if (!empty($h['attachment'])): ?>
                                    <?= htmlspecialchars(basename($h['attachment'])) ?>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </body>
        </html>
        <?php
        $html = ob_get_clean();

        // Create PDF
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        // (Optional) set paper size/orientation
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $pdfOutput = $dompdf->output();

        // Send PDF to browser
        $filename = "patient_{$patient['id']}_history.pdf";
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo $pdfOutput;
        exit();
    } else {
        // Dompdf not installed — redirect back but show a friendly message in UI (below)
        $pdf_error = "Server-side PDF generation not available. Use the Print button to save as PDF from your browser, or install Dompdf (composer require dompdf/dompdf).";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>View Patient - History</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
/* Printable / on-screen styling */
body { background:#f8f9fa; font-family: Arial, sans-serif; }
.container { max-width: 1100px; }
.card { margin-top: 20px; }
.meta p { margin: 3px 0; }
.history-note { white-space: pre-wrap; }
.table-nowrap td, .table-nowrap th { white-space: nowrap; }
@media print {
  .no-print { display: none !important; }
  .card { box-shadow:none; border:none; }
}
</style>
</head>
<body class="bg-light">
<div class="container">
    <div class="d-flex justify-content-between align-items-center mt-4">
        <h2>👤 Patient Details & History</h2>
        <div class="no-print">
            <?php if (!isset($error) && isset($patient)): ?>
                <a class="btn btn-outline-primary btn-sm" href="view_patient.php?id=<?= $patient['id'] ?>&download=doc">Download as Word</a>
                <a class="btn btn-outline-success btn-sm" href="view_patient.php?id=<?= $patient['id'] ?>&download=pdf">Download as PDF</a>
                <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">Print / Save as PDF</button>
            <?php endif; ?>
            <a class="btn btn-secondary btn-sm" href="patient.php">Back</a>
        </div>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger mt-3"><?= htmlspecialchars($error) ?></div>
    <?php else: ?>

        <?php if (isset($pdf_error)): ?>
            <div class="alert alert-warning mt-3 no-print"><?= htmlspecialchars($pdf_error) ?></div>
        <?php endif; ?>

        <div class="card shadow-sm mt-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h4 class="mb-1"><?= htmlspecialchars($patient['patient_name']) ?> <small class="text-muted">#<?= $patient['id'] ?></small></h4>
                        <div class="meta mb-2">
                            <p><strong>Email:</strong> <?= htmlspecialchars($patient['email']) ?></p>
                            <p><strong>Contact:</strong> <?= htmlspecialchars($patient['contact_no']) ?> &nbsp; <strong>Age:</strong> <?= htmlspecialchars($patient['age']) ?> &nbsp; <strong>Gender:</strong> <?= htmlspecialchars($patient['gender']) ?></p>
                            <p><strong>Doctor:</strong> <?= htmlspecialchars($patient['doctor_name'] ?? '—') ?> &nbsp; <strong>Created:</strong> <?= htmlspecialchars($patient['created_at']) ?></p>
                        </div>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <!-- Optional quick actions -->
                        <small class="text-muted">Admin view</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mt-3 mb-5">
            <div class="card-body">
                <h5>Medical History</h5>

                <?php if (empty($history)): ?>
                    <p class="text-muted">No history entries found for this patient.</p>
                <?php else: ?>
                    <div class="accordion" id="historyAccordion">
                    <?php foreach ($history as $idx => $h): 
                        $entryId = 'entry' . $h['id'];
                    ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading<?= $entryId ?>">
                                <button class="accordion-button <?= $idx>0 ? 'collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $entryId ?>" aria-expanded="<?= $idx===0 ? 'true' : 'false' ?>" aria-controls="collapse<?= $entryId ?>">
                                    <?= htmlspecialchars($h['visit_date']) ?> — <?= htmlspecialchars(substr($h['note'],0,80)) ?><?= strlen($h['note'])>80 ? '...' : '' ?>
                                </button>
                            </h2>
                            <div id="collapse<?= $entryId ?>" class="accordion-collapse collapse <?= $idx===0 ? 'show' : '' ?>" aria-labelledby="heading<?= $entryId ?>" data-bs-parent="#historyAccordion">
                                <div class="accordion-body">
                                    <p class="text-muted"><strong>Visit Date:</strong> <?= htmlspecialchars($h['visit_date']) ?></p>
                                    <p class="history-note"><?= nl2br(htmlspecialchars($h['note'])) ?></p>

                                    <?php if (!empty($h['attachment'])): ?>
                                        <p><strong>Attachment:</strong>
                                            <?php
                                                $path = $h['attachment'];
                                                // Safe output; you might want to validate path or use a download script
                                                if (file_exists('../' . $path)) {
                                                    echo '<a href="../' . htmlspecialchars($path) . '" target="_blank">Download</a>';
                                                } else {
                                                    echo htmlspecialchars(basename($path)) . " (file not found)";
                                                }
                                            ?>
                                        </p>
                                    <?php endif; ?>

                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
