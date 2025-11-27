<?php
session_start();
if (!isset($_SESSION['patient_id'])) {
    header("Location: login.php");
    exit;
}

require 'db.php';

$specialization = $_GET['specialization'] ?? '';
$q = trim($_GET['q'] ?? '');

// Base query
$sql = "SELECT * FROM doctors";
$params = [];

// If a search query is provided, search first_name, last_name, and specialization
if ($q !== '') {
    $sql .= " WHERE first_name ILIKE ? OR last_name ILIKE ? OR specialization ILIKE ?";
    $like = "%" . $q . "%";
    $params = [$like, $like, $like];
} elseif (!empty($specialization)) {
    $sql .= " WHERE specialization = ?";
    $params = [$specialization];
}

$sql .= " ORDER BY last_name, first_name";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="lt">
<head>
    <meta charset="UTF-8">
    <title>Gydytojų Sąrašas</title>
    <link rel="stylesheet" href="static/styles.css">
</head>
<body>
    <div class="container">
        <h1 onclick="window.location.href='index.php'">HOSPITAL</h1>
        <h2>Gydytojų sąrašas</h2>

        <?php if (!empty($specialization)): ?>
            <h3>Specializacija: <?= htmlspecialchars($specialization) ?></h3>
        <?php endif; ?>
        <?php if (!empty($q)): ?>
            <h3>Paieškos frazė: "<?= htmlspecialchars($q) ?>"</h3>
        <?php endif; ?>

        <?php if (empty($doctors)): ?>
            <p>Pagal jūsų kriterijus gydytojų nerasta.</p>
        <?php else: ?>
            <table>
                <tr>
                    <th>Vardas, pavardė</th>
                    <th>Specialybė</th>
                    <th>Darbo laikas</th>
                    <th>Veiksmas</th>
                </tr>
                <?php foreach ($doctors as $d): ?>
                <tr>
                    <td><?= htmlspecialchars(($d['first_name'] ?? '') . ' ' . ($d['last_name'] ?? '')) ?></td>
                    <td><?= htmlspecialchars($d['specialization'] ?? '') ?></td>
                    <td><?= htmlspecialchars((isset($d['work_start']) ? substr($d['work_start'],0,5) : '') . ' - ' . (isset($d['work_end']) ? substr($d['work_end'],0,5) : '')) ?></td>
                    <td>
                        <a href="doctor_details.php?doctor_id=<?= htmlspecialchars($d['id']) ?>" class="btn">Registruotis</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>

        <a href="doctor_registration.php" class="btn" style="margin-top: 20px;">Grįžti atgal</a>
    </div>
</body>
</html>