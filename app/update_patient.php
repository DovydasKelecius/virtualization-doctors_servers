<?php
session_start();
if (!isset($_SESSION['patient_id'])) {
    header("Location: login.php");
    exit;
}

$host = getenv('DB_HOST') ?: 'host.docker.internal';
$port = getenv('DB_PORT') ?: '5433';
$dbname = getenv('DB_NAME') ?: 'hospital';
$user = getenv('DB_USER') ?: 'postgres';
$password = getenv('DB_PASSWORD') ?: '159511';

$pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);

$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';

$stmt = $pdo->prepare("UPDATE patients SET email = ?, phone = ? WHERE id = ?");
$stmt->execute([$email, $phone, $_SESSION['patient_id']]);

header("Location: patient_card.php");
exit;
?>
