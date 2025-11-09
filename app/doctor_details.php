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
<link rel="stylesheet" href="/static/styles.css">
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
            $class = 'unavailable';
            $label = 'Nedirbama';
            $is_available = false;
        } elseif ($booked_count >= $total_slots) {
            $class = 'unavailable';
            $label = 'Užimta';
            $is_available = false;
        } else {
            $class = 'available';
            $label = 'Pasirinkti';
            $is_available = true;
        }
      ?>
      <?php if ($is_available): ?>
        <a href="select_time.php?doctor_id=<?= $doctor_id ?>&date=<?= $dateStr ?>">
          <div class="day <?= $class ?>">
            <?= $dateStr ?><br><small><?= $label ?></small>
          </div>
        </a>
      <?php else: ?>
        <div class="day <?= $class ?>">
          <?= $dateStr ?><br><small><?= $label ?></small>
        </div>
      <?php endif; ?>
    <?php endforeach; ?>
  </div>

  <a href="javascript:history.back()" class="back-btn">Grįžti atgal</a>

</body>
</html>
