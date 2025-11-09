<?php
session_start();

if (!isset($_SESSION['appointment_success'])) {
    header("Location: patient_home.php");
    exit;
}

$data = $_SESSION['appointment_success'];
unset($_SESSION['appointment_success']);
?>
<!DOCTYPE html>
<html lang="lt">
<head>
<meta charset="UTF-8">
<title>Registracija sėkminga</title>
<link rel="stylesheet" href="/static/styles.css">
</head>
<body>

<div class="box">
  <h2>Sėkmingai užsiregistravote!</h2>
  <p><strong>Gydytojas:</strong> <?= htmlspecialchars($data['doctor']) ?></p>
  <p><strong>Specializacija:</strong> <?= htmlspecialchars($data['specialization']) ?></p>
  <p><strong>Data ir laikas:</strong> <?= htmlspecialchars($data['datetime']) ?></p>

  <?php if (!empty($data['comment'])): ?>
    <p><strong>Komentaras:</strong> <?= htmlspecialchars($data['comment']) ?></p>
  <?php endif; ?>

  <a href="patient_home.php" class="btn">Grįžti į pradinį puslapį</a>
</div>

</body>
</html>
