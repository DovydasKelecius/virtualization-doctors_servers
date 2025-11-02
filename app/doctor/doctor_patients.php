<?php
session_start();
if (!isset($_SESSION['doctor_id'])) {
    header("Location: doctorlogin.php");
    exit;
}

require '../db.php';
?>
<!DOCTYPE html>
<html lang="lt">
<head>
<meta charset="UTF-8">
<title>Pasirinkite dieną – Mano pacientai</title>
<style>
  body { font-family: Arial; text-align: center; background:#f7f7f7; padding-top:50px; }
  .container { background:white; display:inline-block; padding:30px; border-radius:10px;
               box-shadow:0 0 10px rgba(0,0,0,0.1); }
  input[type="date"] { padding:10px; font-size:16px; }
  button { padding:10px 20px; background:#28a745; color:white; border:none; border-radius:5px; cursor:pointer; margin-top:15px; }
  button:hover { background:#218838; }
  a { text-decoration:none; color:#007bff; display:inline-block; margin-top:20px; }
</style>
</head>
<body>

<h1 onclick="window.location.href='doctor_home.php'" style="cursor:pointer;">HOSPITAL</h1>
<div class="container">
  <h2>Pasirinkite dieną peržiūrai</h2>
  <form action="doctor_patients_list.php" method="GET">
    <input type="date" name="date" required>
    <button type="submit">Peržiūrėti pacientus</button>
  </form>
  <a href="doctor_home.php">← Grįžti atgal</a>
</div>

</body>
</html>
