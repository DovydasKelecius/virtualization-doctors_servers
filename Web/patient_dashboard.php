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
} catch (PDOException $e) {
    die("❌ Connection failed: " . $e->getMessage());
}

$stmt = $pdo->prepare("SELECT * FROM patients WHERE id = ?");
$stmt->execute([$_SESSION["patient_id"]]);
$patient = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="lt">
<head>
<meta charset="UTF-8">
<title>Paciento kortelė</title>
<link rel="stylesheet" href="/static/styles.css">
</head>
<body>
  <h1>HOSPITAL</h1>
  <h3>Pacientas: <?= htmlspecialchars(
      $patient["first_name"],
  ) ?> <?= htmlspecialchars($patient["last_name"]) ?></h3>

  <div class="container">
    <form action="update_contact.php" method="POST">
      <label>Asmens kodas:</label>
      <input type="text" value="<?= htmlspecialchars(
          $patient["personal_code"],
      ) ?>" disabled>

      <label>Lytis:</label>
      <input type="text" value="<?= htmlspecialchars(
          $patient["gender"],
      ) ?>" disabled>

      <label>El. paštas:</label>
      <input type="email" name="email" value="<?= htmlspecialchars(
          $patient["email"],
      ) ?>" required>

      <label>Telefono numeris:</label>
      <input type="text" name="phone" value="<?= htmlspecialchars(
          $patient["phone"],
      ) ?>" required>

      <label>Medicinos istorija:</label>
      <textarea disabled><?= htmlspecialchars(
          $patient["medical_history"] ?: "Nėra įrašų",
      ) ?></textarea>

      <button type="submit">Atnaujinti kontaktinę informaciją</button>
    </form>

    <form action="logout.php" method="POST">
      <button class="logout" type="submit">Atsijungti</button>
    </form>
    <button onclick="window.location.href='doctor_registration.php'">Registracija pas daktarą</button>

  </div>
</body>
</html>
