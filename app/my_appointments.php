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
// Fetch the appointment ID (a.id) for cancellation purposes
$appointments = $pdo->prepare(
    "SELECT
        a.id,
        d.specialization,
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
        a.appointment_date ASC",
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
    /* 🎨 Global Styling and Centering */
    body {
        font-family: Arial, sans-serif;
        background-color: #f8f9fa;
        text-align: center;
        padding-top: 40px;
    }
    h1, h2, h3 { color: #343a40; }

    /* Table Styling */
    .table-container {
        /* This wrapper ensures the table box-shadow is centered */
        display: inline-block;
        max-width: 95%;
        margin: 20px auto;
        border-radius: 10px;
        overflow: hidden; /* Keeps the shadow clean */
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    table {
        margin: 0; /* Remove auto margin as the container centers it */
        border-collapse: collapse;
        width: 100%;
        min-width: 700px;
        background: #fff;
    }
    th, td {
        padding: 12px;
        border: 1px solid #dee2e6;
        text-align: left;
    }
    th {
        background-color: #007bff;
        color: white;
        font-weight: bold;
    }
    tr:nth-child(even) { background-color: #f2f2f2; }

    /* Button Styling */
    .btn-action {
        display: block;
        width: 100%;
        padding: 8px 10px;
        border-radius: 4px;
        text-decoration: none;
        text-align: center;
        font-weight: bold;
        transition: background-color 0.2s;
        box-sizing: border-box;
    }
    .btn-cancel {
        background-color: #dc3545;
        color: white;
    }
    .btn-cancel:hover {
        background-color: #c82333;
    }

    /* Back Link Styling */
    .btn-back {
        display: block;
        width: 300px; /* Consistent width for navigation */
        padding: 12px 20px;
        border-radius: 5px;
        background-color: #6c757d;
        color: white;
        text-decoration: none;
        margin: 30px auto; /* Center it */
        font-weight: bold;
        transition: background-color 0.2s;
    }
    .btn-back:hover {
        background-color: #5a6268;
    }
  </style>
</head>
<body>
  <h1>HOSPITAL</h1>
  <h3>Pacientas: <?= htmlspecialchars(
      $patient["first_name"],
  ) ?> <?= htmlspecialchars($patient["last_name"]) ?></h3>

  <h2>Jūsų registracijos</h2>

  <?php if (empty($list)): ?>
      <p style="font-size: 1.1em;">Jūs dar nesate užsiregistravę vizitui.</p>
  <?php else: ?>
      <div class="table-container">
          <table>
              <tr>
                  <th>Specializacija</th>
                  <th>Gydytojas</th>
                  <th>Data ir Laikas</th>
                  <th>Aprašymas</th>
                  <th>Veiksmas</th> </tr>
              <?php foreach ($list as $a): ?>
              <tr>
                  <td><?= htmlspecialchars($a["specialization"]) ?></td>
                  <td><?= htmlspecialchars(
                      $a["doctor_first_name"] . " " . $a["doctor_last_name"],
                  ) ?></td>
                  <td><?= htmlspecialchars($a["appointment_date"]) ?></td>
                  <td><?= htmlspecialchars($a["comment"]) ?></td>
                  <td>
                      <a href="cancel_appointment.php?id=<?= $a["id"] ?>"
                         class="btn-action btn-cancel"
                         onclick="return confirm('Ar tikrai norite atšaukti šį vizitą?');">
                          Atšaukti
                      </a>
                  </td>
              </tr>
              <?php endforeach; ?>
          </table>
      </div>
  <?php endif; ?>

  <a href="patient_home.php" class="btn-back">Grįžti atgal</a>
</body>
</html>
