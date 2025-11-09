<?php
session_start();
if (!isset($_SESSION["patient_id"])) {
    header("Location: login.php");
    exit();
}

require "db.php"; 

try {
    $pdo = new PDO(
        "pgsql:host=$host;port=$port;dbname=$dbname",
        $user,
        $password,
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT * FROM patients WHERE id = ?");
    $stmt->execute([$_SESSION["patient_id"]]);
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$patient) {
        // Handle case where patient data is missing
        header("Location: logout.php");
        exit();
    }
} catch (PDOException $e) {
    die("Database connection error.");
}
?>
<!DOCTYPE html>
<html lang="lt">
<head>
  <meta charset="UTF-8">
  <title>Pacientas - Pagrindinis</title>
  <link rel="stylesheet" href="/static/styles.css">
</head>
<body>
  <h1>HOSPITAL</h1>
  <div class="info">Sveiki, <?= htmlspecialchars(
      $patient["first_name"],
  ) ?></div>

  <div class="button-group">
    <a href="patient_card.php" class="btn btn-primary">Paciento kortelė</a>
    <a href="doctor_registration.php" class="btn btn-primary">Registracija pas daktarą</a>
    <a href="my_appointments.php" class="btn btn-secondary">Mano vizitai</a>
    <a href="logout.php" class="btn btn-danger">Atsijungti</a>
  </div>
</body>
</html>
