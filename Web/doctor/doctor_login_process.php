<?php
session_start();
require '../db.php';

$docloginid = $_POST['docloginid'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($docloginid) || empty($password)) {
    $_SESSION['error'] = "Įveskite prisijungimo duomenis";
    header("Location: doctorlogin.php");
    exit;
}

$stmt = $pdo->prepare("SELECT id, first_name, last_name, password FROM doctors WHERE docloginid = ?");
$stmt->execute([$docloginid]);
$doctor = $stmt->fetch();

if ($doctor && password_verify($password, $doctor['password'])) {
    $_SESSION['doctor_id'] = $doctor['id'];
    $_SESSION['doctor_name'] = $doctor['first_name'] . ' ' . $doctor['last_name'];
    header("Location: doctor_home.php");
    exit;
}
else {
    $_SESSION['error'] = "Neteisingas darbuotojo ID arba slaptažodis";
    header("Location: doctorlogin.php");
    exit;
}