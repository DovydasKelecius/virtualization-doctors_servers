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
    body { font-family: Arial; margin: 0; padding: 0; background-color: #f2f2f2; }
    header {
      background-color: #007BFF;
      color: white;
      padding: 15px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    header h1 { margin: 0; cursor: pointer; }
    header .user {
      font-weight: bold;
      margin-left: 20px;
    }
    main {
      text-align: center;
      margin-top: 100px;
    }
    a.button {
      display: inline-block;
      padding: 15px 30px;
      background-color: #28a745;
      color: white;
      text-decoration: none;
      border-radius: 8px;
    }
    a.button:hover {
      background-color: #1e7e34;
    }
  </style>
</head>
<body>
  <header>
    <div class="user">
      <?= htmlspecialchars($_SESSION['user']['first_name'] . ' ' . $_SESSION['user']['last_name']) ?>
    </div>
    <h1 onclick="window.location.href='index.php'">HOSPITAL</h1>
  </header>

  <main>
    <a href="patient_card.php" class="button">Paciento kortelė</a>
  </main>
</body>
</html>
