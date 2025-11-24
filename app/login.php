<?php 
session_start(); 

// NEW CHECK: If already logged in as a DOCTOR, redirect to doctor home
if (isset($_SESSION['doctor_id'])) {
    header("Location: doctor/doctor_home.php");
    exit;
}

// 1. If already logged in as a PATIENT, redirect to patient home
if (isset($_SESSION['patient_id'])) {
    header("Location: patient_home.php");
    exit;
}
?>
<!DOCTYPE html>
<!DOCTYPE html>
<html lang="lt">
<head>
  <meta charset="UTF-8">
  <title>Prisijungimas - Hospital</title>
  <link rel="stylesheet" href="/static/styles.css">
</head>
<body>
  <div class="container">
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
    
    <a href="index.php" class="btn btn-secondary">Grįžti į pagrindinį puslapį</a>
  </div>
</body>
</html>