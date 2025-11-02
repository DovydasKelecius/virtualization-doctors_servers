<?php
session_start();
require "db.php";

if (!isset($_SESSION["patient_id"])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET["doctor_id"]) || !isset($_GET["date"])) {
    // Better to redirect back than die if data is missing
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

// Generate 30-minute slots
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
    $specialization = $doctor["specialization"];
    $datetime = $date . " " . $time;

    // Double-check availability (prevents race conditions)
    $checkStmt = $pdo->prepare("
        SELECT COUNT(*) FROM appointments
        WHERE doctor_id = ? AND appointment_date = ?
    ");
    $checkStmt->execute([$doctor_id, $datetime]);
    if ($checkStmt->fetchColumn() > 0) {
        // Use session error message for cleaner display
        $_SESSION["error"] = "❌ Šis laikas jau užimtas. Bandykite kitą laiką.";
        // Redirect back to this page to show error
        header("Location: book_time.php?doctor_id={$doctor_id}&date={$date}");
        exit();
    }

    // Insert appointment
    $stmt = $pdo->prepare("
        INSERT INTO appointments (patient_id, doctor_id, specialization, appointment_date, comment)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $patient_id,
        $doctor_id,
        $specialization,
        $datetime,
        $comment,
    ]);

    $_SESSION["appointment_success"] = [
        "doctor" => $doctor["first_name"] . " " . $doctor["last_name"],
        "specialization" => $doctor["specialization"],
        "datetime" => $datetime,
        "comment" => $comment,
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
<style>
    body { font-family: Arial; background-color: #f8f9fa; text-align: center; margin-top: 40px; }
    .top { font-size: 24px; font-weight: bold; cursor: pointer; margin-bottom: 20px; }

    /* Standardized centered blocks */
    .doctor-info, form {
        background: #fff;
        display: inline-block;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        margin-bottom: 30px;
        /* Enforce consistent width and centering */
        max-width: 300px;
        width: 90%;
        box-sizing: border-box;
    }

    /* Input and button consistency */
    select, textarea, button[type="submit"] {
        display: block;
        width: 100%; /* Fill the 300px width of the form */
        padding: 10px;
        margin-top: 10px;
        border-radius: 5px;
        border: 1px solid #ccc;
        box-sizing: border-box;
    }

    textarea { height: 100px; resize: none; }

    /* Confirmation button style */
    button[type="submit"] {
        background-color: #28a745;
        color: white;
        cursor: pointer;
        margin-top: 15px;
        border: none; /* Override border for submit button */
    }
    button[type="submit"]:hover { background-color: #218838; }

    /* Centered Back Button (Moved outside form) */
    .back {
        display: block;
        width: 300px; /* Match max-width of form/info */
        margin: 15px auto 0 auto; /* Center it horizontally */

        background: #6c757d;
        color: white;
        padding: 10px 20px;
        border-radius: 5px;
        text-decoration: none;
        box-sizing: border-box;
    }
    .back:hover { background: #5a6268; }
</style>
</head>
<body>

<div class="top" onclick="window.location.href='patient_home.php'">HOSPITAL</div>

<?php if (isset($_SESSION["error"])): ?>
    <div style="color: red; margin: 0 auto 20px auto; max-width: 300px; padding: 10px; background: #fff0f0; border: 1px solid red; border-radius: 5px;">
        <?= htmlspecialchars($_SESSION["error"]) ?>
        <?php unset($_SESSION["error"]); ?>
    </div>
<?php endif; ?>

<div class="doctor-info">
    <h2>Dr. <?= htmlspecialchars(
        $doctor["first_name"] . " " . $doctor["last_name"],
    ) ?></h2>
    <p><strong>Specializacija:</strong> <?= htmlspecialchars(
        $doctor["specialization"],
    ) ?></p>
    <p><strong>Pasirinkta data:</strong> <?= htmlspecialchars($date) ?></p>
</div>

<h3>Pasirinkite laiką</h3>

<form method="POST">
    <label for="time">Laikas:</label>
    <select name="time" id="time" required>
        <option value="">-- Pasirinkite laiką --</option>
        <?php foreach ($time_slots as $t): ?>
            <option value="<?= $t ?>"><?= $t ?></option>
        <?php endforeach; ?>
    </select>

    <label for="comment">Komentarai (pasirinktinai):</label>
    <textarea name="comment" id="comment" placeholder="Trumpai aprašykite savo problemą..."></textarea>

    <button type="submit">Patvirtinti vizitą</button>
</form>

<a href="javascript:history.back()" class="back">Grįžti atgal</a>

</body>
</html>
