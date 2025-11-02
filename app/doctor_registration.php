<?php
session_start();
if (!isset($_SESSION['patient_id'])) {
    header("Location: login.php");
    exit;
}

require 'db.php';

$id = $_SESSION['patient_id'];
$stmt = $pdo->prepare("SELECT first_name, last_name FROM patients WHERE id = ?");
$stmt->execute([$id]);
$patient = $stmt->fetch();

// Load distinct specializations from the DB (no duplicates)
$specStmt = $pdo->query("SELECT DISTINCT specialization FROM doctors ORDER BY specialization");
$specializations = $specStmt->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="lt">
<head>
    <meta charset="UTF-8">
    <title>Registracija pas daktarą</title>
    <style>
        body { font-family: Arial; text-align: center; background-color: #f5f5f5; margin-top: 40px; }
        .container { display: inline-block; background: white; padding: 25px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        a.specialization {
            display: block;
            width: 300px;
            padding: 10px;
            margin: 8px auto;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.2s;
        }
        a.specialization:hover { background-color: #218838; }
        .top { font-size: 24px; font-weight: bold; margin-bottom: 20px; cursor:pointer; }
    </style>
</head>
<body>
    <div class="top" onclick="window.location.href='patient_home.php'">HOSPITAL</div>

    <h2>Pacientas: <?= htmlspecialchars($patient['first_name']) ?> <?= htmlspecialchars($patient['last_name']) ?></h2>

    <!-- Search box (top-right) -->
    <div style="position: absolute; top: 12px; right: 12px;">
        <form action="doctor_list.php" method="GET" style="display:flex; align-items:center;">
            <input type="text" name="q" placeholder="Ieškoti gydytojo arba specializacijos" style="padding:8px 10px; width:260px; border:1px solid #ccc; border-radius:4px;" />
            <button type="submit" style="margin-left:8px; padding:8px 12px; border-radius:4px; border:none; background:#007bff; color:#fff; cursor:pointer;">Ieškoti</button>
        </form>
    </div>

    <div class="container">
        <h3>Pasirinkite gydytojo specializaciją:</h3>
        <?php foreach ($specializations as $spec): ?>
            <a class="specialization" href="doctor_list.php?specialization=<?= urlencode($spec) ?>"><?= htmlspecialchars($spec) ?></a>
        <?php endforeach; ?>
    </div>
</body>
</html>
