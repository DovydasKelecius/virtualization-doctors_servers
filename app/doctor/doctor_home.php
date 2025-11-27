<?php
session_start();
if (!isset($_SESSION['doctor_id'])) {
    header("Location: doctorlogin.php");
    exit;
}

require '../db.php';

// Fetch doctor's details
$stmt = $pdo->prepare("SELECT * FROM doctors WHERE id = ?");
$stmt->execute([$_SESSION['doctor_id']]);
$doctor = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="lt">
<head>
    <meta charset="UTF-8">
    <title>Gydytojo darbo aplinka</title>
    <link rel="stylesheet" href="../static/styles.css">
</head>
<body>
    <div class="container">
        <h1 onclick="window.location.href='../index.php'">HOSPITAL</h1>
        <h2>Gydytojo Darbo Aplinka</h2>

        <p>Sveiki, <strong>Dr. <?= htmlspecialchars($doctor['first_name'] . ' ' . $doctor['last_name']) ?></strong></p>
        
        <hr style="margin: 20px 0;">

        <h3>Jūsų informacija:</h3>
        <p><strong>Specializacija:</strong> <?= htmlspecialchars($doctor['specialization']) ?></p>
        <p><strong>Darbo laikas:</strong> <?= substr($doctor['work_start'], 0, 5) ?> - <?= substr($doctor['work_end'], 0, 5) ?></p>
        <p><strong>Darbuotojo ID:</strong> <?= htmlspecialchars($doctor['docloginid']) ?></p>

        <hr style="margin: 20px 0;">

        <a href="doctor_patients.php" class="btn">Mano pacientai</a>
        <a href="doctorlogout.php" class="btn btn-danger">Atsijungti</a>
    </div>
</body>
</html>