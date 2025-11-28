<?php
session_start();
if (!isset($_SESSION['patient_id'])) {
    header("Location: login.php");
    exit;
}

require 'db.php';

$doctor_id = $_GET['doctor_id'] ?? null;

$doctor = $pdo->query("SELECT * FROM doctors WHERE id = $doctor_id")->fetch(PDO::FETCH_ASSOC);


// Kalendorius
$current_year = date('Y');
$current_month = date('m');

$month = $_GET['month'] ?? $current_month;
$year = $_GET['year'] ?? $current_year;

if (!checkdate($month, 1, $year)) {
    $month = $current_month;
    $year = $current_year;
}

$lt_months = [
    'Sausis', 'Vasaris', 'Kovas', 'Balandis', 'Gegužė', 'Birželis',
    'Liepa', 'Rugpjūtis', 'Rugsėjis', 'Spalis', 'Lapkritis', 'Gruodis'
];
$month_name = $lt_months[(int)$month - 1];
$display_date = new DateTime("$year-$month-01");

$prev_month_date = clone $display_date;
$prev_month_date->modify('-1 month');
$prev_month = $prev_month_date->format('m');
$prev_year = $prev_month_date->format('Y');

$next_month_date = clone $display_date;
$next_month_date->modify('+1 month');
$next_month = $next_month_date->format('m');
$next_year = $next_month_date->format('Y');

// Neleidžia naviguoti atgal nuo esamo mėnesio
$is_past_display_month = ($year < $current_year) || ($year == $current_year && $month <= $current_month);

$days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
$start_day_of_week = $display_date->format('N');

$booked_days = $pdo->query("
    SELECT DATE(appointment_date) AS day, COUNT(*) AS count
    FROM appointments
    WHERE doctor_id = $doctor_id AND EXTRACT(MONTH FROM appointment_date) = $month AND EXTRACT(YEAR FROM appointment_date) = $year
    GROUP BY day
")->fetchAll(PDO::FETCH_KEY_PAIR);

// Čia skaičiuoja, kiek yra laisvų laikų per dieną
$total_slots = 0;
if ($doctor['work_start'] && $doctor['work_end']) {
    $start = new DateTime($doctor['work_start']);
    $end = new DateTime($doctor['work_end']);
    while ($start < $end) {
        $total_slots++;
        $start->modify('+30 minutes');
    }
}
?>


<!DOCTYPE html>
<html lang="lt">
<head>
    <meta charset="UTF-8">
    <title>Pasirinkti datą</title>
    <link rel="stylesheet" href="static/styles.css">
</head>
<body>
    <div class="container">
        <h1 onclick="window.location.href='index.php'">HOSPITAL</h1>
        <h2>Pasirinkite datą vizitui pas Dr. <?= $doctor['first_name'] . ' ' . $doctor['last_name'] ?></h2>

        <div class="calendar-nav">
            <?php if ($is_past_display_month): ?>
                <span class="btn" style="background-color: #6c757d; cursor: not-allowed;">&lt; Ankstesnis</span>
            <?php else: ?>
                <a href="?doctor_id=<?= $doctor_id ?>&month=<?= $prev_month ?>&year=<?= $prev_year ?>" class="btn">&lt; Ankstesnis</a>
            <?php endif; ?>
            
            <h3><?= $month_name . ' ' . $year ?></h3>

            <a href="?doctor_id=<?= $doctor_id ?>&month=<?= $next_month ?>&year=<?= $next_year ?>" class="btn">Sekantis &gt;</a>
        </div>

        <table class="calendar-table">
            <thead>
                <tr>
                    <th>Pirm.</th><th>Antr.</th><th>Treč.</th><th>Ketv.</th><th>Penkt.</th><th>Šešt.</th><th>Sekm.</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <?php
                    // Padaro, kad pirmoje savaitėje nesimatytų buvusio mėnesio dienų prieš mėnesio pradžią
                    for ($i = 1; $i < $start_day_of_week; $i++): ?>
                        <td></td>
                    <?php endfor; ?>

                    <?php

                    for ($day = 1; $day <= $days_in_month; $day++):
                        $date_str = "$year-$month-" . str_pad($day, 2, '0', STR_PAD_LEFT);
                        $day_date = new DateTime($date_str);
                        $day_of_week = $day_date->format('N');
                        $is_past = $day_date <= new DateTime('today');
                        $is_weekend = ($day_of_week >= 6);

                        $booked_count = $booked_days[$date_str] ?? 0;
                        $is_full = ($total_slots > 0 && $booked_count >= $total_slots);
                        
                        $is_available = !$is_past && !$is_weekend && !$is_full;

                        $cell_class = '';
                        if ($is_available) $cell_class = 'day-available';
                        elseif ($is_full) $cell_class = 'day-full';
                        else $cell_class = 'day-unavailable';
                    ?>
                    <td class="<?= $cell_class ?>">
                        <div class="day-number"><?= $day ?></div>
                        <?php if ($is_available): ?>
                            <a href="select_time.php?doctor_id=<?= $doctor_id ?>&date=<?= $date_str ?>">Pasirinkti</a>
                        <?php elseif ($is_full): ?>
                            <span>Užimta</span>
                        <?php elseif ($is_past): ?>
                            <span>Praėjo</span>
                        <?php else: // Savaitgaliuks ?>
                            <span>Nedirba</span>
                        <?php endif; ?>
                    </td>
                    <?php
                        if ($day_of_week == 7 && $day < $days_in_month): ?>
                            </tr><tr>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php
                    // Padaro, kad paskutinėje savaitėje nesimatytų kito mėnesio dienų
                    $remaining_cells = (7 - ($start_day_of_week + $days_in_month - 1) % 7) % 7;
                    if ($remaining_cells > 0) {
                        for ($i = 0; $i < $remaining_cells; $i++) {
                            echo '<td></td>';
                        }
                    }
                    ?>
                </tr>
            </tbody>
        </table>

        <a href="doctor_registration.php" class="btn">Grįžti į Registraciją pas Daktarą</a>
    </div>
</body>
</html>