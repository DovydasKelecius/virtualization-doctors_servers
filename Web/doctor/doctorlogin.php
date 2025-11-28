<?php
session_start();
?>


<!DOCTYPE html>
<html lang="lt">
<head>
    <meta charset="UTF-8">
    <title>Darbuotojo prisijungimas</title>
    <link rel="stylesheet" href="../static/styles.css">
</head>
<body>
    <div class="container">
        <h1 onclick="window.location.href='../index.php'">HOSPITAL</h1>
        <h2>Darbuotojų Prisijungimas</h2>
        
        <?php 
        if (isset($_SESSION['error'])) {
            echo '<p style="color: red; font-weight: bold;">' . $_SESSION['error'] . '</p>';
            unset($_SESSION['error']);
        }
        ?>

        <form action="doctor_login_process.php" method="POST">
            <label for="docloginid">Darbuotojo ID:</label>
            <input type="text" id="docloginid" name="docloginid" required>
            
            <label for="password">Slaptažodis:</label>
            <input type="password" id="password" name="password" required>
            
            <button type="submit">Prisijungti</button>
        </form>

        <a href="../index.php" class="btn">Grįžti į pagrindinį puslapį</a>
    </div>
</body>
</html>