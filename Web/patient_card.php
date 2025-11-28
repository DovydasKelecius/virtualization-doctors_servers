<?php
session_start();
if (!isset($_SESSION["patient_id"])) {
    header("Location: login.php");
    exit();
}

require "db.php";

$patient_id = $_SESSION["patient_id"];

$p = $pdo->query("SELECT * FROM patients WHERE id = $patient_id")->fetch(PDO::FETCH_ASSOC);

if (!$p) {
    header("Location: logout.php");
    exit();
}


$history = $pdo->query("
    SELECT mr.event, mr.diagnosis, mr.created_at, d.first_name AS doctor_first_name, d.last_name AS doctor_last_name
    FROM medical_records mr
    LEFT JOIN doctors d ON mr.doctor_id = d.id
    WHERE mr.patient_id = $patient_id
    ORDER BY mr.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="lt">
<head>
    <meta charset="UTF-8">
    <title>Paciento kortelė</title>
    <link rel="stylesheet" href="static/styles.css">
</head>
<body>
    <div class="container">
        <h1 onclick="window.location.href='index.php'">HOSPITAL</h1>

        <?php 
        if (isset($_SESSION['error'])) {
            echo '<p style="color: red;">' . $_SESSION['error'] . '</p>';
            unset($_SESSION['error']);
        }
        if (isset($_SESSION['message'])) {
            echo '<p style="color: green;">' . $_SESSION['message'] . '</p>';
            unset($_SESSION['message']);
        }
        ?>

        <form action="update_patient.php" method="POST">
            <h3>Paciento Duomenys</h3>

            <label>Vardas:</label>
            <input type="text" name="first_name" value="<?= $p["first_name"] ?>" required>
          
            <label>Pavardė:</label>
            <input type="text" name="last_name" value="<?= $p["last_name"] ?>" required>

            <label>Asmens kodas:</label>
            <input type="text" value="<?= $p["personal_code"] ?>" disabled>
            
            <label>Lytis:</label>
            <select name="gender" required>
                <option value="">-- Pasirinkite lytį --</option>
                <option value="Vyras" <?= ($p["gender"] === 'Vyras' ? 'selected' : '') ?>>Vyras</option>
                <option value="Moteris" <?= ($p["gender"] === 'Moteris' ? 'selected' : '') ?>>Moteris</option>
                <option value="Kita" <?= ($p["gender"] === 'Kita' ? 'selected' : '') ?>>Kita</option>
                <option value="Nenoriu sakyti" <?= ($p["gender"] === 'Nenoriu sakyti' ? 'selected' : '') ?>>Nenoriu sakyti</option>
            </select>
          
            <label>El. paštas:</label>
            <input type="email" name="email" value="<?= $p["email"] ?>" required>
          
            <label>Telefono numeris:</label>
            <input type="text" name="phone" value="<?= $p["phone"] ?>" required>

            <button type="submit">Atnaujinti</button>
        </form>


        <h2>Medicininė istorija</h2>
        <?php if (empty($history)): ?>
            <p>Nėra įrašų.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Gydytojas</th>
                        <th>Įvykis</th>
                        <th>Išrašas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($history as $h): ?>
                        <tr>
                            <td><?= date("Y-m-d H:i", strtotime($h["created_at"])) ?></td>
                            <td><?= $h["doctor_first_name"] . " " . $h["doctor_last_name"] ?></td>
                            <td><?= $h["event"] ?></td>
                            <td><?= $h["diagnosis"] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <a href="patient_home.php" class="btn">Grįžti atgal</a>
    </div>
</body>
</html>