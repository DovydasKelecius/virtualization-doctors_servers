<?php session_start(); ?>
<!DOCTYPE html>
<html lang="lt">
<head>
  <meta charset="UTF-8">
  <title>Prisijungimas - Hospital</title>
  <style>
    /* Base Styling from patient_card.php */
    body { 
        font-family: Arial, sans-serif; 
        text-align: center; 
        background: #f8f9fa; 
        display: flex;
        flex-direction: column;
        align-items: center;
        min-height: 100vh;
        padding-top: 50px; 
    }
    h1 { 
        cursor: pointer; 
        margin-bottom: 30px;
        color: #343a40;
    }

    /* Form Container (Card) */
    form { 
        background: white; 
        padding: 30px; 
        border-radius: 10px; 
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        width: 100%;
        max-width: 380px; /* Standardizuotas plotis */
        text-align: left;
        box-sizing: border-box;
    }

    /* Input/Label Styling */
    label { 
        display: block; 
        margin-top: 15px; 
        font-weight: bold; 
        color: #343a40;
    }
    input { 
        width: 100%; 
        padding: 10px; 
        margin-top: 5px; 
        border: 1px solid #ced4da; 
        border-radius: 5px; 
        box-sizing: border-box;
    }

    /* Button Styling */
    button { 
        width: 100%; 
        padding: 12px; 
        margin-top: 25px; 
        background-color: #007bff; /* Blue button style for main action */
        color: white; 
        border: none; 
        border-radius: 5px; 
        cursor: pointer; 
        font-size: 16px;
        font-weight: bold;
        transition: background-color 0.2s;
    }
    button:hover { 
        background-color: #0056b3; 
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
    
    /* Back Link Styling (Grey button) */
    .back {
      display: block; 
      width: 100%;
      max-width: 380px; /* Suderintas su formos pločiu */
      margin-top: 20px;
      background: #6c757d;
      color: white;
      padding: 12px 20px; 
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
  <h1 onclick="window.location.href='index.php'">HOSPITAL</h1>

  <form action="login_process.php" method="POST">
    <?php if (isset($_SESSION['error'])): ?>
        <div class="error">
            <?= htmlspecialchars($_SESSION['error']) ?>
            <?php unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    
    <label for="personal_code">Asmens kodas:</label>
    <input type="text" id="personal_code" name="personal_code"  maxlength="11" required>
    
    <label for="password">Slaptažodis:</label>
    <input type="password" id="password" name="password" required>
    
    <button type="submit">Prisijungti</button>
  </form>
  
  <a href="index.php" class="back">Grįžti į pagrindinį puslapį</a>
</body>
</html>