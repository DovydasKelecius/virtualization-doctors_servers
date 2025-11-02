<?php
session_start();
if (!isset($_SESSION["patient_id"])) {
    header("Location: login.php");
    exit();
}

require "db.php";

try {
    // Get patient info
    $stmt = $pdo->prepare("SELECT * FROM patients WHERE id = ?");
    $stmt->execute([$_SESSION["patient_id"]]);
    $p = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$p) {
        header("Location: logout.php");
        exit();
    }
    
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
    die("Database connection error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="lt">
<head>
  <meta charset="UTF-8">
  <title>Paciento kortelė</title>
  <style>
    body {
        font-family: Arial, sans-serif;
        text-align: center;
        background: #f8f9fa;
        padding-top: 40px;
    }
    form {
        display: inline-block;
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        text-align: left;
        max-width: 700px;
        width: 90%;
    }
    label { font-weight: bold; margin-top: 10px; display: block; }
    input, textarea {
        width: 100%; padding: 10px; margin-top: 5px;
        border: 1px solid #ced4da; border-radius: 5px;
    }
    table {
        width: 100%; border-collapse: collapse; margin-top: 15px;
    }
    th, td {
        border: 1px solid #ccc; padding: 8px; text-align: left;
    }
    th { background: #007bff; color: white; }
    tr:nth-child(even){ background:#f2f2f2; }
    .back-link {
        display: block;
        margin: 20px auto;
        background: #6c757d;
        color: white;
        padding: 10px 20px;
        border-radius: 5px;
        text-decoration: none;
        width: 200px;
        text-align: center;
    }
    .back-link:hover { background: #5a6268; }
  </style>
</head>
<body>
  <h1>HOSPITAL</h1>

  <form>
      <h3>Paciento Duomenys</h3>

      <label>Vardas:</label>
      <input type="text" value="<?= htmlspecialchars($p["first_name"]) ?>" disabled>

      <label>Pavardė:</label>
      <input type="text" value="<?= htmlspecialchars($p["last_name"]) ?>" disabled>

      <label>Asmens kodas:</label>
      <input type="text" value="<?= htmlspecialchars($p["personal_code"]) ?>" disabled>

      <label>Lytis:</label>
      <input type="text" value="<?= htmlspecialchars($p["gender"]) ?>" disabled>

      <label>El. paštas:</label>
      <input type="email" value="<?= htmlspecialchars($p["email"]) ?>" disabled>

      <label>Telefono numeris:</label>
      <input type="text" value="<?= htmlspecialchars($p["phone"]) ?>" disabled>

      <!-- ✅ Medical history table -->
      <label>Medicininė istorija:</label>
      <?php if (empty($history)): ?>
          <p><i>Nėra įrašų.</i></p>
      <?php else: ?>
          <table>
              <tr>
                  <th>Data</th>
                  <th>Gydytojas</th>
                  <th>Įvykis</th>
                  <th>Išrašas</th>
              </tr>
              <?php foreach ($history as $h): ?>
                  <tr>
                      <td><?= date("Y-m-d H:i", strtotime($h["created_at"])) ?></td>
                      <td><?= htmlspecialchars($h["doctor_first_name"] . " " . $h["doctor_last_name"]) ?></td>
                      <td><?= htmlspecialchars($h["event"]) ?></td>
                      <td><?= htmlspecialchars($h["diagnosis"]) ?></td>
                  </tr>
              <?php endforeach; ?>
          </table>
      <?php endif; ?>

  </form>

  <a href="patient_home.php" class="back-link">Grįžti atgal</a>
</body>
</html>
