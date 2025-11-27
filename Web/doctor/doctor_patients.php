<?php
session_start();
if (!isset($_SESSION['doctor_id'])) {
    header("Location: doctorlogin.php");
    exit;
}

require '../db.php';

$doctor_id = $_SESSION['doctor_id'];

// Get doctor info
$dstmt = $pdo->prepare("SELECT first_name, last_name, specialization FROM doctors WHERE id = ?");
$dstmt->execute([$doctor_id]);
$doctor = $dstmt->fetch(PDO::FETCH_ASSOC);

// Determine filter
$filter = $_GET['filter'] ?? 'today';
$today = date('Y-m-d');

// Base query
$query = "
    SELECT 
        a.id AS appointment_id, a.appointment_date, a.comment,
        p.id AS patient_id, p.first_name AS patient_first_name, p.last_name AS patient_last_name
    FROM appointments a
    JOIN patients p ON a.patient_id = p.id
    WHERE a.doctor_id = :doctor_id
";

// Apply date filter
if ($filter === 'today') {
    $query .= " AND DATE(a.appointment_date) = :date_filter";
    $params = [':doctor_id' => $doctor_id, ':date_filter' => $today];
} elseif ($filter === 'future') {
    $query .= " AND DATE(a.appointment_date) > :date_filter";
    $params = [':doctor_id' => $doctor_id, ':date_filter' => $today];
} else { // Default/fallback to today
    $query .= " AND DATE(a.appointment_date) = :date_filter";
    $params = [':doctor_id' => $doctor_id, ':date_filter' => $today];
}

$query .= " ORDER BY a.appointment_date ASC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="lt">
<head>
    <meta charset="UTF-8">
    <title>Mano pacientai</title>
    <link rel="stylesheet" href="../static/styles.css">
</head>
<body>
    <div class="container">
        <h1 onclick="window.location.href='../index.php'">HOSPITAL</h1>
        <h2>Mano Pacientų Vizitai</h2>
        <p>Gydytojas: <strong><?= htmlspecialchars($doctor['first_name'] . ' ' . $doctor['last_name']) ?></strong></p>

        <div style="margin: 20px 0; padding: 10px; border-top: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6;">
            <strong>Filtruoti vizitus:</strong>
            <?php if ($filter === 'today'): ?>
                <span style="margin-left: 10px; font-weight: bold;">Šiandien</span>
            <?php else: ?>
                <a href="doctor_patients.php?filter=today" class="btn">Šiandien</a>
            <?php endif; ?>

            <?php if ($filter === 'future'): ?>
                <span style="margin-left: 10px; font-weight: bold;">Ateities vizitai</span>
            <?php else: ?>
                <a href="doctor_patients.php?filter=future" class="btn">Ateities vizitai</a>
            <?php endif; ?>
        </div>

        <?php if (empty($appointments)): ?>
          <p>Pagal pasirinktą filtrą vizitų nėra.</p>
        <?php else: ?>
          <table>
            <thead>
              <tr>
                <th>Data</th>
                <th>Laikas</th>
                <th>Pacientas</th>
                <th>Komentaras</th>
                <th>Veiksmas</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($appointments as $a): ?>
                <tr>
                  <td><?= htmlspecialchars(date('Y-m-d', strtotime($a['appointment_date']))) ?></td>
                  <td><?= htmlspecialchars(date('H:i', strtotime($a['appointment_date']))) ?></td>
                  <td><?= htmlspecialchars($a['patient_first_name'] . ' ' . $a['patient_last_name']) ?></td>
                  <td><?= htmlspecialchars($a['comment'] ?: '-') ?></td>
                  <td>
                    <a href="doctor_patient_details.php?appointment_id=<?= $a['appointment_id'] ?>&patient_id=<?= $a['patient_id'] ?>" class="btn">Peržiūrėti</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
        
        <a href="doctor_home.php" class="btn" style="margin-top: 20px;">Grįžti atgal</a>
    </div>
</body>
</html>