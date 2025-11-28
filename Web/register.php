<?php session_start(); ?>

<!DOCTYPE html>
<html lang="lt">
<head>
    <meta charset="UTF-8">
    <title>Registracija - Hospital</title>
    <link rel="stylesheet" href="static/styles.css">
</head>
<body>
    <div class="container">
        <h1 onclick="window.location.href='index.php'">HOSPITAL</h1>

        <?php 
        if (isset($_SESSION['error'])) {
            echo '<p style="color: red;">' . $_SESSION['error'] . '</p>';
            unset($_SESSION['error']);
        }
        ?>

        <form action="register_process.php" method="POST">
            <label for="first_name">Vardas:</label>
            <input type="text" id="first_name" name="first_name" maxlength="30" required>
            
            <label for="last_name">Pavardė:</label>
            <input type="text" id="last_name" name="last_name" maxlength="30" required>
            
            <label for="gender">Lytis:</label>
            <select id="gender" name="gender" required>
                <option value="">-- Pasirinkite lytį --</option>
                <option value="Vyras">Vyras</option>
                <option value="Moteris">Moteris</option>
                <option value="Kita">Kita</option>
                <option value="Nenoriu sakyti">Nenoriu sakyti</option>
            </select>
            
            <label for="email">El. paštas:</label>
            <input type="email" id="email" name="email" maxlength="40" required>
            
            <label for="personal_code">Asmens kodas:</label>
            <input type="text" id="personal_code" name="personal_code" maxlength="11" required>
            
            <label for="password">Slaptažodis:</label>
            <input type="password" id="password" name="password" required>
            
            <label for="password_repeat">Pakartokite slaptažodį:</label>
            <input type="password" id="password_repeat" name="password_repeat" required>
            
            <label for="phone">Telefono nr.:</label>
            <input type="text" id="phone" name="phone" required>
            
            <button type="submit">Registruotis</button>
        </form>

        <a href="index.php" class="btn btn-secondary">Grįžti į pagrindinį puslapį</a>
    </div>
</body>
</html>