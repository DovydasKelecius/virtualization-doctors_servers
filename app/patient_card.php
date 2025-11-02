<?php
session_start();
if (!isset($_SESSION['patient_id'])) {
    header("Location: login.php");
    exit;
}

$host = getenv('DB_HOST') ?: '193.219.91.104';
$port = getenv('DB_PORT') ?: '3545';
$dbname = getenv('DB_NAME') ?: 'hospital';
$user = getenv('DB_USER') ?: 'hospital_owner';
$password = getenv('DB_PASSWORD') ?: 'iLoveUnix';

$pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
$stmt = $pdo->prepare("SELECT * FROM patients WHERE id = ?");
$stmt->execute([$_SESSION['patient_id']]);
$p = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="lt">
<head>
  <meta charset="UTF-8">
  <title>Paciento kortelė</title>
  <style>
    body { font-family: Arial; text-align: center; background: #f9f9f9; padding-top: 40px; }
    form { display: inline-block; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); text-align: left; }
    label { display: block; margin-top: 10px; font-weight: bold; }
    input, textarea { width: 300px; padding: 8px; }
    button { width: 100%; padding: 10px; margin-top: 20px; background-color: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; }
  </style>
</head>
<body>
  <h1>HOSPITAL</h1>
  <?php if (isset($_SESSION['error'])): ?>
    <div style="color: red; margin-bottom: 20px;">
        <?= htmlspecialchars($_SESSION['error']) ?>
        <?php unset($_SESSION['error']); ?>
    </div>
  <?php endif; ?>
  
  <?php if (isset($_SESSION['message'])): ?>
    <div style="color: green; margin-bottom: 20px;">
        <?= htmlspecialchars($_SESSION['message']) ?>
        <?php unset($_SESSION['message']); ?>
    </div>
  <?php endif; ?>
  
  <form action="update_patient.php" method="POST">
    <label>Vardas:</label>
    <input type="text" value="<?= htmlspecialchars($p['first_name']) ?>" disabled>

    <label>Pavardė:</label>
    <input type="text" value="<?= htmlspecialchars($p['last_name']) ?>" disabled>

    <label>Asmens kodas:</label>
    <input type="text" value="<?= htmlspecialchars($p['personal_code']) ?>" disabled>

    <label>Lytis:</label>
    <input type="text" value="<?= htmlspecialchars($p['gender']) ?>" disabled>

    <label>El. paštas:</label>
    <input type="email" name="email" value="<?= htmlspecialchars($p['email']) ?>" required>

    <label>Telefono numeris:</label>
    <input type="text" name="phone" value="<?= htmlspecialchars($p['phone']) ?>" required>

    <label>Medicinos istorija:</label>
    <textarea disabled><?= htmlspecialchars($p['medical_history'] ?: 'Nėra įrašų') ?></textarea>

    <button type="submit">Atnaujinti</button>
  </form>
  <br>
  <a href="patient_home.php">Grįžti atgal</a>
</body>
</html>
