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
        body {
            font-family: Arial;
            margin: 0;
            padding: 20px;
            background: #f7f7f7;
        }
        .header {
            background: white;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .welcome {
            font-size: 24px;
            color: #333;
        }
        .details {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .btn:hover {
            background: #c82333;
        }
        .info-row {
            margin: 10px 0;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 4px;
        }
        .label {
            font-weight: bold;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="welcome">
            Sveiki, <?= htmlspecialchars($doctor['first_name'] . ' ' . $doctor['last_name']) ?>
        </div>
        <a href="doctorlogout.php" class="btn">Atsijungti</a>
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

    <!-- Additional functionality can be added here -->
</body>
</html>