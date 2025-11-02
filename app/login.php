<!DOCTYPE html>
<html lang="lt">
<head>
  <meta charset="UTF-8">
  <title>Prisijungimas - Hospital</title>
  <style>
    body { font-family: Arial; text-align: center; background: #f9f9f9; margin-top: 80px; }
    form { display: inline-block; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    input { width: 300px; padding: 8px; margin-top: 10px; }
    button { width: 100%; padding: 10px; margin-top: 15px; background-color: #007bff; color: #fff; border: none; border-radius: 5px; cursor: pointer; }
    .error { color: red; font-weight: bold; }
  </style>
</head>
<body>
  <h1 onclick="window.location.href='index.php'" style="cursor:pointer;">HOSPITAL</h1>
  <form action="login_process.php" method="POST">
    <label>Asmens kodas:</label><br>
    <input type="text" name="personal_code" required><br>
    <label>Slaptažodis:</label><br>
    <input type="password" name="password" required><br>
    <button type="submit">Prisijungti</button>

    <?php
    session_start();
    if (isset($_SESSION['error'])) {
        echo "<p class='error'>" . $_SESSION['error'] . "</p>";
        unset($_SESSION['error']);
    }
    ?>
  </form>
</body>
</html>
