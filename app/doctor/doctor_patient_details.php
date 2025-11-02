<?php
session_start();
if (!isset($_SESSION['doctor_id'])) {
    header("Location: doctorlogin.php");
    exit;
}

require '../db.php';

$appointment_id = $_GET['appointment_id'] ?? null;
$patient_id = $_GET['patient_id'] ?? null;

if (!$appointment_id || !$patient_id) {
    die("Trūksta duomenų (appointment_id arba patient_id).");
}

// Get patient info
$pstmt = $pdo->prepare("
    SELECT first_name, last_name, personal_code, phone, gender
    FROM patients
    WHERE id = ?
");
$pstmt->execute([$patient_id]);
$patient = $pstmt->fetch(PDO::FETCH_ASSOC);

// Get appointment details
$astmt = $pdo->prepare("
    SELECT appointment_date, comment, specialization
    FROM appointments
    WHERE id = ?
");
$astmt->execute([$appointment_id]);
$appointment = $astmt->fetch(PDO::FETCH_ASSOC);

// Get patient medical history (all previous appointments)
$hstmt = $pdo->prepare("
    SELECT appointment_date, specialization, comment
    FROM appointments
    WHERE patient_id = ?
    ORDER BY appointment_date DESC
");
$hstmt->execute([$patient_id]);
$history = $hstmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="lt">
<head>
<meta charset="UTF-8">
<title>Paciento informacija</title>
<style>
  body { font-family: Arial; background:#f8f9fa; text-align:center; padding:40px; }
  .card { background:white; display:inline-block; padding:25px; border-radius:10px;
          box-shadow:0 0 10px rgba(0,0,0,0.1); width:500px; text-align:left; }
  h1 { cursor:pointer; }
  h2 { text-align:center; }
  .info-row { margin:8px 0; }
  .label { font-weight:bold; color:#555; width:160px; display:inline-block; }
  .btn { display:inline-block; background:#007bff; color:white; padding:10px 20px; border-radius:5px;
         text-decoration:none; margin-top:20px; }
  .btn:hover { background:#0056b3; }
  .history { margin-top:30px; }
  table { width:100%; border-collapse:collapse; margin-top:10px; }
  th, td { border:1px solid #ccc; padding:8px; text-align:left; }
  th { background:#007bff; color:white; }
  tr:nth-child(even){ background:#f2f2f2; }
</style>
</head>
<body>

<h1 onclick="window.location.href='doctor_home.php'">HOSPITAL</h1>
<h2>Paciento informacija</h2>

<div class="card">
  <div class="info-row"><span class="label">Vardas:</span> <?= htmlspecialchars($patient['first_name']) ?></div>
  <div class="info-row"><span class="label">Pavardė:</span> <?= htmlspecialchars($patient['last_name']) ?></div>
  <div class="info-row"><span class="label">Asmens kodas:</span> <?= htmlspecialchars($patient['personal_code']) ?></div>
  <div class="info-row"><span class="label">Telefono numeris:</span> <?= htmlspecialchars($patient['phone']) ?></div>
  <div class="info-row"><span class="label">Lytis:</span> <?= htmlspecialchars($patient['gender']) ?></div>
  <div class="info-row"><span class="label">Specializacija:</span> <?= htmlspecialchars($appointment['specialization']) ?></div>
  <div class="info-row"><span class="label">Komentaras:</span> <?= htmlspecialchars($appointment['comment'] ?: '-') ?></div>

  <div class="history">
    <h3>Medicininė istorija</h3>
    <?php if (empty($history)): ?>
      <p>Nėra ankstesnių vizitų.</p>
    <?php else: ?>
      <table>
        <tr>
          <th>Data</th>
          <th>Specializacija</th>
          <th>Komentaras</th>
        </tr>
        <?php foreach ($history as $h): ?>
          <tr>
            <td><?= date('Y-m-d H:i', strtotime($h['appointment_date'])) ?></td>
            <td><?= htmlspecialchars($h['specialization']) ?></td>
            <td><?= htmlspecialchars($h['comment'] ?: '-') ?></td>
          </tr>
        <?php endforeach; ?>
      </table>
    <?php endif; ?>
  </div>

  <a href="doctor_patients.php" class="btn">← Grįžti į pacientų sąrašą</a>
</div>

</body>
</html>
