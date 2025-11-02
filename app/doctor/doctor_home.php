<?php
session_start();
if (!isset($_SESSION['doctor_id'])) {
    header("Location: doctorlogin.php");
    exit;
}

require '../db.php';

// Fetch doctor's details
$stmt = $pdo->prepare("SELECT * FROM doctors WHERE id = ?");
$stmt->execute([$_SESSION['doctor_id']]);
$doctor = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="lt">
<head>
    <meta charset="UTF-8">
    <title>Gydytojo darbo aplinka</title>
    <style>
        /* Base Styling from patient_card.php */
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 0;
            padding-top: 40px;
            background: #f8f9fa;
        }
        h1 {
            cursor: pointer;
            margin-bottom: 20px;
        }
        
        /* Main Content Container */
        .content-container {
            width: 90%;
            max-width: 800px;
            margin: 0 auto;
            text-align: left;
        }

        /* Header Bar (similar to patient_card's general structure) */
        .header {
            background: white;
            padding: 20px 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .welcome {
            font-size: 24px;
            color: #333;
            font-weight: bold;
        }
        
        /* Card for Details */
        .details {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            margin-bottom: 25px;
        }
        .details h2 {
            color: #343a40;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 10px;
            margin-bottom: 20px;
            margin-top: 0;
        }
        
        /* Info Rows */
        .info-row {
            margin: 15px 0;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 4px;
            border-left: 5px solid #007bff; /* Highlight bar */
        }
        .label {
            font-weight: bold;
            color: #343a40;
            display: inline-block;
            width: 150px; /* Fixed width for alignment */
        }
        
        /* Button Styling - Logout */
        .btn-logout {
            display: inline-block;
            padding: 10px 20px;
            background: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.2s;
        }
        .btn-logout:hover {
            background: #c82333;
        }
        
        /* Button Styling - Main Action (My Patients) */
        .btn-main-action {
            display: block; /* Make it full width of container */
            width: 100%;
            padding: 12px 20px;
            background: #007bff; /* Blue button style */
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            margin-top: 10px;
            transition: background-color 0.2s;
        }
        .btn-main-action:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <h1 onclick="window.location.href='doctor_home.php'">HOSPITAL</h1>
    
    <div class="content-container">
        <div class="header">
            <div class="welcome">
                Sveiki, <?= htmlspecialchars($doctor['first_name'] . ' ' . $doctor['last_name']) ?>
            </div>
            <a href="doctorlogout.php" class="btn-logout">Atsijungti</a>
        </div>

        <div class="details">
            <h2>Jūsų informacija:</h2>
            <div class="info-row">
                <span class="label">Specializacija:</span>
                <?= htmlspecialchars($doctor['specialization']) ?>
            </div>
            <div class="info-row">
                <span class="label">Darbo laikas:</span>
                <?= substr($doctor['work_start'], 0, 5) ?> - <?= substr($doctor['work_end'], 0, 5) ?>
            </div>
            <div class="info-row">
                <span class="label">Darbuotojo ID:</span>
                <?= htmlspecialchars($doctor['docloginid']) ?>
            </div>
        </div>

        <a href="doctor_patients.php" class="btn-main-action">Mano pacientai</a>
    </div>
</body>
</html>