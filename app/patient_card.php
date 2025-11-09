<?php
session_start();
if (!isset($_SESSION["patient_id"])) {
    header("Location: login.php");
    exit();
}

require "db.php";

try {
    $pdo = new PDO(
        "pgsql:host=$host;port=$port;dbname=$dbname",
        $user,
        $password,
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT * FROM patients WHERE id = ?");
    $stmt->execute([$_SESSION["patient_id"]]);
    $p = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$p) {
        // Patient not found, log out or redirect
        header("Location: logout.php");
        exit();
    }
    
    $history_stmt = $pdo->prepare("
        SELECT 
            mr.event, 
            mr.diagnosis, 
            mr.created_at,
            d.first_name AS doctor_first_name,
            d.last_name AS doctor_last_name
        FROM medical_records mr
        LEFT JOIN doctors d ON mr.doctor_id = d.id
        WHERE mr.patient_id = ?
        ORDER BY mr.created_at DESC
    ");
    $history_stmt->execute([$_SESSION["patient_id"]]);
    $history = $history_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // This now catches query execution errors, as connection errors are handled in db.php
    die("Database query error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="lt">
<head>
  <meta charset="UTF-8">
  <title>Paciento kortelė</title>
  <link rel="stylesheet" href="/static/styles.css">
</head>
<body>
  <h1>HOSPITAL</h1>

  <?php if (isset($_SESSION["error"])): ?>
      <div class="alert-fixed alert-error">
          ❌ <?= htmlspecialchars($_SESSION["error"]) ?>
          <?php unset($_SESSION["error"]); ?>
      </div>
    <?php endif; ?>

    <?php if (isset($_SESSION["message"])): ?>
      <div class="alert-fixed alert-success">
          ✅ <?= htmlspecialchars($_SESSION["message"]) ?>
          <?php unset($_SESSION["message"]); ?>
      </div>
    <?php endif; ?>

    <form action="update_patient.php" method="POST">
      <h3>Paciento Duomenys</h3>

      <div class="input-group">
          <div class="input-item">
              <label>Vardas:</label>
              <input type="text" name="first_name" value="<?= htmlspecialchars(
                  $p["first_name"],
              ) ?>" required>
          </div>
          <div class="input-item">
              <label>Pavardė:</label>
              <input type="text" name="last_name" value="<?= htmlspecialchars(
                  $p["last_name"],
              ) ?>" required>
          </div>
      </div>

      <label>Asmens Tapybės Informacija</label>
      <div class="input-group">
          <div class="input-item">
              <label>Asmens kodas:</label>
              <input type="text" value="<?= htmlspecialchars(
                  $p["personal_code"],
              ) ?>" disabled>
          </div>
          <div class="input-item"> 
            <label>Lytis:</label>
            <select name="gender" required>
                <option value="">-- Pasirinkite lytį --</option>
                <option value="Vyras" <?= ($p["gender"] === 'Vyras' ? 'selected' : '') ?>>Vyras</option>
                <option value="Moteris" <?= ($p["gender"] === 'Moteris' ? 'selected' : '') ?>>Moteris</option>
                <option value="Kita" <?= ($p["gender"] === 'Kita' ? 'selected' : '') ?>>Kita</option>
                <option value="Nenoriu sakyti" <?= ($p["gender"] === 'Nenoriu sakyti' ? 'selected' : '') ?>>Nenoriu sakyti</option>
            </select>
          </div>
      </div>

      <div class="input-group">
          <div class="input-item">
              <label>El. paštas:</label>
              <input type="email" name="email" value="<?= htmlspecialchars(
                  $p["email"],
              ) ?>" required>
          </div>
          <div class="input-item">
              <label>Telefono numeris:</label>
              <input type="text" name="phone" value="<?= htmlspecialchars(
                  $p["phone"],
              ) ?>" required>
          </div>
      </div>
      
      <div style="margin-top: 15px;">
        <label>Medicininė istorija:</label>
        <button type="button" id="openHistoryModal" class="history-button">
            Peržiūrėti įrašus (<?= count($history) ?>)
        </button>
      </div>

      <button type="submit">Atnaujinti</button>
    </form>

    <a href="patient_home.php" class="back-link">Grįžti atgal</a>

    <div id="medicalHistoryModal" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <h2>Medicininė istorija</h2>

            <?php if (empty($history)): ?>
                <p style="text-align: center; color: #6c757d; font-style: italic;">Nėra įrašų.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Gydytojas</th>
                            <th>Įvykis</th>
                            <th>Išrašas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($history as $h): ?>
                            <tr>
                                <td><?= date("Y-m-d H:i", strtotime($h["created_at"])) ?></td>
                                <td><?= htmlspecialchars($h["doctor_first_name"] . " " . $h["doctor_last_name"]) ?></td>
                                <td><?= htmlspecialchars($h["event"]) ?></td>
                                <td><?= htmlspecialchars($h["diagnosis"]) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- Alert Fading Logic ---
            const alerts = document.querySelectorAll('.alert-fixed');

            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                }, 4000);

                setTimeout(() => {
                    alert.remove();
                }, 4300); // 4000ms + 300ms fade time
            });

            // --- Modal Logic ---
            const modal = document.getElementById('medicalHistoryModal');
            const openBtn = document.getElementById('openHistoryModal');
            const closeSpan = document.getElementsByClassName('close-button')[0];

            // 1. Open the modal when the button is clicked 
            openBtn.onclick = function() {
                modal.classList.add('show-modal');
            }

            // 2. Close the modal when the (x) is clicked
            closeSpan.onclick = function() {
                modal.classList.remove('show-modal');
            }

            // 3. Close the modal when the user clicks anywhere outside of the modal
            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.classList.remove('show-modal');
                }
            }
        });
    </script>
</body>
</html>