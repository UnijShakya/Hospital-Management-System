<?php
include '../db.php';
include 'patient_sidebar.php';

// Fetch all patient reports with patient & doctor info
$sql = "
SELECT r.*, p.patient_name AS p_name, p.gender, p.age, p.contact_no,
       d.doctor_name AS d_name, d.contact_no AS d_contact
FROM patient_reports r
JOIN patients p ON r.patient_id = p.id
JOIN doctors d ON r.doctor_id = d.id
ORDER BY r.created_at DESC
";
$result = $con->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Patient Reports</title>
<style>
body {
  margin: 0;
  font-family: "Poppins", Arial, sans-serif;
  background: #f8f9fa;
  display: flex;
}

/* --- Main Content --- */
.main-content {
  margin-left: 230px; /* Space for sidebar */
  padding: 30px;
  flex: 1;
}

h2 {
  font-size: 24px;
  color: #333;
  margin-bottom: 20px;
  text-align: center;
  font-weight: 600;
}

/* --- Table --- */
table {
  width: 100%;
  border-collapse: collapse;
  background: #fff;
  font-size: 14px;
}

th, td {
  padding: 10px 12px;
  border: 1px solid #dee2e6;
  text-align: center;
}

th {
  background: #f1f1f1;
  font-weight: 600;
  color: #333;
}

tr:nth-child(even) {
  background: #f9f9f9;
}

tr:hover {
  background: #f2f2f2;
}

/* --- Button --- */
a.view-btn {
  background: #0d6efd;
  color: #fff;
  text-decoration: none;
  padding: 5px 10px;
  border-radius: 4px;
  font-size: 13px;
}

a.view-btn:hover {
  background: #0b5ed7;
}

/* --- Responsive --- */
@media(max-width: 768px) {
  .main-content {
    margin-left: 0;
    padding: 15px;
  }
  table {
    font-size: 12px;
  }
}
</style>
</head>
<body>

<div class="main-content">
  <h2>Patient Reports</h2>

  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Date</th>
        <th>Patient Name</th>
        <th>Doctor Name</th>
        <th>Report Title</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php
      if ($result->num_rows > 0) {
          $i = 1;
          while ($row = $result->fetch_assoc()) {
              echo "<tr>
                      <td>{$i}</td>
                      <td>".date('d M Y', strtotime($row['report_date']))."</td>
                      <td>".htmlspecialchars($row['p_name'])."</td>
                      <td>".htmlspecialchars($row['d_name'])."</td>
                      <td>".htmlspecialchars($row['report_title'])."</td>
                      <td><a class='view-btn' href='view_report.php?id={$row['id']}'>View</a></td>
                    </tr>";
              $i++;
          }
      } else {
          echo "<tr><td colspan='6'>No reports found</td></tr>";
      }
      ?>
    </tbody>
  </table>
</div>

</body>
</html>
