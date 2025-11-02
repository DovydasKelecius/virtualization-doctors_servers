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

    $stmt = $pdo->prepare("SELECT * FROM patients WHERE id = ?");
    $stmt->execute([$_SESSION["patient_id"]]);
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$patient) {
        // Handle case where patient data is missing
        header("Location: logout.php");
        exit();
    }
} catch (PDOException $e) {
    die("Database connection error.");
}
?>
<!DOCTYPE html>
<html lang="lt">
<head>
  <meta charset="UTF-8">
  <title>Pacientas - Pagrindinis</title>
  <style>
    /* 🎨 Global Styling and Centering */
    body {
        font-family: Arial, sans-serif;
        background: #f8f9fa;
        text-align: center;
        padding-top: 40px;
    }
    h1 {
        margin-bottom: 5px;
        color: #343a40;
    }
    .info {
        margin-bottom: 30px;
        font-weight: bold;
        font-size: 1.1em;
        color: #007bff; /* Highlight patient name */
    }

    /* 🧱 Button Container for Alignment */
    .button-group {
        display: inline-block; /* Makes the group center via text-align: center on body */
        padding: 20px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);

        /* Set a standard width for the entire button block */
        width: 280px;
        margin: 0 auto;
    }

    /* 🔘 Individual Button Styling */
    .btn {
        display: block; /* Important: Forces buttons to stack vertically */
        width: 100%; /* Fills the container width */
        padding: 12px 0;
        text-align: center;
        text-decoration: none;
        border-radius: 5px;
        margin: 10px 0; /* Vertical margin for spacing between buttons */
        font-weight: bold;
        transition: background-color 0.2s;
    }

    /* Primary Actions */
    .btn-primary {
        background: #007bff;
        color: #fff;
    }
    .btn-primary:hover {
        background: #0056b3;
    }

    /* Secondary Action (My Appointments) */
    .btn-secondary {
        background: #6c757d;
        color: #fff;
    }
    .btn-secondary:hover {
        background: #5a6268;
    }

    /* Logout Button (Danger/Red) */
    .btn-danger {
        background: #dc3545;
        color: #fff;
        margin-top: 20px; /* Extra space before logout */
    }
    .btn-danger:hover {
        background: #c82333;
    }
  </style>
</head>
<body>
  <h1>HOSPITAL</h1>
  <div class="info">Sveiki, <?= htmlspecialchars(
      $patient["first_name"],
  ) ?></div>

  <div class="button-group">
    <a href="patient_card.php" class="btn btn-primary">Paciento kortelė</a>
    <a href="doctor_registration.php" class="btn btn-primary">Registracija pas daktarą</a>
    <a href="my_appointments.php" class="btn btn-secondary">Mano vizitai</a>
    <a href="logout.php" class="btn btn-danger">Atsijungti</a>
  </div>
</body>
</html>
