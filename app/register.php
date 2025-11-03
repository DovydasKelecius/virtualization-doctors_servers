<!DOCTYPE html>
<html lang="lt">
<head>
  <meta charset="UTF-8">
  <title>Registracija - Hospital</title>
  <style>
    /* Base Styling from patient_card.php */
    body { 
        font-family: Arial, sans-serif; 
        text-align: center; 
        background: #f8f9fa; 
        padding-top: 40px; 
    }
    h1 {
        cursor: pointer;
        margin-bottom: 20px;
    }

    /* Form Container (Card) */
    .form-container { /* Pridėtas bendras konteineris centravimui */
        width: 90%;
        max-width: 550px;
        margin: 0 auto;
    }

    form { 
        text-align: left; 
        background: white; 
        padding: 30px; 
        border-radius: 10px; 
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        width: 100%; /* Uztikrina kad forma uzims visa konteinerio ploti */
        box-sizing: border-box;
    }
    
    /* Input Group for 2-Column Layout */
    .input-group {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        margin-bottom: 5px;
    }
    .input-item {
        width: calc(50% - 10px);
        margin-bottom: 5px;
    }
    .full-width {
        width: 100%;
    }

    /* Input/Label Styling */
    label { 
        display: block; 
        margin-top: 15px; 
        font-weight: bold; 
        color: #343a40;
    }
    input, select { 
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
    button:hover:not(:disabled) { 
        background-color: #0056b3; 
    }
    button:disabled { 
        background-color: #ccc; 
        cursor: not-allowed; 
    }
    
    /* Error Styling */
    .error { 
        color: #721c24;
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
        padding: 10px;
        border-radius: 5px;
        text-align: center;
        margin-bottom: 20px; /* Pakeista is margin-top: 20px */
        font-weight: bold;
    }
    
    /* Back Link Styling (Dabar už formos ribų) */
    .back {
      display: block; 
      width: 100%; /* Paims 550px is .form-container */
      margin-top: 20px;
      background: #6c757d;
      color: white;
      padding: 12px 20px; /* Padidinau padding, kad atrodytų kaip mygtukas */
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
  <?php session_start(); ?>
  <h1 onclick="window.location.href='index.php'">HOSPITAL</h1>

  <div class="form-container">
    
    <?php
    if (isset($_SESSION['error'])) {
        echo "<p class='error'>" . htmlspecialchars($_SESSION['error']) . "</p>";
        unset($_SESSION['error']);
    }
    ?>

    <form id="registerForm" action="register_process.php" method="POST">
      
      <div class="input-group">
          <div class="input-item">
              <label>Vardas:</label>
              <input type="text" name="first_name" required>
          </div>
          <div class="input-item">
              <label>Pavardė:</label>
              <input type="text" name="last_name" required>
          </div>
      </div>

      <div class="input-group">
          <div class="input-item full-width">
              <label>Lytis:</label>
              <select name="gender" required>
                  <option value="">-- Pasirinkite lytį --</option>
                  <option value="Vyras">Vyras</option>
                  <option value="Moteris">Moteris</option>
                  <option value="Kita">Kita</option>
                  <option value="Nenoriu sakyti">Nenoriu sakyti</option>
              </select>
          </div>
      </div>

      <div class="input-group">
          <div class="input-item full-width">
              <label>El. paštas:</label>
              <input type="email" name="email" required>
          </div>
      </div>

      <div class="input-group">
          <div class="input-item full-width">
              <label>Asmens kodas:</label>
              <input type="text" name="personal_code" id="personal_code" required>
          </div>
      </div>

      <div class="input-group">
          <div class="input-item">
              <label>Slaptažodis:</label>
              <input type="password" name="password" id="password" required>
          </div>
          <div class="input-item">
              <label>Pakartokite slaptažodį:</label>
              <input type="password" name="password_repeat" id="password_repeat" required>
          </div>
      </div>

      <div class="input-group">
          <div class="input-item full-width">
              <label>Telefono nr.:</label>
              <input type="text" name="phone" id="phone" required>
          </div>
      </div>

      <button type="submit" id="submitBtn" disabled>Registruotis</button>
      
    </form>
    
    <a href="../index.php" class="back">Grįžti į pagrindinį puslapį</a>

  </div>

  <script>
    const form = document.getElementById('registerForm');
    const submitBtn = document.getElementById('submitBtn');

    function validateForm() {
      const pass = document.getElementById('password').value;
      const repeat = document.getElementById('password_repeat').value;
      const code = document.getElementById('personal_code').value;
      const phone = document.getElementById('phone').value;

      // Check all fields for content before validating patterns
      const allFieldsFilled = [...form.querySelectorAll('input[required], select[required]')].every(field => field.value.trim() !== '');

      const passMatch = pass === repeat;
      // Added a check for minimum password length for better UX, e.g., 6 chars
      const passLength = pass.length >= 6; 
      const codeValid = /^\d{11}$/.test(code);
      // Pataisytas telefono numerio patikrinimas (leistinas formatas +370XXXXXXXX arba XXXXXXXXX)
      const phoneValid = /^(\+370\d{8}|\d{9})$/.test(phone); 

      submitBtn.disabled = !(allFieldsFilled && passMatch && passLength && codeValid && phoneValid);
    }
    form.addEventListener('input', validateForm);
  </script>
</body>
</html>