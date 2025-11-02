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
<style>
  /* Base Styling */
  body { 
      font-family: Arial, sans-serif; 
      background:#f8f9fa; 
      text-align:center; 
      padding-top:40px; 
  }
  h1 { 
      cursor:pointer; 
      margin-bottom: 20px;
  }
  h2 {
    color: #343a40;
    margin-bottom: 25px;
  }
  
  /* Card */
  .card { 
      background:white; 
      display:inline-block; 
      padding:30px; /* Šiek tiek mažiau padding */
      border-radius:10px;
      box-shadow:0 4px 12px rgba(0,0,0,0.1); 
      width:90%;
      max-width:500px; 
      text-align:left; 
      box-sizing: border-box;
  }
  
  /* Form Elements */
  label { 
      font-weight:bold; 
      display:block; 
      margin-top:15px; 
      color: #343a40;
  }
  textarea { 
      width:100%; 
      height:100px; /* Padidintas laukas geriau informacijai */
      resize:none; 
      padding:10px; 
      margin-top:5px;
      border:1px solid #ced4da; 
      border-radius:5px; 
      font-family:Arial, sans-serif;
      box-sizing: border-box;
  }
  
  /* Button Styling - Pakeistas dydis */
  .btn { 
      background:#28a745; /* Green button for action */
      color:white; 
      border:none; 
      padding:10px 20px; /* Mažesnis padding */
      border-radius:5px; 
      cursor:pointer; 
      margin-top:20px; 
      width:100%; 
      font-size: 16px;
      font-weight: bold;
      transition: background-color 0.2s;
  }
  .btn:hover { 
      background:#218838; 
  }
  
  /* Error Styling */
  .error { 
      color: #721c24;
      background-color: #f8d7da;
      border: 1px solid #f5c6cb;
      padding: 10px;
      border-radius: 5px;
      text-align: center;
      margin-bottom: 20px;
      font-weight: bold;
  }
  
  /* Back Link Styling (Grey button) */
  .back { 
      display: block; 
      width: 90%;
      max-width: 500px;
      margin: 20px auto 0 auto;
      background: #6c757d;
      color: white;
      padding: 10px 20px;
      border-radius: 5px;
      text-decoration: none;
      box-sizing: border-box;
      transition: background-color 0.2s;
      font-weight: bold;
  }
  .back:hover { 
      background: #5a6268; 
  }
</style>
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