<?php
$host = getenv('DB_HOST') ?: 'host.docker.internal';
$port = getenv('DB_PORT') ?: '5433';
$dbname = getenv('DB_NAME') ?: 'hospital';
$user = getenv('DB_USER') ?: 'postgres';
$password = getenv('DB_PASSWORD') ?: '159511';

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Connection failed: " . $e->getMessage());
}
