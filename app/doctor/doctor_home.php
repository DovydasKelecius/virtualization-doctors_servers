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
    <link rel="stylesheet" href="/static/styles.css">
</head>
<body>
    <h1 onclick="window.location.href='doctor_home.php'">HOSPITAL</h1>
    
    <div class="content-container">
        <div class="header">
            <div class="welcome">
                Sveiki, <?= htmlspecialchars($doctor['first_name'] . ' ' . $doctor['last_name']) ?>
            </div>
            <a href="doctorlogout.php" class="btn-logout">Atsijungti</a>
        </div>

        <div class="details">
            <h2>Jūsų informacija:</h2>
            <div class="info-row">
                <span class="label">Specializacija:</span>
                <?= htmlspecialchars($doctor['specialization']) ?>
            </div>
            <div class="info-row">
                <span class="label">Darbo laikas:</span>
                <?= substr($doctor['work_start'], 0, 5) ?> - <?= substr($doctor['work_end'], 0, 5) ?>
            </div>
            <div class="info-row">
                <span class="label">Darbuotojo ID:</span>
                <?= htmlspecialchars($doctor['docloginid']) ?>
            </div>
        </div>

        <a href="doctor_patients.php" class="btn-main-action">Mano pacientai</a>
    </div>
</body>
</html>