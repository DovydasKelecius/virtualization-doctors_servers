<?php
session_start();
if (!isset($_SESSION['patient_id'])) {
    header("Location: login.php");
    exit;
}

require 'db.php';

$specialization = $_GET['specialization'] ?? '';
$q = trim($_GET['q'] ?? '');

// If a search query is provided, search first_name, last_name, and specialization (case-insensitive)
if ($q !== '') {
    $like = "%" . $q . "%";
    $stmt = $pdo->prepare("SELECT * FROM doctors WHERE first_name ILIKE ? OR last_name ILIKE ? OR specialization ILIKE ? ORDER BY last_name, first_name");
    $stmt->execute([$like, $like, $like]);
} elseif (!empty($specialization)) {
    $stmt = $pdo->prepare("SELECT * FROM doctors WHERE specialization = ? ORDER BY last_name, first_name");
    $stmt->execute([$specialization]);
} else {
    $stmt = $pdo->query("SELECT * FROM doctors ORDER BY last_name, first_name");
}
$doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get patient info for header
$pstmt = $pdo->prepare("SELECT first_name, last_name FROM patients WHERE id = ?");
$pstmt->execute([$_SESSION['patient_id']]);
$patient = $pstmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="lt">
<head>
<meta charset="UTF-8">
<title>Gydytojai – <?= htmlspecialchars($specialization) ?></title>
<style>
  body { font-family: Arial; background: #f9f9f9; text-align: center; padding-top: 40px; }
  table { margin: 20px auto; border-collapse: collapse; background: white; width: 60%; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
  th, td { border: 1px solid #ccc; padding: 10px; }
  th { background-color: #007bff; color: white; }
  tr:nth-child(even) { background: #f2f2f2; }
  .btn { background: #28a745; color: white; border: none; border-radius: 5px; padding: 8px 15px; cursor: pointer; text-decoration: none; display: inline-block; }
  .btn:hover { background: #218838; }
  .back { display: inline-block; margin-top: 20px; background: #6c757d; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; }
  .top { font-size: 24px; font-weight: bold; margin-bottom: 20px; cursor: pointer; }
</style>
</head>
<body>
  <div class="top" onclick="window.location.href='patient_home.php'">HOSPITAL</div>

  <h3>Pacientas: <?= htmlspecialchars($patient['first_name'].' '.$patient['last_name']) ?></h3>
  <h2><?= htmlspecialchars($specialization) ?> – Gydytojai</h2>

  <?php if (empty($doctors)): ?>
      <p>Šiuo metu nėra gydytojų šioje specializacijoje.</p>
  <?php else: ?>
      <table>
    <tr>
        <th>Vardas, pavardė</th>
        <th>Daktaro specialybė</th>
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

  <a href="doctor_registration.php" class="back">Grįžti atgal</a>
</body>
</html>
