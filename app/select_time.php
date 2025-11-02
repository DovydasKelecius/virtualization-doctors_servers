<?php
session_start();
require 'db.php';

if (!isset($_SESSION['patient_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['doctor_id']) || !isset($_GET['date'])) {
    die("Trūksta duomenų (gydytojas arba data).");
}

$doctor_id = $_GET['doctor_id'];
$date = $_GET['date'];

// Gauti gydytojo informaciją
$stmt = $pdo->prepare("SELECT * FROM doctors WHERE id = ?");
$stmt->execute([$doctor_id]);
$doctor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$doctor) {
    die("Gydytojas nerastas.");
}

// Generuoti laiko tarpus (kas 30 min nuo work_start iki work_end)
$time_slots = [];
$startTime = new DateTime($doctor['work_start']);
$endTime = new DateTime($doctor['work_end']);

while ($startTime < $endTime) {
    $time_slots[] = $startTime->format('H:i');
    $startTime->modify('+30 minutes');
}

// Jei forma pateikta (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $time = $_POST['time'] ?? '';
    $comment = $_POST['comment'] ?? '';
    $patient_id = $_SESSION['patient_id'];
    $specialization = $doctor['specialization'];
    $datetime = $date . ' ' . $time;

    // Įrašyti vizitą į duomenų bazę
    $stmt = $pdo->prepare("
        INSERT INTO appointments (patient_id, specialization, appointment_date, comment)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$patient_id, $specialization, $datetime, $comment]);

    $_SESSION['appointment_success'] = [
        'doctor' => $doctor['first_name'] . ' ' . $doctor['last_name'],
        'specialization' => $doctor['specialization'],
        'datetime' => $datetime,
        'comment' => $comment
    ];

    header("Location: appointment_confirm.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="lt">
<head>
<meta charset="UTF-8">
<title>Pasirinkite laiką</title>
<style>
  body { font-family: Arial; background-color: #f8f9fa; text-align: center; margin-top: 40px; }
  .top { font-size: 24px; font-weight: bold; cursor: pointer; margin-bottom: 20px; }
  .doctor-info { background: #fff; display: inline-block; padding: 20px; border-radius: 10px;
                 box-shadow: 0 0 10px rgba(0,0,0,0.1); margin-bottom: 30px; }
  form { background: #fff; display: inline-block; padding: 20px; border-radius: 10px;
         box-shadow: 0 0 10px rgba(0,0,0,0.1); margin-top: 20px; text-align: left; }
  select, textarea, button { display: block; width: 100%; padding: 10px; margin-top: 10px;
                             border-radius: 5px; border: 1px solid #ccc; }
  textarea { height: 100px; resize: none; }
  button { background-color: #28a745; color: white; cursor: pointer; margin-top: 15px; }
  button:hover { background-color: #218838; }
  .back-btn { display: inline-block; margin-top: 20px; background: #6c757d; color: white;
              padding: 10px 20px; border-radius: 5px; text-decoration: none; }
</style>
</head>
<body>

<div class="top" onclick="window.location.href='patient_home.php'">HOSPITAL</div>

<div class="doctor-info">
  <h2>Dr. <?= htmlspecialchars($doctor['first_name'] . ' ' . $doctor['last_name']) ?></h2>
  <p><strong>Specializacija:</strong> <?= htmlspecialchars($doctor['specialization']) ?></p>
  <p><strong>Pasirinkta data:</strong> <?= htmlspecialchars($date) ?></p>
</div>

<h3>Pasirinkite laiką</h3>
<form method="POST">
  <label>Laikas:</label>
  <select name="time" required>
    <option value="">-- Pasirinkite laiką --</option>
    <?php foreach ($time_slots as $t): ?>
      <option value="<?= $t ?>"><?= $t ?></option>
    <?php endforeach; ?>
  </select>

  <label>Komentarai (pasirinktinai):</label>
  <textarea name="comment" placeholder="Trumpai aprašykite savo problemą..."></textarea>

  <button type="submit">Patvirtinti vizitą</button>
</form>

<a href="javascript:history.back()" class="back-btn">Grįžti atgal</a>

</body>
</html>
