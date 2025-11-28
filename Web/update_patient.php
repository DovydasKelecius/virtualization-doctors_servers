<?php
session_start();

if (!isset($_SESSION["patient_id"])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: patient_card.php");
    exit();
}

require "db.php";

try {
    $patient_id = $_SESSION["patient_id"];
    
    $first_name = trim($_POST["first_name"] ?? "");
    $last_name = trim($_POST["last_name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $gender = trim($_POST["gender"] ?? "");

    $name_regex = "/^[\p{L}\s'-]{2,30}$/u";
    
    if (empty($first_name) || !preg_match($name_regex, $first_name)) {
        throw new Exception("Vardas turi būti nuo 2 iki 30 raidžių ir negali turėti skaičių ar specialių simbolių (išskyrus brūkšnelį/apostrofą).");
    }
    if (empty($last_name) || !preg_match($name_regex, $last_name)) {
        throw new Exception("Pavardė turi būti nuo 2 iki 30 raidžių ir negali turėti skaičių ar specialių simbolių.");
    }

    $sanitized_email = filter_var($email, FILTER_SANITIZE_EMAIL);
    if (!filter_var($sanitized_email, FILTER_VALIDATE_EMAIL) || strlen($sanitized_email) > 40) {
        throw new Exception("Neteisingas el. pašto formatas.");
    }
    if (!preg_match('/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/', $sanitized_email)) {
        throw new Exception("Neteisingas el. pašto formatas");
    }
    $email = $sanitized_email;

    $phone_clean = preg_replace("/[^\d]/", "", $phone);
    if (strlen($phone_clean) < 8 || strlen($phone_clean) > 15) {
        throw new Exception("Telefono numeris turi būti sudarytas iš 8 iki 15 skaitmenų.");
    }
    $phone_format_regex = "/^\+?[\d\s\-\(\)]{0,20}\d$/";
    if (!preg_match($phone_format_regex, $phone)) {
        throw new Exception("Neteisingas telefono numerio formatas. Leistini simboliai: skaitmenys, tarpai, -, ( ), ir + pradžioje. Numeris negali baigtis simboliu.");
    }

    $valid_genders = ['Vyras', 'Moteris', 'Kita', 'Nenoriu sakyti'];
    if (!in_array($gender, $valid_genders)) {
        throw new Exception("Neteisinga pasirinkta lyties reikšmė.");
    }
    
    $stmt = $pdo->prepare("
        UPDATE patients
        SET first_name = ?, last_name = ?, email = ?, phone = ?, gender = ?
        WHERE id = ?
    ");

    $stmt->execute([
        $first_name,
        $last_name,
        $email,
        $phone,
        $gender,
        $patient_id,
    ]);

    $_SESSION["message"] = "Duomenys sėkmingai atnaujinti.";
    header("Location: patient_card.php");
    exit();

}
catch (PDOException $e) {
    $_SESSION["error"] = "Įvyko duomenų bazės klaida. Bandykite dar kartą vėliau.";
    header("Location: patient_card.php");
    exit();
}
catch (Exception $e) {
    $_SESSION["error"] = "Klaida: " . $e->getMessage();
    header("Location: patient_card.php");
    exit();
}