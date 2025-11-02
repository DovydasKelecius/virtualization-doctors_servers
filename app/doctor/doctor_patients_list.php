<?php
session_start();
if (!isset($_SESSION['doctor_id'])) {
    header("Location: doctorlogin.php");
    exit;
}

require '../db.php';

$date = $_GET['date'] ?? null;
if (!$date) {
    header("Location: doctor_patients.php");
    exit;
}

$doctor_id = $_SESSION['doctor_id'];

$stmt = $pdo->prepare("
    SELECT 
        a.appointment_date,
        a.comment,
        p.first_name AS patient_first_name,
        p.last_name AS patient_last_name
    FROM appointments a
    JOIN patients p ON a.patient_id = p.id
    WHERE a.doctor_id = ? AND DATE(a.appointment_date) = ?
    ORDER BY a.appointment_date ASC
");
$stmt->execute([$doctor_id, $date]);
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="lt">
<head>
<meta charset="UTF-8">
<title>Pacientų sąrašas</title>
<style>
  body { font-family: Arial; text-align:center; background:#f8f9fa; padding-top:40px; }
  table { margin:auto; border-collapse:collapse; width:70%; background:white;
          box-shadow:0 0 10px rgba(0,0,0,0.1); }
  th, td { border:1px solid #ccc; padding:10px; }
  th { background:#007bff; color:white; }
  tr:nth-child(even){ background:#f2f2f2; }
  a { text-decoration:none; color:#007bff; display:inline-block; margin-top:20px; }
</style>
</head>
<body>

<h1 onclick="window.location.href='doctor_home.php'" style="cursor:pointer;">HOSPITAL</h1>
<h2>Pacientai <?= htmlspecialchars($date) ?></h2>

<?php if (empty($appointments)): ?>
  <p>Tą dieną nėra registracijų.</p>
<?php else: ?>
  <table>
    <tr>
      <th>Laikas</th>
      <th>Pacientas</th>
      <th>Komentaras</th>
    </tr>
    <?php foreach ($appointments as $a): ?>
      <tr>
        <td><?= substr($a['appointment_date'], 11, 5) ?></td>
        <td><?= htmlspecialchars($a['patient_first_name'] . ' ' . $a['patient_last_name']) ?></td>
        <td><?= htmlspecialchars($a['comment']) ?></td>
      </tr>
    <?php endforeach; ?>
  </table>
<?php endif; ?>

<a href="doctor_patients.php">← Pasirinkti kitą dieną</a>

</body>
</html>
