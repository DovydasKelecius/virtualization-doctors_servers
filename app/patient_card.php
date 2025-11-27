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

    // Get patient data
    $stmt = $pdo->prepare("SELECT * FROM patients WHERE id = ?");
    $stmt->execute([$_SESSION["patient_id"]]);
    $p = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$p) {
        header("Location: logout.php");
        exit();
    }
    
    // Get medical history
    $history_stmt = $pdo->prepare("
        SELECT 
            mr.event, 
            mr.diagnosis, 
            mr.created_at,
            d.first_name AS doctor_first_name,
            d.last_name AS doctor_last_name
        FROM medical_records mr
        LEFT JOIN doctors d ON mr.doctor_id = d.id
        WHERE mr.patient_id = ?
        ORDER BY mr.created_at DESC
    ");
    $history_stmt->execute([$_SESSION["patient_id"]]);
    $history = $history_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database query error: " . $e->getMessage());
}
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
            echo '<p style="color: red;">' . htmlspecialchars($_SESSION['error']) . '</p>';
            unset($_SESSION['error']);
        }
        if (isset($_SESSION['message'])) {
            echo '<p style="color: green;">' . htmlspecialchars($_SESSION['message']) . '</p>';
            unset($_SESSION['message']);
        }
        ?>

        <form action="update_patient.php" method="POST">
            <h3>Paciento Duomenys</h3>

            <label>Vardas:</label>
            <input type="text" name="first_name" value="<?= htmlspecialchars($p["first_name"]) ?>" required>
          
            <label>Pavardė:</label>
            <input type="text" name="last_name" value="<?= htmlspecialchars($p["last_name"]) ?>" required>

            <label>Asmens kodas:</label>
            <input type="text" value="<?= htmlspecialchars($p["personal_code"]) ?>" disabled>
            
            <label>Lytis:</label>
            <select name="gender" required>
                <option value="">-- Pasirinkite lytį --</option>
                <option value="Vyras" <?= ($p["gender"] === 'Vyras' ? 'selected' : '') ?>>Vyras</option>
                <option value="Moteris" <?= ($p["gender"] === 'Moteris' ? 'selected' : '') ?>>Moteris</option>
                <option value="Kita" <?= ($p["gender"] === 'Kita' ? 'selected' : '') ?>>Kita</option>
                <option value="Nenoriu sakyti" <?= ($p["gender"] === 'Nenoriu sakyti' ? 'selected' : '') ?>>Nenoriu sakyti</option>
            </select>
          
            <label>El. paštas:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($p["email"]) ?>" required>
          
            <label>Telefono numeris:</label>
            <input type="text" name="phone" value="<?= htmlspecialchars($p["phone"]) ?>" required>

            <button type="submit">Atnaujinti</button>
        </form>

        <hr style="margin: 20px 0;">

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
                            <td><?= htmlspecialchars($h["doctor_first_name"] . " " . $h["doctor_last_name"]) ?></td>
                            <td><?= htmlspecialchars($h["event"]) ?></td>
                            <td><?= htmlspecialchars($h["diagnosis"]) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <a href="patient_home.php" class="btn" style="margin-top: 20px;">Grįžti atgal</a>
    </div>
</body>
</html>