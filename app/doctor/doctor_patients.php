<?php
session_start();
if (!isset($_SESSION['doctor_id'])) {
    header("Location: doctorlogin.php");
    exit;
}

require '../db.php';

$doctor_id = $_SESSION['doctor_id'];

// Get doctor info
$dstmt = $pdo->prepare("SELECT first_name, last_name, specialization FROM doctors WHERE id = ?");
$dstmt->execute([$doctor_id]);
$doctor = $dstmt->fetch(PDO::FETCH_ASSOC);

// Determine which filter was pressed
$filter = $_GET['filter'] ?? 'today'; // default is today
$today = date('Y-m-d');

// Base query
$query = "
    SELECT 
        a.id AS appointment_id,
        a.appointment_date,
        a.comment,
        p.id AS patient_id,
        p.first_name AS patient_first_name,
        p.last_name AS patient_last_name
    FROM appointments a
    JOIN patients p ON a.patient_id = p.id
    WHERE a.doctor_id = :doctor_id
";

// Apply filter for date range
if ($filter === 'today') {
    $query .= " AND DATE(a.appointment_date) = :today";
} elseif ($filter === 'future') {
    $query .= " AND DATE(a.appointment_date) > :today";
} else {
    $query .= " AND DATE(a.appointment_date) >= :today"; // fallback (safety)
}

$query .= " ORDER BY a.appointment_date ASC";

$stmt = $pdo->prepare($query);
$stmt->bindValue(':doctor_id', $doctor_id, PDO::PARAM_INT);
$stmt->bindValue(':today', $today);
$stmt->execute();
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="lt">
<head>
<meta charset="UTF-8">
<title>Mano pacientai</title>
<style>
  /* Base Styling from patient_card.php */
  body { 
      font-family: Arial, sans-serif; 
      background: #f8f9fa; 
      text-align: center; 
      padding-top: 40px; 
  }
  h1 { cursor:pointer; }
  h2 { margin-bottom: 5px; }
  
  /* Filter Button Styling */
  .filter-btn {
      background:#007bff; 
      color:white; 
      border:none; 
      padding:10px 20px;
      border-radius:5px; 
      cursor:pointer; 
      margin:5px 10px;
      font-weight: bold;
      transition: background-color 0.2s;
  }
  .filter-btn.active { 
      background:#0056b3; 
  }
  .filter-btn:hover { 
      background:#0056b3; 
  }
  
  /* Table Container/Card */
  .table-container {
      width: 90%;
      max-width: 900px;
      margin: 20px auto 0 auto;
      background: white;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      border-radius: 10px;
      overflow: hidden;
      padding: 20px;
      box-sizing: border-box;
  }
  
  /* Table Styling (Copied from patient_card.php) */
  table { 
      width: 100%; 
      border-collapse: collapse; 
      margin: 0;
      font-size: 0.95em;
  }
  th, td { 
      border: 1px solid #dee2e6; 
      padding: 10px; 
      text-align: left; 
  }
  th { 
      background-color: #007bff; 
      color: white; 
  }
  tr:nth-child(even){ 
      background:#f8f9fa; 
  }
  
  /* Link Styling */
  a { 
      color:#007bff; 
      text-decoration:none; 
  }
  a:hover { 
      text-decoration:underline; 
  }
  
  /* Back Button Styling (Aligned with patient_card's back-link) */
  .back-btn { 
      display: block; 
      width: 90%;
      max-width: 900px;
      margin: 25px auto 0 auto; 
      padding: 12px 20px; 
      background:#6c757d;
      color:white; 
      text-decoration:none; 
      border-radius:5px; 
      font-weight: bold;
      transition: background-color 0.2s;
  }
  .back-btn:hover { 
      background:#5a6268; 
  }
</style>
</head>
<body>

<h1 onclick="window.location.href='doctor_home.php'">HOSPITAL</h1>
<h2>Pacientai daktaro <?= htmlspecialchars($doctor['first_name'] . ' ' . $doctor['last_name']) ?></h2>
<p style="margin-bottom: 20px;"><strong>Specializacija:</strong> <?= htmlspecialchars($doctor['specialization']) ?></p>

<div>
  <button class="filter-btn <?= $filter === 'today' ? 'active' : '' ?>" 
          onclick="window.location.href='doctor_patients.php?filter=today'">Ši diena</button>
  <button class="filter-btn <?= $filter === 'future' ? 'active' : '' ?>" 
          onclick="window.location.href='doctor_patients.php?filter=future'">Ateities vizitai</button>
</div>

<div class="table-container">
<?php if (empty($appointments)): ?>
  <p style="text-align: center; color: #6c757d; font-style: italic;">
    Nėra vizitų <?= $filter === 'today' ? 'šiandien' : 'ateinančioms dienoms' ?>.
  </p>
<?php else: ?>
  <table>
    <thead>
      <tr>
        <th>Data</th>
        <th>Laikas</th>
        <th>Pacientas</th>
        <th>Komentaras</th>
        <th>Veiksmas</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($appointments as $a): 
          $date = date('Y-m-d', strtotime($a['appointment_date']));
          $time = date('H:i', strtotime($a['appointment_date']));
      ?>
        <tr>
          <td><?= htmlspecialchars($date) ?></td>
          <td><?= htmlspecialchars($time) ?></td>
          <td><?= htmlspecialchars($a['patient_first_name'] . ' ' . $a['patient_last_name']) ?></td>
          <td><?= htmlspecialchars($a['comment'] ?: '-') ?></td>
          <td>
            <a href="doctor_patient_details.php?appointment_id=<?= $a['appointment_id'] ?>&patient_id=<?= $a['patient_id'] ?>">Peržiūrėti</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>
</div>

<a href="doctor_home.php" class="back-btn">Grįžti atgal</a>

</body>
</html>