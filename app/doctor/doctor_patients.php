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

// Get all appointments for this doctor
$stmt = $pdo->prepare("
    SELECT 
        a.appointment_date,
        a.comment,
        p.first_name AS patient_first_name,
        p.last_name AS patient_last_name
    FROM appointments a
    JOIN patients p ON a.patient_id = p.id
    WHERE a.doctor_id = ?
    ORDER BY a.appointment_date ASC
");
$stmt->execute([$doctor_id]);
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="lt">
<head>
<meta charset="UTF-8">
<title>Mano pacientai</title>
<style>
  body { font-family: Arial; background:#f8f9fa; text-align:center; padding-top:40px; }
  h1 { cursor:pointer; }
  h2 { margin-bottom:20px; }
  table { margin:auto; border-collapse:collapse; width:80%; background:white;
          box-shadow:0 0 10px rgba(0,0,0,0.1); border-radius:6px; overflow:hidden; }
  th, td { border:1px solid #ddd; padding:10px; }
  th { background:#007bff; color:white; }
  tr:nth-child(even){ background:#f2f2f2; }
  .back-btn { display:inline-block; margin-top:25px; padding:10px 20px; background:#6c757d;
              color:white; text-decoration:none; border-radius:5px; }
  .back-btn:hover { background:#5a6268; }
</style>
</head>
<body>

<h1 onclick="window.location.href='doctor_home.php'">HOSPITAL</h1>
<h2>Pacientai daktaro <?= htmlspecialchars($doctor['first_name'] . ' ' . $doctor['last_name']) ?></h2>
<p><strong>Specializacija:</strong> <?= htmlspecialchars($doctor['specialization']) ?></p>

<?php if (empty($appointments)): ?>
  <p>Šiuo metu neturite jokių užregistruotų vizitų.</p>
<?php else: ?>
    <table>
    <tr>
      <th>Data</th>
      <th>Laikas</th>
      <th>Pacientas</th>
      <th>Komentaras</th>
      <th>Veiksmas</th>
    </tr>
    <?php
    // include appointment id and patient id in the query
    $stmt = $pdo->prepare("
        SELECT 
            a.id AS appointment_id,
            a.appointment_date,
            a.comment,
            p.id AS patient_id,
            p.first_name AS patient_first_name,
            p.last_name AS patient_last_name
        FROM appointments a
        JOIN patients p ON a.patient_id = p.id
        WHERE a.doctor_id = ?
        ORDER BY a.appointment_date ASC
    ");
    $stmt->execute([$doctor_id]);
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($appointments as $a):
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
  </table>

<?php endif; ?>

<a href="doctor_home.php" class="back-btn">Grįžti atgal</a>

</body>
</html>
