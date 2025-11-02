<?php
session_start();
if (!isset($_SESSION['patient_id'])) {
    header("Location: login.php");
    exit;
}

$host = getenv('DB_HOST') ?: '193.219.91.104';
$port = getenv('DB_PORT') ?: '3545';
$dbname = getenv('DB_NAME') ?: 'hospital';
$user = getenv('DB_USER') ?: 'hospital_owner';
$password = getenv('DB_PASSWORD') ?: 'iLoveUnix';

$pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
$stmt = $pdo->prepare("SELECT * FROM patients WHERE id = ?");
$stmt->execute([$_SESSION['patient_id']]);
$patient = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="lt">
<head>
  <meta charset="UTF-8">
  <title>Pacientas</title>
  <style>
    body { font-family: Arial; background: #f2f2f2; text-align: center; padding-top: 40px; }
    h1 { margin-bottom: 10px; }
    .info { margin-bottom: 30px; font-weight: bold; }
    .btn {
      display: inline-block;
      padding: 12px 20px;
      background: #007bff;
      color: #fff;
      text-decoration: none;
      border-radius: 5px;
      margin: 5px;
    }
    .logout { background: #dc3545; }
    .btn-secondary { background: #6c757d; }
  </style>
</head>
<body>
  <h1>HOSPITAL</h1>
  <div class="info">Pacientas: <?= htmlspecialchars($patient['first_name'].' '.$patient['last_name']) ?></div>

  <a href="patient_card.php" class="btn">Paciento kortelė</a>
  <a href="doctor_registration.php" class="btn">Registracija pas daktarą</a>
  <a href="my_appointments.php" class="btn btn-secondary">Mano vizitai</a>
  <a href="logout.php" class="btn logout">Atsijungti</a>
</body>
</html>
