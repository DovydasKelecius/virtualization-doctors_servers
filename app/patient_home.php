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
    // For simplicity in a school project, we can show a generic error.
    // In a real-world scenario, you would log this error.
    die("Klaida jungiantis prie duomenų bazės.");
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
        <p>Sveiki, <?= htmlspecialchars($patient["first_name"]) ?>!</p>

        <a href="patient_card.php" class="btn">Paciento kortelė</a>
        <a href="doctor_registration.php" class="btn">Registracija pas daktarą</a>
        <a href="my_appointments.php" class="btn">Mano vizitai</a>
        <a href="logout.php" class="btn btn-danger">Atsijungti</a>
    </div>
</body>
</html>