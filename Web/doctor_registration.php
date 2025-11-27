<?php
session_start();
if (!isset($_SESSION["patient_id"])) {
    header("Location: login.php");
    exit();
}

require "db.php";

$id = $_SESSION["patient_id"];
$stmt = $pdo->prepare(
    "SELECT first_name, last_name FROM patients WHERE id = ?",
);
$stmt->execute([$id]);
$patient = $stmt->fetch();

// Load distinct specializations
$specStmt = $pdo->query(
    "SELECT DISTINCT specialization FROM doctors ORDER BY specialization",
);
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
        <p>Pacientas: <?= htmlspecialchars($patient["first_name"]) ?> <?= htmlspecialchars($patient["last_name"]) ?></p>
        
        <hr style="margin: 20px 0;">

        <form action="doctor_list.php" method="GET">
            <label for="q"><strong>Ieškoti gydytojo arba specializacijos:</strong></label>
            <input type="text" id="q" name="q" placeholder="Įveskite vardą, pavardę arba specializaciją..." />
            <button type="submit">Ieškoti</button>
        </form>
        
        <hr style="margin: 20px 0;">

        <h3>Arba pasirinkite specializaciją iš sąrašo:</h3>
        <?php foreach ($specializations as $spec): ?>
            <a class="btn" href="doctor_list.php?specialization=<?= urlencode($spec) ?>"><?= htmlspecialchars($spec) ?></a>
        <?php endforeach; ?>
        
        <hr style="margin: 20px 0;">

        <a href="patient_home.php" class="btn">Grįžti atgal</a>
    </div>
</body>
</html>