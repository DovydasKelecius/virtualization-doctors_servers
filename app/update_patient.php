<?php
session_start();
if (!isset($_SESSION["patient_id"])) {
    header("Location: login.php");
    exit();
}

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: patient_card.php");
    exit();
}

require "db.php"; 

try {
    $pdo = new PDO(
        "pgsql:host=$host;port=$port;dbname=$dbname",
        $user,
        $password,
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Validate and sanitize input
    $first_name = trim($_POST["first_name"] ?? "");
    $last_name = trim($_POST["last_name"] ?? "");
    $email = filter_var($_POST["email"] ?? "", FILTER_SANITIZE_EMAIL);
    $phone = trim($_POST["phone"] ?? "");

    // --- Validation for Name and Surname ---
    if (empty($first_name) || !preg_match("/^[\p{L}\s'-]+$/u", $first_name)) {
        throw new Exception("Neteisingas vardo formatas.");
    }
    if (empty($last_name) || !preg_match("/^[\p{L}\s'-]+$/u", $last_name)) {
        throw new Exception("Neteisingas pavardės formatas.");
    }
    // --- End Validation for Name and Surname ---

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Neteisingas el. pašto formatas.");
    }

    if (empty($phone)) {
        throw new Exception("Telefono numeris negali būti tuščias.");
    }

    // Prepare and execute the update
    $stmt = $pdo->prepare("
        UPDATE patients
        SET
            first_name = ?,
            last_name = ?,
            email = ?,
            phone = ?
        WHERE id = ?
        RETURNING *
    ");

    $result = $stmt->execute([
        $first_name,
        $last_name,
        $email,
        $phone,
        $_SESSION["patient_id"],
    ]);

    if ($stmt->rowCount() === 0) {
        throw new Exception(
            "Nepavyko atnaujinti paciento duomenų. Galbūt nebuvo pakeitimų.",
        );
    }

    $_SESSION["message"] = "Duomenys sėkmingai atnaujinti.";
    header("Location: patient_card.php");
    exit();
} catch (Exception $e) {
    $_SESSION["error"] = $e->getMessage();
    header("Location: patient_card.php");
    exit();
}
?>
