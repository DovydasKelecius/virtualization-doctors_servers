<?php
session_start();
require 'db.php';

if (!isset($_SESSION['patient_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $specialization = $_POST['specialization'];
    $doctor_id = $_POST['doctor_id'] ?? null;
    $patient_id = $_SESSION['patient_id'];

    if (!empty($specialization)) {
        // ✅ INSERT the new appointment, including the date
        $stmt = $pdo->prepare("
            INSERT INTO appointments (patient_id, specialization, appointment_date)
            VALUES (?, ?, NOW())
        ");
        $stmt->execute([$patient_id, $specialization]);

        $_SESSION['success'] = "Jūs sėkmingai užsiregistravote pas $specialization!";
    }
}

header("Location: patient_home.php");
exit;
