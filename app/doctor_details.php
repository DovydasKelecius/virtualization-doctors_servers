<?php
session_start();
if (!isset($_SESSION['patient_id'])) {
    header("Location: login.php");
    exit;
}

require 'db.php';

if (!isset($_GET['doctor_id'])) {
    die("Nenurodytas gydytojas.");
}

$doctor_id = $_GET['doctor_id'];

// Gauti gydytojo informaciją
$stmt = $pdo->prepare("SELECT * FROM doctors WHERE id = ?");
$stmt->execute([$doctor_id]);
$doctor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$doctor) {
    die("Gydytojas nerastas.");
}

// Sukuriamos kitos 14 dienų datos
$days = [];
for ($i = 0; $i < 14; $i++) {
    $date = new DateTime("+$i day");
    $days[] = $date;
}

// Gauti visas gydytojo rezervacijas artimiausioms 14 dienų
$booked = $pdo->prepare("
    SELECT DATE(appointment_date) AS day, COUNT(*) AS count
    FROM appointments
    WHERE doctor_id = ? AND appointment_date >= CURRENT_DATE AND appointment_date < CURRENT_DATE + INTERVAL '14 days'
    GROUP BY day
");
$booked->execute([$doctor_id]);
$booked_days = $booked->fetchAll(PDO::FETCH_KEY_PAIR);

// Apskaičiuoti kiek laiko per dieną gydytojas dirba
$start = new DateTime($doctor['work_start']);
$end = new DateTime($doctor['work_end']);
$total_slots = 0;
while ($start < $end) {
    $total_slots++;
    $start->modify('+30 minutes');
}
?>
<!DOCTYPE html>
<html lang="lt">
<head>
<meta charset="UTF-8">
<title>Gydytojo informacija</title>
<style>
  body { font-family: Arial; background-color: #f8f9fa; text-align: center; margin-top: 40px; }
  .top { font-size: 28px; font-weight: bold; cursor: pointer; margin-bottom: 20px; }
  .doctor-info { background: #fff; display: inline-block; padding: 25px; border-radius: 12px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1); margin-bottom: 35px; }
  .calendar { display: flex; flex-wrap: wrap; justify-content: center; gap: 12px; max-width: 800px; margin: 0 auto; }
  .day { width: 130px; padding: 12px; border-radius: 10px; font-weight: bold; }
  .available { background-color: #28a745; color: white; cursor: pointer; transition: 0.2s; }
  .available:hover { background-color: #218838; }
  .unavailable { background-color: #6c757d; color: white; }
  a { text-decoration: none; color: inherit; }
  .back-btn { display: inline-block; margin-top: 25px; background: #6c757d; color: white;
    padding: 10px 25px; border-radius: 6px; text-decoration: none; font-weight: bold; }
  .back-btn:hover { background-color: #5a6268; }
</style>
</head>
<body>

  <div class="top" onclick="window.location.href='patient_home.php'">HOSPITAL</div>

  <div class="doctor-info">
    <h2>Dr. <?= htmlspecialchars($doctor['first_name'] . ' ' . $doctor['last_name']) ?></h2>
    <p><strong>Specializacija:</strong> <?= htmlspecialchars($doctor['specialization']) ?></p>
    <p><strong>Darbo laikas:</strong> <?= htmlspecialchars(substr($doctor['work_start'], 0, 5) . ' - ' . substr($doctor['work_end'], 0, 5)) ?></p>
  </div>

  <h3>Pasirinkite datą vizitui</h3>
  <div class="calendar">
    <?php foreach ($days as $d): ?>
      <?php
        $dayOfWeek = $d->format('N'); // 6 = Saturday, 7 = Sunday
        $dateStr = $d->format('Y-m-d');
        $booked_count = $booked_days[$dateStr] ?? 0;

        if ($dayOfWeek >= 6) {
            // Šeštadienis ar sekmadienis
            $class = 'unavailable';
            $label = 'Nedirbama';
        } elseif ($booked_count >= $total_slots) {
            // Visi laikai užimti
            $class = 'unavailable';
            $label = 'Užimta';
        } else {
            // Laisva diena
            $class = 'available';
            $label = 'Pasirinkti';
        }
      ?>
      <div class="day <?= $class ?>">
        <?php if ($class === 'available'): ?>
          <a href="select_time.php?doctor_id=<?= $doctor_id ?>&date=<?= $dateStr ?>">
            <?= $dateStr ?><br><small><?= $label ?></small>
          </a>
        <?php else: ?>
          <?= $dateStr ?><br><small><?= $label ?></small>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>

  <a href="javascript:history.back()" class="back-btn">Grįžti atgal</a>

</body>
</html>
