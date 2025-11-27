<?php
session_start();

// --- 1. Security Checks (Authentication & Method) ---

// Redirect if not logged in
if (!isset($_SESSION["patient_id"])) {
    header("Location: login.php");
    exit();
}

// Redirect if not a POST request
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: patient_card.php");
    exit();
}

require "db.php"; // Contains $host, $port, $dbname, $user, $password

try {
    // Establish PDO Connection
    $pdo = new PDO(
        "pgsql:host=$host;port=$port;dbname=$dbname",
        $user,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $patient_id = $_SESSION["patient_id"];
    
    // --- 2. Input Sanitize and Collect ---
    $first_name = trim($_POST["first_name"] ?? "");
    $last_name = trim($_POST["last_name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $gender = trim($_POST["gender"] ?? "");

    // --- 3. Robust Validation ---

    // Name and Surname Validation (Supports Lithuanian/Unicode letters, spaces, hyphens, and apostrophes)
    $name_regex = "/^[\p{L}\s'-]{2,30}$/u";
    
    if (empty($first_name) || !preg_match($name_regex, $first_name)) {
        throw new Exception("Vardas turi būti nuo 2 iki 30 raidžių ir negali turėti skaičių ar specialių simbolių (išskyrus brūkšnelį/apostrofą).");
    }
    if (empty($last_name) || !preg_match($name_regex, $last_name)) {
        throw new Exception("Pavardė turi būti nuo 2 iki 30 raidžių ir negali turėti skaičių ar specialių simbolių.");
    }

    // Email Validation and Sanitization (FIXED: Added stricter domain check)
    $sanitized_email = filter_var($email, FILTER_SANITIZE_EMAIL);

    if (!filter_var($sanitized_email, FILTER_VALIDATE_EMAIL) || strlen($sanitized_email) > 40) {
        throw new Exception("Neteisingas el. pašto formatas.");
    }
    
    /**
     * FIX: The built-in FILTER_VALIDATE_EMAIL is too permissive with TLDs (like 'com2aaaaaaaaaaaaaaa').
     * This additional check forces the email to have a conventional TLD (2 to 10 letters).
     * This blocks the malformed input 'to2mas@gmail.com2aaaaaaaaaaaaaaa'.
     */
    if (!preg_match('/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/', $sanitized_email)) {
        throw new Exception("Neteisingas el. pašto formatas");
    }

    // Use the sanitized email for database operations
    $email = $sanitized_email;

    // Phone Number Validation (FIXED: Added stricter format check to prevent trailing separators)
    $phone_clean = preg_replace("/[^\d]/", "", $phone); // Get only digits

    if (strlen($phone_clean) < 8 || strlen($phone_clean) > 15) {
        throw new Exception("Telefono numeris turi būti sudarytas iš 8 iki 15 skaitmenų.");
    }

    /**
     * FIX: The previous regex allowed phone numbers to end with a separator, e.g., '+32323232-'.
     * New stricter format: Must start with optional '+', can contain spaces/separators, but must END with a digit (\d$).
     */
    $phone_format_regex = "/^\+?[\d\s\-\(\)]{0,20}\d$/";
    //$phone_format_regex = "/^(\+370\d{8}|\d{9})$/"; 

    if (!preg_match($phone_format_regex, $phone)) {
        throw new Exception("Neteisingas telefono numerio formatas. Leistini simboliai: skaitmenys, tarpai, -, ( ), ir + pradžioje. Numeris negali baigtis simboliu.");
    }

    // Gender Validation
    $valid_genders = ['Vyras', 'Moteris', 'Kita', 'Nenoriu sakyti'];
    if (!in_array($gender, $valid_genders)) {
        throw new Exception("Neteisinga pasirinkta lyties reikšmė.");
    }
    
    // --- 4. Database Integrity Check (Email Uniqueness) ---
    // Check if the new email is already in use by *another* patient
    $check_email_stmt = $pdo->prepare("
        SELECT id FROM patients 
        WHERE email = ? AND id != ?
    ");
    $check_email_stmt->execute([$email, $patient_id]);
    
    if ($check_email_stmt->fetch()) {
        throw new Exception("Šis el. pašto adresas jau užregistruotas kito paciento.");
    }
    
    // --- 5. Update Database ---
    $stmt = $pdo->prepare("
        UPDATE patients
        SET
            first_name = ?,
            last_name = ?,
            email = ?,
            phone = ?,
            gender = ?
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

    // Handle update result
    if ($stmt->rowCount() === 0) {
        $_SESSION["message"] = "Duomenys atnaujinti (arba nebuvo jokių pakeitimų).";
    } else {
        $_SESSION["message"] = "Duomenys sėkmingai atnaujinti.";
    }

    header("Location: patient_card.php");
    exit();

} catch (PDOException $e) {
    // Handle database errors
    $_SESSION["error"] = "Įvyko duomenų bazės klaida. Bandykite dar kartą vėliau.";
    header("Location: patient_card.php");
    exit();
} catch (Exception $e) {
    // Handle validation errors
    $_SESSION["error"] = "Klaida: " . $e->getMessage();
    header("Location: patient_card.php");
    exit();
}