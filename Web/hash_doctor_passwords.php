// IF YOU WANT TO HASH DOCTOR PASSWORDS, RUN THIS SCRIPT IN BROWSER
<?php
require_once 'db.php';

echo "<h1>Hashing Doctor Passwords...</h1>";

try {
    // Fetch all doctors with plain-text passwords
    $stmt = $pdo->query("SELECT id, password FROM doctors");
    $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($doctors)) {
        echo "<p>No doctors found to hash passwords for.</p>";
    } else {
        echo "<p>Processing " . count($doctors) . " doctors...</p>";
        foreach ($doctors as $doctor) {
            $plain_password = $doctor['password'];
            
            // Check if password is already hashed (simple check for common hash format)
            if (str_starts_with($plain_password, '$2y$') || str_starts_with($plain_password, '$2a$')) {
                echo "<p>Doctor ID " . $doctor['id'] . " password already appears to be hashed. Skipping.</p>";
                continue;
            }

            $hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);

            // Update the doctor's password in the database
            $update_stmt = $pdo->prepare("UPDATE doctors SET password = ? WHERE id = ?");
            $update_stmt->execute([$hashed_password, $doctor['id']]);
            echo "<p>Doctor ID " . $doctor['id'] . " password hashed successfully.</p>";
        }
        echo "<h2>All doctor passwords processed.</h2>";
    }
} catch (PDOException $e) {
    echo "<p style=\"color:red;\">Database error: " . $e->getMessage() . "</p>";
} catch (Exception $e) {
    echo "<p style=\"color:red;\">Error: " . $e->getMessage() . "</p>";
}

echo "<p>Please delete this script (hash_doctor_passwords.php) from your server after use.</p>";
?>