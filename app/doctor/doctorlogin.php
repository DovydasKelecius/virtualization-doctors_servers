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
        /* Base Styling from patient_card.php */
        body { 
            font-family: Arial, sans-serif; 
            background: #f8f9fa; 
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            padding-top: 50px;
        }
        h1 { 
            text-align: center; 
            margin-bottom: 30px;
            cursor: pointer;
        }
        
        /* Card Container */
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px; /* Standardized width */
            box-sizing: border-box;
        }
        
        /* Form Styling */
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
            color: #343a40;
        }
        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            box-sizing: border-box;
        }
        
        /* Button Styling */
        .btn {
            background: #007bff; /* Blue button style for main action */
            color: white;
            padding: 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 15px;
            font-weight: bold;
            transition: background-color 0.2s;
        }
        .btn:hover {
            background: #0056b3;
        }
        
        /* Error Styling */
        .error {
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
        }
        
        /* Back Link Styling */
        .back {
            display: block; 
            width: 100%;
            max-width: 400px;
            margin-top: 20px;
            background: #6c757d;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            box-sizing: border-box;
            transition: background-color 0.2s;
            font-weight: bold;
            text-align: center;
        }
        .back:hover {
            background: #5a6268;
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
    <a href="../index.php" class="back">Grįžti į pagrindinį puslapį</a>
</body>
</html>