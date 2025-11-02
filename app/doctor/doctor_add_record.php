<?php
session_start();
if (!isset($_SESSION['doctor_id'])) {
    header("Location: doctorlogin.php");
    exit;
}

require '../db.php';

$doctor_id = $_SESSION['doctor_id'];
$patient_id = $_GET['patient_id'] ?? null;
$appointment_id = $_GET['appointment_id'] ?? null;

if (!$patient_id) {
    die("Trūksta paciento ID.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event = trim($_POST['event'] ?? '');
    $diagnosis = trim($_POST['diagnosis'] ?? '');

    if ($event && $diagnosis) {
        $stmt = $pdo->prepare("
            INSERT INTO medical_records (patient_id, doctor_id, appointment_id, event, diagnosis)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$patient_id, $doctor_id, $appointment_id, $event, $diagnosis]);

        header("Location: doctor_patient_details.php?patient_id=$patient_id&appointment_id=$appointment_id");
        exit;
    } else {
        $error = "Užpildykite visus laukus!";
    }
}
?>
<!DOCTYPE html>
<html lang="lt">
<head>
<meta charset="UTF-8">
<title>Įvesti apsilankymo duomenis</title>
<style>
  body { font-family: Arial; background:#f8f9fa; text-align:center; padding:40px; }
  .card { background:white; display:inline-block; padding:25px; border-radius:10px;
          box-shadow:0 0 10px rgba(0,0,0,0.1); width:400px; text-align:left; }
  h1 { cursor:pointer; }
  label { font-weight:bold; display:block; margin-top:15px; }
  textarea { width:100%; height:80px; resize:none; padding:8px; margin-top:5px;
             border:1px solid #ccc; border-radius:4px; font-family:Arial; }
  .btn { background:#28a745; color:white; border:none; padding:10px 20px;
         border-radius:5px; cursor:pointer; margin-top:15px; width:100%; }
  .btn:hover { background:#218838; }
  .error { color:red; margin-bottom:10px; }
  .back { display:inline-block; margin-top:20px; color:#555; text-decoration:none; }
  .back:hover { text-decoration:underline; }
</style>
</head>
<body>

<h1 onclick="window.location.href='doctor_home.php'">HOSPITAL</h1>
<h2>Įvesti apsilankymo duomenis</h2>

<div class="card">
  <?php if (!empty($error)): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <form method="POST">
    <label for="event">Įvykis (dėl ko atvyko pacientas):</label>
    <textarea name="event" id="event" required></textarea>

    <label for="diagnosis">Išrašas (diagnozė):</label>
    <textarea name="diagnosis" id="diagnosis" required></textarea>

    <button type="submit" class="btn">Išsaugoti duomenis</button>
  </form>
</div>

<a href="doctor_patient_details.php?patient_id=<?= $patient_id ?>&appointment_id=<?= $appointment_id ?>" class="back">← Grįžti atgal</a>

</body>
</html>
