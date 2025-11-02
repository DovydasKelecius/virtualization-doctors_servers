<?php
session_start();
if (!isset($_SESSION['patient_id'])) {
    header("Location: login.php");
    exit;
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: patient_card.php");
    exit;
}

$host = getenv('DB_HOST') ?: '193.219.91.104';
$port = getenv('DB_PORT') ?: '3545';
$dbname = getenv('DB_NAME') ?: 'hospital';
$user = getenv('DB_USER') ?: 'hospital_owner';
$password = getenv('DB_PASSWORD') ?: 'iLoveUnix';

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Validate and sanitize input
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $phone = trim($_POST['phone'] ?? '');
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Neteisingas el. pašto formatas");
    }
    
    if (empty($phone)) {
        throw new Exception("Telefono numeris negali būti tuščias");
    }

    // Prepare and execute the update
    $stmt = $pdo->prepare("UPDATE patients SET email = ?, phone = ? WHERE id = ? RETURNING *");
    $result = $stmt->execute([$email, $phone, $_SESSION['patient_id']]);
    
    if ($stmt->rowCount() === 0) {
        throw new Exception("Nepavyko atnaujinti paciento duomenų");
    }

    $_SESSION['message'] = "Duomenys sėkmingai atnaujinti";
    header("Location: patient_card.php");
    exit;

} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    header("Location: patient_card.php");
    exit;
}
?>
