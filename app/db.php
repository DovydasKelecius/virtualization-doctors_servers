<?php
// Function to safely read the content of a Docker Secret file
function get_docker_secret($secret_name)
{
    $file_path = "/run/secrets/" . $secret_name;
    if (file_exists($file_path)) {
        return trim(file_get_contents($file_path));
    }
    return null;
}

$host = getenv("DB_HOST");
$port = getenv("DB_PORT");
$dbname = getenv("DB_NAME");

$user = get_docker_secret("db_user_secret");
$password = get_docker_secret("db_password_secret");

if (!$user || !$password) {
    die("❌ Database secrets (user/password) not found in /run/secrets.");
}

try {
    $pdo = new PDO(
        "pgsql:host=$host;port=$port;dbname=$dbname",
        $user,
        $password,
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Connection failed: " . $e->getMessage());
}
?>
