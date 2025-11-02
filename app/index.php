<!DOCTYPE html>
<html lang="lt">
<head>
  <meta charset="UTF-8">
  <title>HOSPITAL</title>
  <style>
    body { font-family: Arial; text-align: center; margin-top: 100px; background: #f7f7f7; }
    .btn {
      display: inline-block;
      padding: 12px 25px;
      margin: 15px;
      background-color: #007bff;
      color: #fff;
      text-decoration: none;
      border-radius: 6px;
      transition: background-color 0.2s;
    }
    .btn:hover {
      background-color: #0056b3;
    }
    .btn-staff {
      background-color: #28a745;
    }
    .btn-staff:hover {
      background-color: #218838;
    }
    h1 { cursor: pointer; }
  </style>
</head>
<body>
  <h1 onclick="window.location.href='index.php'">HOSPITAL</h1>
  <a href="login.php" class="btn">Prisijungti</a>
  <a href="register.php" class="btn">Tapti pacientu</a>
  <br>
  <a href="doctor/doctorlogin.php" class="btn btn-staff">Darbuotojams</a>
</body>
</html>
