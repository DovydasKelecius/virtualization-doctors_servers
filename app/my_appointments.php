<?php
session_start();
if (!isset($_SESSION["patient_id"])) {
    header("Location: login.php");
    exit();
}

require "db.php";

$patient_id = $_SESSION["patient_id"];

// Get patient info
$stmt = $pdo->prepare(
    "SELECT first_name, last_name FROM patients WHERE id = ?",
);
$stmt->execute([$patient_id]);
$patient = $stmt->fetch(PDO::FETCH_ASSOC);

// Get appointments
// Nauja užklausa, naudojanti LEFT JOIN
$appointments = $pdo->prepare(
    "SELECT
        a.specialization,
        a.appointment_date,
        a.comment,
        d.first_name AS doctor_first_name,
        d.last_name AS doctor_last_name
    FROM
        appointments a
    LEFT JOIN
        doctors d ON a.doctor_id = d.id
    WHERE
        a.patient_id = ?
    ORDER BY
        a.appointment_date DESC",
);
$appointments->execute([$patient_id]);
$list = $appointments->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="lt">
<head>
  <meta charset="UTF-8">
  <title>Mano vizitai</title>
  <style>
    body { font-family: Arial; background-color: #f9f9f9; text-align: center; padding-top: 40px; }
    table { margin: 0 auto; border-collapse: collapse; width: 60%; background: #fff; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    th, td { padding: 12px; border: 1px solid #ddd; }
    th { background-color: #007bff; color: white; }
    tr:nth-child(even) { background-color: #f2f2f2; }
    .btn { display: inline-block; margin-top: 20px; padding: 10px 20px; border: none; border-radius: 5px; background-color: #28a745; color: white; text-decoration: none; }
  </style>
</head>
<body>
  <h1>HOSPITAL</h1>
  <h3>Pacientas: <?= htmlspecialchars(
      $patient["first_name"],
  ) ?> <?= htmlspecialchars($patient["last_name"]) ?></h3>

  <h2>Jūsų registracijos</h2>

  <?php if (empty($list)): ?>
      <p>Jūs dar nesate užsiregistravę vizitui.</p>
  <?php else: ?>
      <table>
          <tr>
              <th>Specializacija</th>
              <th>Gydytojas</th>
              <th>Data</th>
              <th>Aprašymas</th>
          </tr>
          <?php foreach ($list as $a): ?>
          <tr>
              <td><?= htmlspecialchars($a["specialization"]) ?></td>
              <td><?= htmlspecialchars(
                  $a["doctor_first_name"] . " " . $a["doctor_last_name"],
              ) ?></td>
              <td><?= htmlspecialchars($a["appointment_date"]) ?></td>
              <td><?= htmlspecialchars($a["comment"]) ?></td>
          </tr>
          <?php endforeach; ?>
      </table>
  <?php endif; ?>

  <a href="patient_home.php" class="btn">Grįžti atgal</a>
</body>
</html>
