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
    $gender = trim($_POST["gender"] ?? "");

    // --- 1. Validation for Name and Surname ---
    // \p{L} allows any kind of letter from any language, including Lithuanian characters (ąčęėįšųūž)
    $name_regex = "/^[\p{L}\s'-]{2,50}$/u"; // Allows 2 to 50 characters
    
    if (empty($first_name) || !preg_match($name_regex, $first_name)) {
        throw new Exception("Vardas turi būti nuo 2 iki 50 raidžių ir negali turėti specialių simbolių.");
    }
    if (empty($last_name) || !preg_match($name_regex, $last_name)) {
        throw new Exception("Pavardė turi būti nuo 2 iki 50 raidžių ir negali turėti specialių simbolių.");
    }
    // --- End Validation for Name and Surname ---

    // --- 2. Email Validation ---
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100) {
        throw new Exception("Neteisingas el. pašto formatas.");
    }
    // --- End Email Validation ---

    // --- 3. Phone Number Validation (Improved) ---
    // Checks for a common international format: digits, optional +, spaces, hyphens.
    // Length check (e.g., min 8 digits, max 20 characters including symbols)
    $phone_clean = preg_replace("/[^0-9\+]/", "", $phone); // Remove non-digit/non-plus characters for basic check
    
    if (empty($phone) || !preg_match("/^[\d\s\-\+]{8,20}$/", $phone) || strlen($phone_clean) < 8) {
        throw new Exception("Neteisingas telefono numerio formatas (turėtų būti min. 8 skaitmenys).");
    }
    // --- End Phone Validation ---

    // --- 4. Gender Validation ---
    $valid_genders = ['Vyras', 'Moteris', 'Kita', 'Nenoriu sakyti'];
    if (!in_array($gender, $valid_genders)) {
        throw new Exception("Neteisinga pasirinkta lyties reikšmė.");
    }
    // --- End Gender Validation ---


    // Prepare and execute the update
    $stmt = $pdo->prepare("
        UPDATE patients
        SET
            first_name = ?,
            last_name = ?,
            email = ?,
            phone = ?,
            gender = ?
        WHERE id = ?
        RETURNING *
    ");

    $result = $stmt->execute([
        $first_name,
        $last_name,
        $email,
        $phone,
        $gender,
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