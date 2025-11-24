<?php session_start(); ?>
<!DOCTYPE html>
<html lang="lt">
<head>
  <meta charset="UTF-8">
  <title>Registracija - Hospital</title>
  <link rel="stylesheet" href="/static/styles.css">
</head>
<body>
  <h1 onclick="window.location.href='index.php'">HOSPITAL</h1>

  <div class="form-container">
    <?php if (isset($_SESSION['error'])): ?>
      <p style="color:#e55353; font-weight:bold; margin-bottom:15px;">
        <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
      </p>
    <?php endif; ?>

    <form action="register_process.php" method="POST">
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

      <button type="submit">Registruotis</button>
    </form>

    <a href="../index.php" class="btn btn-secondary">Grįžti į pagrindinį puslapį</a>
  </div>



</body>
</html>
