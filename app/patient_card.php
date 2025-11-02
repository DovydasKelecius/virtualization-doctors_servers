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
  <style>
    /* 🎨 Global Styling and Centering (Retained old style) */
    body {
        font-family: Arial, sans-serif;
        text-align: center;
        background: #f8f9fa;
        padding-top: 40px;
    }

    /* 🚨 Fixed Alert Styling */
    .alert-fixed {
        position: fixed; 
        top: 20px;       
        left: 50%;       
        transform: translateX(-50%); 
        padding: 15px 30px;
        border-radius: 8px;
        z-index: 1000; 
        max-width: 600px;
        width: 90%;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        text-align: center;
        font-weight: bold;
        transition: opacity 0.3s ease-in-out;
    }

    .alert-success {
        color: #155724;
        background-color: #d4edda;
        border: 1px solid #c3e6cb;
    }

    .alert-error {
        color: #721c24;
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
    }

    /* 📐 Standardized Form Styling (Retained old style) */
    form {
        display: inline-block;
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        text-align: left;
        max-width: 700px;
        width: 90%;
        box-sizing: border-box;
    }
    
    h3 {
        color: #343a40;
        border-bottom: 1px solid #dee2e6;
        padding-bottom: 10px;
        margin-bottom: 20px;
        margin-top: 0;
    }

    /* 2-COLUMN LAYOUT FOR INPUTS */
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
    .input-item label {
        margin-top: 0; /* Override default label margin for better alignment */
    }
    /* END 2-COLUMN LAYOUT */

    label {
        display: block;
        margin-top: 15px;
        font-weight: bold;
        color: #343a40;
    }

    /* 📏 Standardized Input Styling (Retained old style) */
    input[type="text"], 
    input[type="email"], 
    input[type="tel"] {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        border: 1px solid #ced4da;
        border-radius: 5px;
        box-sizing: border-box;
    }

    input[disabled] {
        background-color: #e9ecef;
        color: #6c757d;
    }

    /* 🟢 Update Button Styling (Retained old style) */
    button[type="submit"] {
        width: 100%;
        padding: 12px;
        margin-top: 25px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        transition: background-color 0.2s;
    }
    button[type="submit"]:hover {
        background-color: #0056b3;
    }

    /* ⬅️ Centered "Go Back" Link (Retained old style) */
    .back-link {
        display: block;
        width: 340px;
        margin: 20px auto 0 auto;
        background: #6c757d;
        color: white;
        padding: 10px 20px;
        border-radius: 5px;
        text-decoration: none;
        box-sizing: border-box;
        transition: background-color 0.2s;
        font-weight: bold;
    }
    .back-link:hover {
        background: #5a6268;
    }

    /* 🏥 Modal Trigger Button (New, styled like submit button) */
    .history-button {
        width: 100%;
        padding: 10px 15px;
        background-color: #28a745; /* Different color for differentiation */
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        margin-top: 5px;
        transition: background-color 0.2s;
    }
    .history-button:hover {
        background-color: #218838;
    }
    
    /* --- MODAL STYLING (Minimalist, to fit old style) --- */
    .modal {
        display: none; 
        position: fixed; 
        z-index: 2000; 
        left: 0;
        top: 0;
        width: 100%; 
        height: 100%; 
        overflow: auto; 
        background-color: rgba(0,0,0,0.5); /* Semi-transparent black overlay */
        padding-top: 50px;
    }

    .modal.show-modal {
        display: block; 
    }

    .modal-content {
        background-color: #fefefe;
        margin: 5% auto; 
        padding: 30px;
        border: 1px solid #888;
        width: 90%; 
        max-width: 900px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        position: relative;
        text-align: left;
    }

    .modal-content h2 {
        text-align: center;
        color: #007bff;
        margin-top: 0;
        border-bottom: 1px solid #dee2e6;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }

    .close-button {
        color: #aaa;
        float: right;
        font-size: 32px;
        font-weight: bold;
        position: absolute;
        top: 10px;
        right: 20px;
        line-height: 1;
    }

    .close-button:hover,
    .close-button:focus {
        color: #000;
        text-decoration: none;
        cursor: pointer;
    }

    /* 🏥 TABLE STYLING FOR MEDICAL HISTORY (Retained old style) */
    .modal-content table {
        width: 100%; 
        border-collapse: collapse; 
        margin-top: 10px;
        font-size: 0.95em;
    }
    .modal-content th, .modal-content td {
        border: 1px solid #dee2e6;
        padding: 10px;
        text-align: left;
        vertical-align: top;
    }
    .modal-content th {
        background-color: #007bff;
        color: white;
    }
    .modal-content tr:nth-child(even) {
        background-color: #f8f9fa; 
    }
    /* END MODAL TABLE STYLING */
  </style>
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
              <input type="text" value="<?= htmlspecialchars(
                  $p["gender"],
              ) ?>" disabled>
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