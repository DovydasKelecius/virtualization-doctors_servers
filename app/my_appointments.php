<?php
session_start();
if (!isset($_SESSION["patient_id"])) {
    header("Location: login.php");
    exit();
}

require "db.php";

$patient_id = $_SESSION["patient_id"];

// Get patient info
$stmt = $pdo->prepare(
    "SELECT first_name, last_name FROM patients WHERE id = ?",
);
$stmt->execute([$patient_id]);
$patient = $stmt->fetch(PDO::FETCH_ASSOC);

// Get appointments
$appointments = $pdo->prepare(
    "SELECT
        a.id,
        d.specialization,
        a.appointment_date,
        a.comment,
        d.first_name AS doctor_first_name,
        d.last_name AS doctor_last_name
    FROM
        appointments a
    LEFT JOIN
        doctors d ON a.doctor_id = d.id
    WHERE
        a.patient_id = ?
    ORDER BY
        a.appointment_date ASC",
);
$appointments->execute([$patient_id]);
$list = $appointments->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="lt">
<head>
    <meta charset="UTF-8">
    <title>Mano vizitai</title>
    <link rel="stylesheet" href="static/styles.css">
</head>
<body>
    <div class="container">
        <h1 onclick="window.location.href='index.php'">HOSPITAL</h1>
        <h3>Pacientas: <?= htmlspecialchars($patient["first_name"]) ?> <?= htmlspecialchars($patient["last_name"]) ?></h3>

        <h2>Jūsų registracijos</h2>

        <?php if (empty($list)): ?>
            <p>Jūs dar nesate užsiregistravę vizitui.</p>
        <?php else: ?>
            <table>
                <tr>
                    <th>Specializacija</th>
                    <th>Gydytojas</th>
                    <th>Data ir Laikas</th>
                    <th>Aprašymas</th>
                    <th>Veiksmas</th>
                </tr>
                <?php foreach ($list as $a): ?>
                <tr>
                    <td><?= htmlspecialchars($a["specialization"]) ?></td>
                    <td><?= htmlspecialchars($a["doctor_first_name"] . " " . $a["doctor_last_name"]) ?></td>
                    <td><?= htmlspecialchars($a["appointment_date"]) ?></td>
                    <td><?= htmlspecialchars($a["comment"]) ?></td>
                    <td>
                        <a href="cancel_appointment.php?id=<?= $a["id"] ?>"
                           class="btn btn-danger"
                           onclick="return confirm('Ar tikrai norite atšaukti šį vizitą?');">
                            Atšaukti
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>

        <a href="patient_home.php" class="btn">Grįžti atgal</a>
    </div>
</body>
</html>