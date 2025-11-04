<?php
session_start();

require "db.php"; 

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Connection failed: " . $e->getMessage());
}

$personal_code = $_POST['personal_code'] ?? '';
$pass = $_POST['password'] ?? '';

$stmt = $pdo->prepare("SELECT * FROM patients WHERE personal_code = ?");
$stmt->execute([$personal_code]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && $user['password'] === $pass) {
    session_regenerate_id(true); 
    $_SESSION['patient_id'] = $user['id'];
    header("Location: patient_home.php");
    exit;
} else {
    $_SESSION['error'] = "❌ Neteisingas asmens kodas arba slaptažodis!";
    header("Location: login.php");
    exit;
}
?>
