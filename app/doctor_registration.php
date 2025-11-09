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

// Load distinct specializations from the DB (no duplicates)
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
    <link rel="stylesheet" href="/static/styles.css">
</head>
<body>
    <div class="top" onclick="window.location.href='patient_home.php'">HOSPITAL</div>

    <h2>Pacientas: <?= htmlspecialchars(
        $patient["first_name"],
    ) ?> <?= htmlspecialchars($patient["last_name"]) ?></h2>

    <div class="top-right-search">
        <form action="doctor_list.php" method="GET" class="search-input-group">
            <input type="text" name="q" placeholder="Ieškoti gydytojo arba specializacijos" />
            <button type="submit" class="btn">Ieškoti</button>
        </form>
    </div>

    <div class="container">
        <h3>Pasirinkite gydytojo specializaciją:</h3>
        <?php foreach ($specializations as $spec): ?>
            <a class="specialization" href="doctor_list.php?specialization=<?= urlencode(
                $spec,
            ) ?>"><?= htmlspecialchars($spec) ?></a>
        <?php endforeach; ?>
    <a href="patient_home.php" class="back">Grįžti atgal</a>
    </div>

</body>
</html>
