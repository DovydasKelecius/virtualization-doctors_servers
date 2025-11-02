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
<style>
  body { font-family: Arial; background: #f8f9fa; text-align: center; margin-top: 80px; }
  .box { background: white; display: inline-block; padding: 30px; border-radius: 10px;
         box-shadow: 0 0 10px rgba(0,0,0,0.1); }
  h2 { color: #28a745; }
  .btn { margin-top: 20px; background: #007bff; color: white; padding: 10px 20px;
         border-radius: 6px; text-decoration: none; display: inline-block; }
  .btn:hover { background: #0056b3; }
</style>
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
