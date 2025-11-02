<!DOCTYPE html>
<html lang="lt">
<head>
  <meta charset="UTF-8">
  <title>Registracija - Hospital</title>
  <style>
    body { font-family: Arial; text-align: center; background: #f9f9f9; margin-top: 50px; }
    form { display: inline-block; text-align: left; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    label { display: block; margin-top: 10px; font-weight: bold; }
    input, select { width: 300px; padding: 8px; margin-top: 5px; }
    button { width: 100%; padding: 10px; margin-top: 20px; background-color: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; }
    button:disabled { background-color: #ccc; cursor: not-allowed; }
    .error { color: red; font-weight: bold; }
  </style>
</head>
<body>
  <h1 onclick="window.location.href='index.php'" style="cursor:pointer;">HOSPITAL</h1>

  <form id="registerForm" action="register_process.php" method="POST">
    <label>Vardas:</label>
    <input type="text" name="first_name" required>

    <label>Pavardė:</label>
    <input type="text" name="last_name" required>

    <label>Lytis:</label>
    <select name="gender" required>
      <option value="">-- Pasirinkite lytį --</option>
      <option value="Vyras">Vyras</option>
      <option value="Moteris">Moteris</option>
      <option value="Kita">Kita</option>
      <option value="Nenoriu sakyti">Nenoriu sakyti</option>
    </select>

    <label>El. paštas:</label>
    <input type="email" name="email" required>

    <label>Asmens kodas:</label>
    <input type="text" name="personal_code" id="personal_code" required>

    <label>Slaptažodis:</label>
    <input type="password" name="password" id="password" required>

    <label>Pakartokite slaptažodį:</label>
    <input type="password" name="password_repeat" id="password_repeat" required>

    <label>Telefono nr.:</label>
    <input type="text" name="phone" id="phone" required>

    <button type="submit" id="submitBtn" disabled>Registruotis</button>

    <?php
    session_start();
    if (isset($_SESSION['error'])) {
        echo "<p class='error'>" . $_SESSION['error'] . "</p>";
        unset($_SESSION['error']);
    }
    ?>
  </form>

  <script>
    const form = document.getElementById('registerForm');
    const submitBtn = document.getElementById('submitBtn');

    function validateForm() {
      const pass = document.getElementById('password').value;
      const repeat = document.getElementById('password_repeat').value;
      const code = document.getElementById('personal_code').value;
      const phone = document.getElementById('phone').value;

      const passMatch = pass && repeat && pass === repeat;
      const codeValid = /^\d{11}$/.test(code);
      const phoneValid = /^(\+370\d{8}|\d{9})$/.test(phone);

      submitBtn.disabled = !(passMatch && codeValid && phoneValid);
    }
    form.addEventListener('input', validateForm);
  </script>
</body>
</html>
