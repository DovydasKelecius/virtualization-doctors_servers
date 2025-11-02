<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="lt">
<head>
  <meta charset="UTF-8">
  <title>Sveiki atvykę</title>
  <style>
    /* Base Styling from patient_card.php */
    body { 
        font-family: Arial, sans-serif; 
        margin: 0; 
        padding: 0; 
        background-color: #f8f9fa; 
    }
    
    /* Header (Nav Bar Style) */
    header {
      background-color: white; /* White background for clean look */
      color: #343333;
      padding: 15px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      position: sticky; /* Sticky header */
      top: 0;
      z-index: 100;
    }
    header h1 { 
        margin: 0; 
        cursor: pointer; 
        font-size: 24px;
        color: #007bff; /* Hospital blue */
    }
    header .user {
      font-weight: bold;
      color: #343a40;
    }
    .header-actions {
        display: flex;
        align-items: center;
    }

    /* Logout Button (Red, consistent with other pages) */
    .btn-logout {
      display: inline-block;
      padding: 8px 15px;
      background: #dc3545;
      color: white;
      text-decoration: none;
      border-radius: 5px;
      font-weight: bold;
      margin-left: 20px;
      transition: background-color 0.2s;
    }
    .btn-logout:hover {
      background: #c82333;
    }
    
    /* Main Content Container */
    main {
      text-align: center;
      padding-top: 40px;
    }

    /* Card for Actions */
    .card {
        background: white;
        display: inline-block;
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        width: 90%;
        max-width: 500px;
        box-sizing: border-box;
        text-align: left;
    }
    
    /* Main Action Button (Green, for making appointment) */
    a.button {
      display: block;
      width: 100%;
      padding: 15px 30px;
      background-color: #28a745;
      color: white;
      text-decoration: none;
      border-radius: 5px;
      font-weight: bold;
      font-size: 18px;
      margin-top: 20px;
      transition: background-color 0.2s;
      text-align: center;
    }
    a.button:hover {
      background-color: #218838;
    }

    /* View Details Button (Blue, for patient card) */
    .btn-details {
      display: block;
      width: 100%;
      padding: 15px 30px;
      background-color: #007bff;
      color: white;
      text-decoration: none;
      border-radius: 5px;
      font-weight: bold;
      font-size: 18px;
      margin-top: 15px;
      transition: background-color 0.2s;
      text-align: center;
    }
    .btn-details:hover {
      background-color: #0056b3;
    }
  </style>
</head>
<body>
  <header>
    <h1 onclick="window.location.href='welcome.php'">HOSPITAL</h1>
    <div class="header-actions">
        <div class="user">
          Sveiki, **<?= htmlspecialchars($_SESSION['user']['first_name']) ?>**
        </div>
        <a href="logout.php" class="btn-logout">Atsijungti</a>
    </div>
  </header>
  
  <main>
    <div class="card">
      <h2 style="text-align: center; color: #343a40; margin-top: 0;">Meniu</h2>
      
      <a href="appointment_form.php" class="button">📝 Registracija pas gydytoją</a>
      
      <a href="patient_card.php" class="btn-details">📑 Mano duomenys ir istorija</a>
    </div>
  </main>
</body>
</html>