<?php
require 'db.php';
$msg = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $doctor_id = $_POST['doctor_id'];
    $break_date = $_POST['break_date'];
    $break_start = $_POST['break_start'];
    $break_end = $_POST['break_end'];

    // Simple validation: start < end
    if (strtotime($break_start) >= strtotime($break_end)) {
        $msg = "❌ Break start time must be before end time!";
    } else {
        $stmt = $con->prepare("INSERT INTO doctor_breaks (doctor_id, break_date, break_start, break_end) VALUES (?,?,?,?)");
        $stmt->bind_param("isss", $doctor_id, $break_date, $break_start, $break_end);
        $stmt->execute();
        $msg = "✅ Break added successfully!";
    }
}

// Fetch all breaks
$breaks = $con->query("
    SELECT db.id, d.doctor_name, db.break_date, db.break_start, db.break_end 
    FROM doctor_breaks db
    JOIN doctors d ON db.doctor_id = d.id
    ORDER BY db.break_date, db.break_start
");
?>
<!DOCTYPE html>
<html>
<head>
<title>Doctor Breaks</title>
<style>
body { font-family: Arial; background: #f6f8fb; }
.container { width: 600px; margin: 40px auto; background: #fff; padding: 25px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
label { display: block; margin-top: 10px; font-weight: bold; }
input, select, button { width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 6px; }
button { background: #2a9d8f; color: white; cursor: pointer; }
button:hover { background: #21867a; }
.msg { text-align: center; color: green; margin-top: 10px; }
table { width: 100%; border-collapse: collapse; margin-top: 20px; }
th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
th { background: #2a9d8f; color: white; }
</style>
</head>
<body>
<div class="container">
  <h2>Doctor Breaks</h2>
  
  <form method="POST">
    <label>Doctor:</label>
    <select name="doctor_id" required>
      <option value="">-- Select Doctor --</option>
      <?php
      $res = $con->query("SELECT id, doctor_name FROM doctors");
      while ($row = $res->fetch_assoc()) {
          echo "<option value='{$row['id']}'>{$row['doctor_name']}</option>";
      }
      ?>
    </select>

    <label>Break Date:</label>
    <input type="date" name="break_date" required>

    <label>Break Start:</label>
    <input type="time" name="break_start" required>

    <label>Break End:</label>
    <input type="time" name="break_end" required>

    <button type="submit">Add Break</button>
  </form>

  <?php if($msg): ?><p class="msg"><?= htmlspecialchars($msg) ?></p><?php endif; ?>

  <h3>Existing Breaks</h3>
  <table>
    <tr>
      <th>ID</th>
      <th>Doctor</th>
      <th>Date</th>
      <th>Start</th>
      <th>End</th>
    </tr>
    <?php while($row = $breaks->fetch_assoc()): ?>
    <tr>
      <td><?= $row['id'] ?></td>
      <td><?= htmlspecialchars($row['doctor_name']) ?></td>
      <td><?= $row['break_date'] ?></td>
      <td><?= $row['break_start'] ?></td>
      <td><?= $row['break_end'] ?></td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>
</body>
</html>
