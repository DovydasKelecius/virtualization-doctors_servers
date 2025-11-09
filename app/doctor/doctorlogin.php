<?php
session_start();

// 1. If already logged in as a DOCTOR, redirect to doctor home
if (isset($_SESSION['doctor_id'])) {
    header("Location: doctor_home.php");
    exit;
}

// 2. NEW CHECK: If already logged in as a PATIENT, redirect to patient home
if (isset($_SESSION['patient_id'])) {
    header("Location: ../patient_home.php"); // Assuming doctor files are in a subdirectory
    exit;
}
?>
<!DOCTYPE html>
<html lang="lt">
<head>
    <meta charset="UTF-8">
    <title>Darbuotojo prisijungimas</title>
    <link rel="stylesheet" href="/static/styles.css">
</head>
<body>
    <div class="container">
        <h1 onclick="window.location.href='../index.php'">HOSPITAL</h1>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="error">
                <?= htmlspecialchars($_SESSION['error']) ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        <form action="doctor_login_process.php" method="POST">
            <div class="form-group">
                <label for="docloginid">Darbuotojo ID:</label>
                <input type="text" id="docloginid" name="docloginid" required>
            </div>
            <div class="form-group">
                <label for="password">Slaptažodis:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">Prisijungti</button>
        </form>
    </div>
    <a href="../index.php" class="back">Grįžti į pagrindinį puslapį</a>
</body>
</html>