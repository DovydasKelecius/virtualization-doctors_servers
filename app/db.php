<?php
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
