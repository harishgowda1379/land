<?php
require 'db_production.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $owner_name = trim($_POST['owner_name'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $survey_number = trim($_POST['survey_number'] ?? '');
    $area = trim($_POST['area'] ?? '');

    if ($owner_name === '' || $location === '' || $survey_number === '' || $area === '') {
        die('All fields are required. <a href="add_land.html">Go back</a>');
    }

    $stmt = $pdo->prepare('SELECT id FROM lands WHERE survey_number = ?');
    $stmt->execute([$survey_number]);
    if ($stmt->fetch()) {
        die('Survey number already exists. <a href="add_land.html">Try another</a>');
    }

    $stmt = $pdo->prepare('INSERT INTO lands (owner_name, location, survey_number, area, created_at) VALUES (?, ?, ?, ?, NOW())');
    $stmt->execute([$owner_name, $location, $survey_number, $area]);

    $land_id = $pdo->lastInsertId();
    $date = date('Y-m-d H:i:s');
    $previous_hash = '0';
    $current_hash = hashBlock($land_id, $owner_name, $owner_name, $date, $previous_hash);

    $stmt = $pdo->prepare('INSERT INTO transactions (land_id, seller, buyer, date, current_hash, previous_hash, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())');
    $stmt->execute([$land_id, $owner_name, $owner_name, $date, $current_hash, $previous_hash]);

    echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>Land Registered</title><link rel="stylesheet" href="css/style.css"></head><body><div class="page"><section class="form-card"><h1>Land Registered Successfully</h1><p>Land ID: ' . escape($land_id) . '</p><p>Survey Number: ' . escape($survey_number) . '</p><p>The land has been added with the first blockchain transaction block.</p><a class="btn btn-primary" href="dashboard.html">Back to Dashboard</a></section></div></body></html>';
    exit;
}
echo 'Invalid request method.';
?>