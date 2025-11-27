<?php
session_start();
if (!isset($_SESSION["patient_id"])) {
    header("Location: login.php");
    exit();
}

require "db.php";

$patient_id = $_SESSION["patient_id"];
$appointment_id = $_GET["id"] ?? null;

if (!$appointment_id || !is_numeric($appointment_id)) {
    $_SESSION["error"] = "Klaida: Nurodykite vizito ID.";
    header("Location: my_appointments.php");
    exit();
}

try {
    // CRITICAL: Only allow the patient to cancel THEIR OWN appointment
    $stmt = $pdo->prepare("
        DELETE FROM appointments
        WHERE id = ? AND patient_id = ?
    ");
    $stmt->execute([$appointment_id, $patient_id]);

    if ($stmt->rowCount() > 0) {
        $_SESSION["message"] = "Vizitas sėkmingai atšauktas.";
    } else {
        $_SESSION["error"] =
            "Vizitas nerastas arba neturite leidimo jo atšaukti.";
    }

    header("Location: my_appointments.php");
    exit();
} catch (PDOException $e) {
    $_SESSION["error"] = "Klaida atšaukiant vizitą: " . $e->getMessage();
    header("Location: my_appointments.php");
    exit();
}
?>
