<?php
session_start();

$host = getenv('DB_HOST') ?: '193.219.91.104';
$port = getenv('DB_PORT') ?: '3545';
$dbname = getenv('DB_NAME') ?: 'hospital';
$user = getenv('DB_USER') ?: 'hospital_owner';
$password = getenv('DB_PASSWORD') ?: 'iLoveUnix';

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Connection failed: " . $e->getMessage());
}

$first_name = $_POST['first_name'] ?? '';
$last_name = $_POST['last_name'] ?? '';
$email = $_POST['email'] ?? '';
$personal_code = $_POST['personal_code'] ?? '';
$password = $_POST['password'] ?? '';
$password_repeat = $_POST['password_repeat'] ?? '';
$phone = $_POST['phone'] ?? '';
$gender = $_POST['gender'] ?? '';

if ($password !== $password_repeat) {
    $_SESSION['error'] = "❌ Slaptažodžiai nesutampa!";
    header("Location: register.php");
    exit;
}

if (!preg_match('/^\d{11}$/', $personal_code)) {
    $_SESSION['error'] = "❌ Asmens kodas turi būti 11 skaitmenų!";
    header("Location: register.php");
    exit;
}

if (!preg_match('/^(\+370\d{8}|\d{9})$/', $phone)) {
    $_SESSION['error'] = "❌ Telefono numeris neteisingas!";
    header("Location: register.php");
    exit;
}

$check = $pdo->prepare("SELECT COUNT(*) FROM patients WHERE personal_code = ?");
$check->execute([$personal_code]);
if ($check->fetchColumn() > 0) {
    $_SESSION['error'] = "❌ Asmens kodas jau egzistuoja!";
    header("Location: register.php");
    exit;
}

$stmt = $pdo->prepare("INSERT INTO patients (first_name, last_name, email, personal_code, password, phone, gender)
                       VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->execute([$first_name, $last_name, $email, $personal_code, $password, $phone, $gender]);

header("Location: index.php");
exit;
?>
