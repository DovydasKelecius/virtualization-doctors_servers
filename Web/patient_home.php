<?php
session_start();
if (!isset($_SESSION["patient_id"])) {
    header("Location: login.php");
    exit();
}

require "db.php"; 

$patient_id = $_SESSION["patient_id"];
$patient_result = $pdo->query("SELECT * FROM patients WHERE id = $patient_id");
$patient = $patient_result->fetch(PDO::FETCH_ASSOC);

if (!$patient) {
    header("Location: logout.php");
    exit();
}


?>
<!DOCTYPE html>
<html lang="lt">
<head>
    <meta charset="UTF-8">
    <title>Paciento Pagrindinis Puslapis</title>
    <link rel="stylesheet" href="static/styles.css">
</head>
<body>
    <div class="container">
        <h1 onclick="window.location.href='index.php'">HOSPITAL</h1>
        <p>Sveiki, <?= $patient["first_name"] ?>!</p>

        <a href="patient_card.php" class="btn">Paciento kortelė</a>
        <a href="doctor_registration.php" class="btn">Registracija pas daktarą</a>
        <a href="my_appointments.php" class="btn">Mano vizitai</a>
        <a href="logout.php" class="btn btn-danger">Atsijungti</a>
    </div>
</body>
</html>