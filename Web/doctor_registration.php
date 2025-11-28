<?php
session_start();
if (!isset($_SESSION["patient_id"])) {
    header("Location: login.php");
    exit();
}

require "db.php";

$id = $_SESSION["patient_id"];
$patient = $pdo->query("SELECT first_name, last_name FROM patients WHERE id = $id")->fetch(PDO::FETCH_ASSOC);

$specStmt = $pdo->query("SELECT DISTINCT specialization FROM doctors ORDER BY specialization");
$specializations = $specStmt->fetchAll(PDO::FETCH_COLUMN);
?>


<!DOCTYPE html>
<html lang="lt">
<head>
    <meta charset="UTF-8">
    <title>Registracija pas daktarą</title>
    <link rel="stylesheet" href="static/styles.css">
</head>
<body>
    <div class="container">
        <h1 onclick="window.location.href='index.php'">HOSPITAL</h1>
        <h2>Registracija pas daktarą</h2>
        <p>Pacientas: <?= $patient["first_name"] ?> <?= $patient["last_name"] ?></p>
        

        <form action="doctor_list.php" method="GET">
            <label for="q"><strong>Ieškoti gydytojo arba specializacijos:</strong></label>
            <input type="text" id="q" name="q" placeholder="Įveskite vardą, pavardę arba specializaciją..." />
            <button type="submit">Ieškoti</button>
        </form>
        

        <h3>Arba pasirinkite specializaciją iš sąrašo:</h3>
        <?php foreach ($specializations as $spec): ?>
            <a class="btn" href="doctor_list.php?specialization=<?= urlencode($spec) ?>"><?= $spec ?></a>
        <?php endforeach; ?>
        
        <hr style="margin: 20px 0;">

        <a href="patient_home.php" class="btn">Grįžti atgal</a>
    </div>
</body>
</html>