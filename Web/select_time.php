<?php
session_start();
require "db.php";

if (!isset($_SESSION["patient_id"])) {
    header("Location: login.php");
    exit();
}


$doctor_id = $_GET["doctor_id"];
$date = $_GET["date"];

$doctor = $pdo->query("SELECT * FROM doctors WHERE id = $doctor_id")->fetch(PDO::FETCH_ASSOC);

// Paima jau užimtas vietas tam dakarui/datai
$booked_times = $pdo->query("
    SELECT TO_CHAR(appointment_date, 'HH24:MI') AS booked_time
    FROM appointments
    WHERE doctor_id = $doctor_id AND DATE(appointment_date) = '$date'
")->fetchAll(PDO::FETCH_COLUMN);

// Generuoja laisvus 30 min blokus
$time_slots = [];
if ($doctor && $doctor["work_start"] && $doctor["work_end"]) {
    $startTime = new DateTime($doctor["work_start"]);
    $endTime = new DateTime($doctor["work_end"]);

    while ($startTime < $endTime) {
        $slot = $startTime->format("H:i");
        if (!in_array($slot, $booked_times)) {
            $time_slots[] = $slot;
        }
        $startTime->modify("+30 minutes");
    }
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $time = $_POST["time"] ?? "";
    $comment = $_POST["comment"] ?? "";
    $patient_id = $_SESSION["patient_id"];
    $datetime = $date . " " . $time;

    $sql = "INSERT INTO appointments (patient_id, doctor_id, appointment_date, comment) VALUES ($patient_id, $doctor_id, '$datetime', '$comment')";
    $pdo->query($sql);

    $_SESSION["appointment_success"] = [
        "doctor" => ($doctor["first_name"] ?? '') . " " . ($doctor["last_name"] ?? ''),
        "specialization" => $doctor["specialization"] ?? '',
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
            echo '<p style="color: red; font-weight: bold;">' . $_SESSION['error'] . '</p>';
            unset($_SESSION['error']);
        }
        ?>

        <h2>Pasirinkite laiką vizitui</h2>
        <p>Gydytojas: <strong>Dr. <?= $doctor["first_name"] . " " . $doctor["last_name"] ?></strong></p>
        <p>Data: <strong><?= $date ?></strong></p>
        
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

                <label for="comment" style="display: block;">Komentarai (pasirinktinai):</label>
                <textarea name="comment" id="comment" placeholder="Trumpai aprašykite vizito priežastį..."></textarea>

                <button type="submit">Patvirtinti vizitą</button>
            </form>
        <?php endif; ?>

        <a href="javascript:history.back()" class="btn">Grįžti atgal</a>
    </div>
</body>
</html>