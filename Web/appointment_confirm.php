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
    <link rel="stylesheet" href="static/styles.css">
</head>
<body>
    <div class="container">
        <h1 onclick="window.location.href='index.php'">HOSPITAL</h1>
        <h2>Sėkmingai užsiregistravote!</h2>
        <p>Jūsų vizitas patvirtintas. Žemiau pateikiama vizito informacija:</p>
        <p><strong>Gydytojas:</strong> <?= $data['doctor'] ?></p>
        <p><strong>Specializacija:</strong> <?= $data['specialization'] ?></p>
        <p><strong>Data ir laikas:</strong> <?= $data['datetime'] ?></p>

        <a href="patient_home.php" class="btn">Grįžti į paciento puslapį</a>
        <a href="my_appointments.php" class="btn">Peržiūrėti visus vizitus</a>
    </div>
</body>
</html>