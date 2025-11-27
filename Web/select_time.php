<?php
session_start();
require "db.php";

if (!isset($_SESSION["patient_id"])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET["doctor_id"]) || !isset($_GET["date"])) {
    header("Location: patient_home.php");
    exit();
}

$doctor_id = $_GET["doctor_id"];
$date = $_GET["date"];

// Get doctor info
$stmt = $pdo->prepare("SELECT * FROM doctors WHERE id = ?");
$stmt->execute([$doctor_id]);
$doctor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$doctor) {
    die("Gydytojas nerastas.");
}

// Fetch already booked times for that doctor/date
$bookedStmt = $pdo->prepare("
    SELECT TO_CHAR(appointment_date, 'HH24:MI') AS booked_time
    FROM appointments
    WHERE doctor_id = ? AND DATE(appointment_date) = ?
");
$bookedStmt->execute([$doctor_id, $date]);
$booked_times = $bookedStmt->fetchAll(PDO::FETCH_COLUMN);

// Generate available 30-minute slots
$time_slots = [];
$startTime = new DateTime($doctor["work_start"]);
$endTime = new DateTime($doctor["work_end"]);

while ($startTime < $endTime) {
    $slot = $startTime->format("H:i");
    if (!in_array($slot, $booked_times)) {
        $time_slots[] = $slot;
    }
    $startTime->modify("+30 minutes");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $time = $_POST["time"] ?? "";
    $comment = $_POST["comment"] ?? "";
    $patient_id = $_SESSION["patient_id"];
    $datetime = $date . " " . $time;

    // Double-check availability to prevent booking an already taken slot
    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE doctor_id = ? AND appointment_date = ?");
    $checkStmt->execute([$doctor_id, $datetime]);
    if ($checkStmt->fetchColumn() > 0) {
        $_SESSION["error"] = "Atsiprašome, šis laikas jau buvo užimtas. Pasirinkite kitą laiką.";
        // BUG FIX: Redirect back to the correct page
        header("Location: select_time.php?doctor_id={$doctor_id}&date={$date}");
        exit();
    }

    // Insert appointment
    $stmt = $pdo->prepare(
        "INSERT INTO appointments (patient_id, doctor_id, appointment_date, comment) VALUES (?, ?, ?, ?)"
    );
    $stmt->execute([$patient_id, $doctor_id, $datetime, $comment]);

    // Store confirmation details in session to show on the next page
    $_SESSION["appointment_success"] = [
        "doctor" => $doctor["first_name"] . " " . $doctor["last_name"],
        "specialization" => $doctor["specialization"],
        "datetime" => $datetime,
    ];

    header("Location: appointment_confirm.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="lt">
<head>
    <meta charset="UTF-8">
    <title>Pasirinkite laiką</title>
    <link rel="stylesheet" href="static/styles.css">
</head>
<body>
    <div class="container">
        <h1 onclick="window.location.href='index.php'">HOSPITAL</h1>

        <?php 
        if (isset($_SESSION['error'])) {
            echo '<p style="color: red; font-weight: bold;">' . htmlspecialchars($_SESSION['error']) . '</p>';
            unset($_SESSION['error']);
        }
        ?>

        <h2>Pasirinkite laiką vizitui</h2>
        <p>Gydytojas: <strong>Dr. <?= htmlspecialchars($doctor["first_name"] . " " . $doctor["last_name"]) ?></strong></p>
        <p>Data: <strong><?= htmlspecialchars($date) ?></strong></p>
        
        <hr style="margin: 20px 0;">
        
        <?php if (empty($time_slots)): ?>
            <p>Atsiprašome, šiai dienai laisvų laikų nebėra.</p>
        <?php else: ?>
            <form method="POST">
                <h3>Laisvi laikai:</h3>
                <div class="time-grid">
                    <?php foreach ($time_slots as $t): ?>
                        <div>
                            <input type="radio" id="time_<?= $t ?>" name="time" value="<?= $t ?>" required>
                            <label for="time_<?= $t ?>" class="time-btn"><?= $t ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>

                <label for="comment" style="display: block; margin-top: 20px;">Komentarai (pasirinktinai):</label>
                <textarea name="comment" id="comment" placeholder="Trumpai aprašykite vizito priežastį..."></textarea>

                <button type="submit">Patvirtinti vizitą</button>
            </form>
        <?php endif; ?>

        <a href="javascript:history.back()" class="btn" style="margin-top: 20px;">Grįžti atgal</a>
    </div>
</body>
</html>