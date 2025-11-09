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

// Get patient info
$pstmt = $pdo->prepare("
    SELECT first_name, last_name, personal_code, phone, gender
    FROM patients
    WHERE id = ?
");
$pstmt->execute([$patient_id]);
$patient = $pstmt->fetch(PDO::FETCH_ASSOC);

// Get appointment details
$astmt = $pdo->prepare("
    SELECT a.appointment_date, a.comment, d.specialization
    FROM appointments a
    JOIN doctors d ON a.doctor_id = d.id
    WHERE a.id = ?
");
$astmt->execute([$appointment_id]);
$appointment = $astmt->fetch(PDO::FETCH_ASSOC);

// Get appointment-based visit history
$hstmt = $pdo->prepare("
    SELECT a.appointment_date, d.specialization, a.comment
    FROM appointments a
    JOIN doctors d ON a.doctor_id = d.id
    WHERE a.patient_id = ?
    ORDER BY a.appointment_date DESC
");
$hstmt->execute([$patient_id]);
$history = $hstmt->fetchAll(PDO::FETCH_ASSOC);

// Get medical records (entered by doctors, with joins)
$rstmt = $pdo->prepare("
    SELECT 
        mr.event, 
        mr.diagnosis, 
        mr.created_at,
        d.first_name AS doctor_first_name,
        d.last_name AS doctor_last_name,
        a.appointment_date AS related_appointment
    FROM medical_records mr
    LEFT JOIN doctors d ON mr.doctor_id = d.id
    LEFT JOIN appointments a ON mr.appointment_id = a.id
    WHERE mr.patient_id = ?
    ORDER BY mr.created_at DESC
");
$rstmt->execute([$patient_id]);
$records = $rstmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="lt">
<head>
    <meta charset="UTF-8">
    <title>Paciento duomenys</title>
    <link rel="stylesheet" href="/static/styles.css">
</head>
<body>
    <h1 onclick="window.location.href='doctor_home.php'">HOSPITAL</h1>

    <div class="content-container">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert-success">
                <?= htmlspecialchars($_SESSION['message']) ?>
                <?php unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>

        <div class="card patient-details">
            <h2>Paciento ir vizito informacija</h2>
            
            <div class="details-group">
                <div>
                    <h3>Paciento duomenys</h3>
                    <table>
                        <tr>
                            <td>Vardas, Pavardė:</td>
                            <td><?= htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']) ?></td>
                        </tr>
                        <tr>
                            <td>Asmens kodas:</td>
                            <td><?= htmlspecialchars($patient['personal_code']) ?></td>
                        </tr>
                        <tr>
                            <td>Lytis:</td>
                            <td><?= htmlspecialchars($patient['gender']) ?></td>
                        </tr>
                        <tr>
                            <td>Telefono nr.:</td>
                            <td><?= htmlspecialchars($patient['phone']) ?></td>
                        </tr>
                    </table>
                </div>

                <div>
                    <h3>Šio vizito detalės</h3>
                    <table>
                        <tr>
                            <td>Vizito data:</td>
                            <td><?= date('Y-m-d H:i', strtotime($appointment['appointment_date'])) ?></td>
                        </tr>
                        <tr>
                            <td>Specializacija:</td>
                            <td><?= htmlspecialchars($appointment['specialization']) ?></td>
                        </tr>
                        <tr>
                            <td>Paciento komentaras:</td>
                            <td><?= htmlspecialchars($appointment['comment'] ?: '-') ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            
        </div>

        <div class="btn-group">
            <button id="openVisitHistoryModal" class="btn blue-btn">Visų vizitų istorija</button>
            <button id="openMedicalRecordsModal" class="btn blue-btn">Medicininė istorija (Įrašai)</button>
        </div>
        
        <div class="btn-group" style="margin-top: 15px;">
            <a href="doctor_add_record.php?patient_id=<?= $patient_id ?>&appointment_id=<?= $appointment_id ?>" 
               class="btn green-btn" style="flex-grow: 2;">Įvesti apsilankymo duomenis</a>
            <a href="doctor_patients.php" 
               class="btn gray-btn" style="flex-grow: 1;">Grįžti</a>
        </div>
    </div>
    
    <div id="visitHistoryModal" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <h3>Visų vizitų istorija</h3>
            <?php if (empty($history)): ?>
                <p style="text-align: center; color: #6c757d; font-style: italic;">Nėra vizitų istorijos.</p>
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
                                <td><?= date('Y-m-d H:i', strtotime($h['appointment_date'])) ?></td>
                                <td><?= htmlspecialchars($h['specialization']) ?></td>
                                <td><?= htmlspecialchars($h['comment'] ?: '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <div id="medicalRecordsModal" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <h3>Medicininė istorija (Įvesti įrašai)</h3>
            <?php if (empty($records)): ?>
                <p style="text-align: center; color: #6c757d; font-style: italic;">Nėra įvestų medicininių įrašų.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Data (įrašo)</th>
                            <th>Gydytojas</th>
                            <th>Susijęs vizitas</th>
                            <th>Įvykis</th>
                            <th>Išrašas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($records as $r): ?>
                            <tr>
                                <td><?= date('Y-m-d H:i', strtotime($r['created_at'])) ?></td>
                                <td><?= htmlspecialchars($r['doctor_first_name'].' '.$r['doctor_last_name']) ?></td>
                                <td><?= $r['related_appointment'] ? date('Y-m-d H:i', strtotime($r['related_appointment'])) : '-' ?></td>
                                <td><?= htmlspecialchars($r['event']) ?></td>
                                <td><?= htmlspecialchars($r['diagnosis']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Visit History Modal Logic
            const visitModal = document.getElementById('visitHistoryModal');
            const openVisitBtn = document.getElementById('openVisitHistoryModal');
            const closeVisitSpan = visitModal.querySelector('.close-button');

            // Medical Records Modal Logic
            const medicalModal = document.getElementById('medicalRecordsModal');
            const openMedicalBtn = document.getElementById('openMedicalRecordsModal');
            const closeMedicalSpan = medicalModal.querySelector('.close-button');

            // 1. Open Modals
            if (openVisitBtn) {
                openVisitBtn.onclick = function() {
                    visitModal.classList.add('show-modal');
                }
            }
            if (openMedicalBtn) {
                openMedicalBtn.onclick = function() {
                    medicalModal.classList.add('show-modal');
                }
            }

            // 2. Close Modals (X button)
            if (closeVisitSpan) {
                closeVisitSpan.onclick = function() {
                    visitModal.classList.remove('show-modal');
                }
            }
            if (closeMedicalSpan) {
                closeMedicalSpan.onclick = function() {
                    medicalModal.classList.remove('show-modal');
                }
            }

            // 3. Close modals when user clicks anywhere outside of the modal
            window.onclick = function(event) {
                if (event.target == visitModal) {
                    visitModal.classList.remove('show-modal');
                }
                if (event.target == medicalModal) {
                    medicalModal.classList.remove('show-modal');
                }
            }
        });
    </script>
</body>
</html>