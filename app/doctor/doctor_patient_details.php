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
    <style>
        /* --- General Layout and Typography --- */
        body { 
            font-family: Arial, sans-serif; 
            background: #f8f9fa; 
            margin: 0; 
            padding-top: 40px; 
            text-align: center;
        }
        h1 { 
            cursor: pointer; 
            margin-bottom: 20px;
            color: #343a40;
        }
        
        /* Main Content Container */
        .content-container {
            width: 90%;
            max-width: 900px;
            margin: 0 auto;
            text-align: left;
        }

        /* Card styles */
        .card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        /* --- New: Details Group for two columns layout --- */
        .details-group {
            display: flex;
            gap: 40px;
            margin-bottom: 10px; /* Space before next element (buttons) */
        }
        
        .details-group > div {
            flex: 1; /* Each child div takes equal space */
        }

        .details-group h3 {
            color: #495057; /* Darker secondary color */
            margin-bottom: 15px;
            font-size: 1.1em;
            border-bottom: 1px solid #f1f1f1;
            padding-bottom: 5px;
        }

        /* Patient Details Table */
        .patient-details table {
            width: 100%;
            border-collapse: collapse;
        }
        .patient-details td {
            padding: 10px 0; /* Increased padding */
            border-bottom: 1px dotted #e9ecef;
        }
        .patient-details td:first-child {
            font-weight: bold;
            color: #343a40; /* Darker text for labels */
            width: 150px;
        }
        .patient-details table tr:last-child td {
            border-bottom: none; /* Remove border from last row */
        }

        /* --- Table Styling (for history/records) --- */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        th, td {
            border: 1px solid #dee2e6;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #e9ecef; /* Light gray header */
            color: #343a40;
            font-weight: bold;
        }
        
        /* --- Buttons Group and Individual Buttons --- */
        .btn-group {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        
        .btn {
            display: block;
            flex-grow: 1;
            padding: 12px 15px; /* Slightly larger buttons */
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            font-size: 14px;
            transition: background-color 0.2s, box-shadow 0.2s;
            border: none;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .btn:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        .green-btn {
            background-color: #28a745;
            color: white;
        }
        .green-btn:hover {
            background-color: #218838;
        }
        .blue-btn {
            background-color: #007bff;
            color: white;
        }
        .blue-btn:hover {
            background-color: #0056b3;
        }
        .gray-btn {
            background-color: #6c757d; 
            color: white;
        }
        .gray-btn:hover {
            background-color: #5a6268;
        }
        
        /* --- Alert/Message Styling --- */
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: bold;
        }
        
        /* --- MODAL STYLING --- (Unchanged, as they were working fine) */
        .modal {
            display: none; 
            position: fixed; 
            z-index: 100; 
            left: 0;
            top: 0;
            width: 100%; 
            height: 100%; 
            overflow: auto; 
            background-color: rgba(0,0,0,0.6); 
            padding-top: 60px;
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
            max-width: 1100px; 
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.5); 
            position: relative;
            text-align: left;
        }

        .close-button {
            color: #aaa;
            float: right;
            font-size: 32px;
            font-weight: bold;
            line-height: 1;
        }

        .close-button:hover,
        .close-button:focus {
            color: #000;
            text-decoration: none;
            cursor: pointer;
        }
        
        .modal h3 {
            color: #343a40;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .modal-content table {
            margin-top: 15px;
        }
    </style>
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