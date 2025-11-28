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

$sql = "DELETE FROM appointments WHERE id = $appointment_id AND patient_id = $patient_id";
$pdo->query($sql);

header("Location: my_appointments.php");
exit();
?>
