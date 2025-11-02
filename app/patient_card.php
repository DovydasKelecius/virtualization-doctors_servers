<?php
session_start();
if (!isset($_SESSION["patient_id"])) {
    header("Location: login.php");
    exit();
}

// NOTE: It is best practice to move database connection logic (like this)
// into a separate file (e.g., db.php) and use require "db.php";
$host = getenv("DB_HOST") ?: "193.219.91.104";
$port = getenv("DB_PORT") ?: "3545";
$dbname = getenv("DB_NAME") ?: "hospital";
$user = getenv("DB_USER") ?: "hospital_owner";
$password = getenv("DB_PASSWORD") ?: "iLoveUnix";

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
} catch (PDOException $e) {
    die("Database connection error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="lt">
<head>
  <meta charset="UTF-8">
  <title>Paciento kortelė</title>
  <style>
    /* 🎨 Global Styling and Centering */
    body {
        font-family: Arial, sans-serif;
        text-align: center;
        background: #f8f9fa;
        padding-top: 40px;
    }

    /* 🚨 Fixed Alert Styling */
    .alert-fixed {
        position: fixed; /* Fixes the alert relative to the viewport */
        top: 20px;       /* Position it 20px from the top */
        left: 50%;       /* Start position at 50% from the left */
        transform: translateX(-50%); /* Pull back by half its width to truly center */

        /* Message styling */
        padding: 15px 30px;
        border-radius: 8px;
        z-index: 1000; /* Ensures it sits above other content */
        max-width: 600px;
        width: 90%;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        text-align: center;
        font-weight: bold;
        transition: opacity 0.3s ease-in-out; /* Allows for smooth fading */
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

    /* 📐 Standardized Form Styling */
    form {
        display: inline-block;
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        text-align: left;

        /* Max-width for two-column layout */
        max-width: 700px;
        width: 90%;
        box-sizing: border-box;
    }

    /* 2-COLUMN LAYOUT FOR STATIC DATA */
    .read-only-group {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        margin-bottom: 5px;
    }
    .read-only-item {
        width: calc(50% - 10px);
        margin-bottom: 5px;
    }
    /* END 2-COLUMN LAYOUT */

    label {
        display: block;
        margin-top: 15px;
        font-weight: bold;
        color: #343a40;
    }
    .read-only-item label {
        margin-top: 0;
    }

    /* 📏 Standardized Input and Textarea Width */
    input, textarea {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        border: 1px solid #ced4da;
        border-radius: 5px;
        box-sizing: border-box;
    }

    textarea {
        height: 100px;
        resize: vertical;
    }

    /* 🟢 Update Button Styling */
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

    /* ⬅️ Centered "Go Back" Link */
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
    }
    .back-link:hover {
        background: #5a6268;
    }
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

      <label>Vardas:</label>
      <input type="text" name="first_name" value="<?= htmlspecialchars(
          $p["first_name"],
      ) ?>" required>

      <label>Pavardė:</label>
      <input type="text" name="last_name" value="<?= htmlspecialchars(
          $p["last_name"],
      ) ?>" required>

      <label>Asmens Tapybės Informacija</label>
      <div class="read-only-group">
          <div class="read-only-item">
              <label>Asmens kodas:</label>
              <input type="text" value="<?= htmlspecialchars(
                  $p["personal_code"],
              ) ?>" disabled>
          </div>
          <div class="read-only-item">
              <label>Lytis:</label>
              <input type="text" value="<?= htmlspecialchars(
                  $p["gender"],
              ) ?>" disabled>
          </div>
      </div>

      <label>El. paštas:</label>
      <input type="email" name="email" value="<?= htmlspecialchars(
          $p["email"],
      ) ?>" required>

      <label>Telefono numeris:</label>
      <input type="text" name="phone" value="<?= htmlspecialchars(
          $p["phone"],
      ) ?>" required>

      <label>Medicinos istorija:</label>
      <textarea disabled><?= htmlspecialchars(
          $p["medical_history"] ?: "Nėra įrašų",
      ) ?></textarea>

      <button type="submit">Atnaujinti</button>
    </form>

    <a href="patient_home.php" class="back-link">Grįžti atgal</a>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get all fixed alert elements
            const alerts = document.querySelectorAll('.alert-fixed');

            alerts.forEach(alert => {
                // Set a timer to start fading out after 4000 milliseconds (4 seconds)
                setTimeout(() => {
                    alert.style.opacity = '0';
                }, 4000);

                // Set a second timer to remove the element from the DOM after fading is complete (0.3s CSS transition)
                setTimeout(() => {
                    alert.remove();
                }, 4300); // 4000ms + 300ms fade time
            });
        });
    </script>
</body>
</html>
