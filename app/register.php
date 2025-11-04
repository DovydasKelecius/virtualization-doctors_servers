<?php session_start(); ?>
<!DOCTYPE html>
<html lang="lt">
<head>
  <meta charset="UTF-8">
  <title>Registracija - Hospital</title>
  <style>
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
    .form-container {
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
        width: 100%;
        box-sizing: border-box;
    }
    .input-group {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 0 10px;
    }
    .input-item {
        width: calc(50% - 5px);
        margin-bottom: 18px;
        position: relative;
    }
    .full-width {
        width: 100%;
    }
    label {
        display: block;
        font-weight: bold;
        color: #343a40;
        margin-bottom: 5px;
    }
    input, select {
        width: 100%;
        padding: 10px;
        border: 1px solid #ced4da;
        border-radius: 5px;
        box-sizing: border-box;
        font-size: 14px;
    }
    input.error, select.error {
        border-color: #e55353;
    }
    .error-msg {
        font-size: 12px;
        color: #e55353;
        margin-top: 4px;
        display: none;
    }
    .helper {
        font-size: 11px;
        color: #666;
        margin-top: 3px;
        display: none;
    }
    button {
        width: 100%;
        padding: 12px;
        margin-top: 10px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        font-weight: bold;
    }
    button:disabled {
        background: #ccc;
        cursor: not-allowed;
    }
    .back {
        display: block;
        width: 100%;
        margin-top: 20px;
        background: #6c757d;
        color: white;
        padding: 12px 20px;
        border-radius: 5px;
        text-decoration: none;
        text-align: center;
    }
  </style>
</head>
<body>
  <h1 onclick="window.location.href='index.php'">HOSPITAL</h1>

  <div class="form-container">
    <?php if (isset($_SESSION['error'])): ?>
      <p style="color:#e55353; font-weight:bold; margin-bottom:15px;">
        <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
      </p>
    <?php endif; ?>

    <form id="registerForm" action="register_process.php" method="POST" novalidate>
      <div class="input-group">
        <div class="input-item">
          <label for="first_name">Vardas:</label>
          <input type="text" id="first_name" name="first_name" maxlength="30" required>
          <div class="error-msg" id="err_first_name"></div>
        </div>
        <div class="input-item">
          <label for="last_name">Pavardė:</label>
          <input type="text" id="last_name" name="last_name" maxlength="30" required>
          <div class="error-msg" id="err_last_name"></div>
        </div>
      </div>

      <div class="input-group">
        <div class="input-item full-width">
          <label for="gender">Lytis:</label>
          <select id="gender" name="gender" required>
            <option value="">-- Pasirinkite lytį --</option>
            <option value="Vyras">Vyras</option>
            <option value="Moteris">Moteris</option>
            <option value="Kita">Kita</option>
            <option value="Nenoriu sakyti">Nenoriu sakyti</option>
          </select>
          <div class="error-msg" id="err_gender"></div>
        </div>
      </div>

      <div class="input-group">
        <div class="input-item full-width">
          <label for="email">El. paštas:</label>
          <input type="email" id="email" name="email" maxlength="40" required>
          <div class="error-msg" id="err_email"></div>
        </div>
      </div>

      <div class="input-group">
        <div class="input-item full-width">
          <label for="personal_code">Asmens kodas:</label>
          <input type="text" id="personal_code" name="personal_code" maxlength="11" required>
          <div class="error-msg" id="err_personal_code"></div>
        </div>
      </div>

      <div class="input-group">
        <div class="input-item">
          <label for="password">Slaptažodis:</label>
          <input type="password" id="password" name="password" required>
          <div class="helper" id="help_password">Slaptažodis turi būti ≥ 6 simboliai</div>
          <div class="error-msg" id="err_password"></div>
        </div>
        <div class="input-item">
          <label for="password_repeat">Pakartokite slaptažodį:</label>
          <input type="password" id="password_repeat" name="password_repeat" required>
          <div class="error-msg" id="err_password_repeat"></div>
        </div>
      </div>

      <div class="input-group">
        <div class="input-item full-width">
          <label for="phone">Telefono nr.:</label>
          <input type="text" id="phone" name="phone" required>
          <div class="error-msg" id="err_phone"></div>
        </div>
      </div>

      <button type="submit" id="submitBtn" disabled>Registruotis</button>
    </form>

    <a href="../index.php" class="back">Grįžti į pagrindinį puslapį</a>
  </div>

  <script>
  const form = document.getElementById('registerForm');
  const submitBtn = document.getElementById('submitBtn');

  const fields = [
    'first_name', 'last_name', 'gender',
    'email', 'personal_code', 'password',
    'password_repeat', 'phone'
  ];

  // Add input + blur listeners
  fields.forEach(id => {
    const el = document.getElementById(id);
    el.addEventListener('blur', () => validateField(id));
    el.addEventListener('input', () => {
      const err = document.getElementById('err_' + id);
      err.style.display = 'none';
      el.classList.remove('error');
      validateForm();
    });
  });

  // Show password hint only while focused
  const passwordInput = document.getElementById('password');
  const passwordHelper = document.getElementById('help_password');
  passwordInput.addEventListener('focus', () => passwordHelper.style.display = 'block');
  passwordInput.addEventListener('blur', () => passwordHelper.style.display = 'none');

  function validateField(id) {
    const el = document.getElementById(id);
    const err = document.getElementById('err_' + id);
    const value = el.value.trim();
    let message = '';

    const nameRegex = /^[A-Za-zĄČĘĖĮŠŲŪŽąčęėįšųūž]+$/u; // Lithuanian letters only
    const emailRegex = /^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/;
    const phoneRegex = /^\+?[\d\s\-\(\)]{0,20}\d$/;

    switch (id) {
      case 'first_name':
      case 'last_name':
        if (!value) message = 'Šis laukelis privalomas';
        else if (!nameRegex.test(value)) message = 'Leidžiamos tik raidės';
        else if (value.length === 29) message = 'Pasiektas simbolių limitas (30)';
        break;

      case 'gender':
        if (!value) message = 'Pasirinkite lytį';
        break;

      case 'email':
        if (!value) message = 'Įveskite el. paštą';
        else if (value.length > 40) message = 'El. paštas per ilgas (max 40)';
        else if (!emailRegex.test(value)) message = 'Neteisingas el. pašto formatas';
        break;

      case 'personal_code':
        if (!/^\d{11}$/.test(value)) message = 'Asmens kodas turi būti 11 skaitmenų';
        break;

      case 'password':
        if (value.length < 6) message = 'Slaptažodis per trumpas';
        break;

      case 'password_repeat':
        const pass = document.getElementById('password').value;
        if (value !== pass) message = 'Slaptažodžiai nesutampa';
        break;

      case 'phone':
        if (!phoneRegex.test(value)) message = 'Netinkamas telefono formatas';
        break;
    }

    if (message) {
      err.textContent = message;
      err.style.display = 'block';
      el.classList.add('error');
    } else {
      err.textContent = '';
      err.style.display = 'none';
      el.classList.remove('error');
    }

    validateForm();
  }

  function validateForm() {
    let ok = true;
    fields.forEach(id => {
      const el = document.getElementById(id);
      const err = document.getElementById('err_' + id);
      if (el.value.trim() === '' || (err && err.style.display === 'block')) {
        ok = false;
      }
    });
    submitBtn.disabled = !ok;
  }
</script>

</body>
</html>
