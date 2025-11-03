<!DOCTYPE html>
<html lang="lt">
<head>
  <meta charset="UTF-8">
  <title>HOSPITAL</title>
  <style>
    /* Base Styling from patient_card.php */
    body { 
        font-family: Arial, sans-serif; 
        text-align: center; 
        background: #f8f9fa; 
        padding-top: 80px; /* Daugiau vietos viršuje */
    }
    h1 { 
        cursor: pointer; 
        margin-bottom: 40px;
        color: #343a40;
        font-size: 36px;
    }

    /* Card Container for Buttons */
    .card {
        background: white;
        display: inline-block;
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        width: 90%;
        max-width: 400px; /* Optimalus plotis mygtukams */
        box-sizing: border-box;
    }
    
    /* Button Styling */
    .btn {
      display: block; /* Visi mygtukai užima visą plotį */
      padding: 15px 25px;
      margin: 15px 0; /* Vertikalios maržos tarp mygtukų */
      text-decoration: none;
      border-radius: 5px;
      transition: background-color 0.2s;
      font-weight: bold;
      font-size: 16px;
    }

    /* Paciento prisijungimo/registracijos mygtukai (Blue) */
    .btn-patient {
      background-color: #007bff;
      color: white;
    }
    .btn-patient:hover {
      background-color: #0056b3;
    }

    /* Darbuotojo mygtukas (Green) */
    .btn-staff {
      background-color: #28a745;
      color: white;
      margin-top: 30px; /* Daugiau atskyrimo */
    }
    .btn-staff:hover {
      background-color: #218838;
    }
  </style>
</head>
<body>
  <h1 onclick="window.location.href='index.php'">HOSPITAL</h1>
  
  <div class="card">
    <a href="login.php" class="btn btn-patient">Prisijungti (Pacientas)</a>
    <a href="register.php" class="btn btn-patient">Registruotis (Tapti pacientu)</a>

    <a href="doctor/doctorlogin.php" class="btn btn-staff">Prisijungti (Darbuotojams)</a>
  </div>
</body>
</html>