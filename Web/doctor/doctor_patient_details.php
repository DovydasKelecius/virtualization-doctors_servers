<?php
session_start();
if (!isset($_SESSION['doctor_id'])) {
    header("Location: doctorlogin.php");
    exit;
}

require '../db.php';

$appointment_id = $_GET['appointment_id'] ?? null;
$patient_id = $_GET['patient_id'] ?? null;

if (!$appointment_id || !$patient_id) {
    die("Trūksta duomenų (appointment_id arba patient_id).");
}

// Get Patient Info
$pstmt = $pdo->prepare("SELECT * FROM patients WHERE id = ?");
$pstmt->execute([$patient_id]);
$patient = $pstmt->fetch(PDO::FETCH_ASSOC);

// Get Current Appointment Info
$astmt = $pdo->prepare("SELECT a.*, d.specialization FROM appointments a JOIN doctors d ON a.doctor_id = d.id WHERE a.id = ?");
$astmt->execute([$appointment_id]);
$appointment = $astmt->fetch(PDO::FETCH_ASSOC);

// Get Full Visit History
$hstmt = $pdo->prepare("SELECT a.*, d.specialization FROM appointments a JOIN doctors d ON a.doctor_id = d.id WHERE a.patient_id = ? ORDER BY a.appointment_date DESC");
$hstmt->execute([$patient_id]);
$history = $hstmt->fetchAll(PDO::FETCH_ASSOC);

// Get Medical Records
$rstmt = $pdo->prepare("SELECT mr.*, d.first_name AS doc_first, d.last_name AS doc_last FROM medical_records mr LEFT JOIN doctors d ON mr.doctor_id = d.id WHERE mr.patient_id = ? ORDER BY mr.created_at DESC");
$rstmt->execute([$patient_id]);
$records = $rstmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="lt">
<head>
    <meta charset="UTF-8">
    <title>Paciento duomenys</title>
    <link rel="stylesheet" href="../static/styles.css">
</head>
<body>
    <div class="container">
        <h1 onclick="window.location.href='../index.php'">HOSPITAL</h1>
        <h2>Paciento Kortelė</h2>

        <?php if (isset($_SESSION['message'])): ?>
            <p style="color: green; font-weight: bold;"><?= htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?></p>
        <?php endif; ?>

        <!-- Patient and Appointment Info -->
        <h3>Paciento Duomenys</h3>
        <p><strong>Vardas, Pavardė:</strong> <?= htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']) ?></p>
        <p><strong>Asmens kodas:</strong> <?= htmlspecialchars($patient['personal_code']) ?></p>
        <p><strong>Lytis:</strong> <?= htmlspecialchars($patient['gender']) ?></p>
        <p><strong>Telefono nr.:</strong> <?= htmlspecialchars($patient['phone']) ?></p>

        <hr>

        <h3>Šio Vizito Detalės</h3>
        <p><strong>Data:</strong> <?= htmlspecialchars(date('Y-m-d H:i', strtotime($appointment['appointment_date']))) ?></p>
        <p><strong>Specializacija:</strong> <?= htmlspecialchars($appointment['specialization']) ?></p>
        <p><strong>Paciento Komentaras:</strong> <?= htmlspecialchars($appointment['comment'] ?: '-') ?></p>

        <hr>

        <!-- Actions -->
        <a href="doctor_add_record.php?patient_id=<?= $patient_id ?>&appointment_id=<?= $appointment_id ?>" class="btn">Įvesti Apsilankymo Įrašą</a>
        
        <hr>

        <!-- Medical Records -->
        <h3>Medicininiai Įrašai</h3>
        <?php if (empty($records)): ?>
            <p>Nėra įvestų medicininių įrašų.</p>
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
                    <?php foreach ($records as $r): ?>
                        <tr>
                            <td><?= htmlspecialchars(date('Y-m-d H:i', strtotime($r['created_at']))) ?></td>
                            <td><?= htmlspecialchars($r['doc_first'] . ' ' . $r['doc_last']) ?></td>
                            <td><?= htmlspecialchars($r['event']) ?></td>
                            <td><?= htmlspecialchars($r['diagnosis']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <hr>

        <!-- Full Visit History -->
        <h3>Visų Vizitų Istorija</h3>
        <?php if (empty($history)): ?>
            <p>Nėra vizitų istorijos.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Specializacija</th>
                        <th>Komentaras</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($history as $h): ?>
                        <tr>
                            <td><?= htmlspecialchars(date('Y-m-d H:i', strtotime($h['appointment_date']))) ?></td>
                            <td><?= htmlspecialchars($h['specialization']) ?></td>
                            <td><?= htmlspecialchars($h['comment'] ?: '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <hr>

        <a href="doctor_patients.php" class="btn">Grįžti į Pacientų Sąrašą</a>
    </div>
</body>
</html>