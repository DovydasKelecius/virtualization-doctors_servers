<?php
session_start();
if (!isset($_SESSION['doctor_id'])) {
    header("Location: doctorlogin.php");
    exit;
}

require '../db.php';

$doctor_id = $_SESSION['doctor_id'];
$patient_id = $_GET['patient_id'] ?? null;
$appointment_id = $_GET['appointment_id'] ?? null;

if (!$patient_id) {
    die("Trūksta paciento ID.");
}

// Fetch patient's name for display
$pname_stmt = $pdo->prepare("SELECT first_name, last_name FROM patients WHERE id = ?");
$pname_stmt->execute([$patient_id]);
$pname = $pname_stmt->fetch(PDO::FETCH_ASSOC);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event = trim($_POST['event'] ?? '');
    $diagnosis = trim($_POST['diagnosis'] ?? '');

    if ($event && $diagnosis) {
        $stmt = $pdo->prepare("
            INSERT INTO medical_records (patient_id, doctor_id, appointment_id, event, diagnosis)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$patient_id, $doctor_id, $appointment_id, $event, $diagnosis]);

        // Use a session message for success, consistent with patient_card.php style
        $_SESSION["message"] = "Apsilankymo duomenys sėkmingai įrašyti!";
        
        header("Location: doctor_patient_details.php?patient_id=$patient_id&appointment_id=$appointment_id");
        exit;
    } else {
        $error = "Užpildykite visus laukus!";
    }
}
?>
<!DOCTYPE html>
<html lang="lt">
<head>
<meta charset="UTF-8">
<title>Įvesti apsilankymo duomenis</title>
<link rel="stylesheet" href="/static/styles.css">
</head>
<body>

<h1 onclick="window.location.href='doctor_home.php'">HOSPITAL</h1>
<h2>Įvesti apsilankymo duomenis (<?= htmlspecialchars($pname['first_name'] . ' ' . $pname['last_name']) ?>)</h2>

<div class="card">
  <?php if (!empty($error)): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <form method="POST">
    <label for="event">Apsilankymo eiga/Įvykis:</label>
    <textarea id="event" name="event" required></textarea>
    
    <label for="diagnosis">Diagnozė/Išrašas:</label>
    <textarea id="diagnosis" name="diagnosis" required></textarea>
    
    <button type="submit" class="btn">Išsaugoti įrašą</button>
  </form>
</div>

<a href="doctor_patient_details.php?patient_id=<?= $patient_id ?>&appointment_id=<?= $appointment_id ?>" class="back">Grįžti į paciento duomenis</a>
</body>
</html>