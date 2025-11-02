<?php
session_start();
// If already logged in, redirect to doctor home
if (isset($_SESSION['doctor_id'])) {
    header("Location: doctor_home.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="lt">
<head>
    <meta charset="UTF-8">
    <title>Darbuotojo prisijungimas</title>
    <style>
        body { 
            font-family: Arial; 
            background: #f7f7f7; 
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            padding-top: 50px;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
        h1 { 
            text-align: center; 
            margin-bottom: 30px;
            cursor: pointer;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .btn {
            background: #28a745;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }
        .btn:hover {
            background: #218838;
        }
        .error {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }
        .back {
            margin-top: 20px;
            color: #666;
            text-decoration: none;
        }
        .back:hover {
            text-decoration: underline;
        }
    </style>
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
    <a href="../index.php" class="back">← Grįžti į pagrindinį puslapį</a>
</body>
</html>