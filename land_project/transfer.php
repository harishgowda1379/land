<?php
require 'db_production.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $land_id = intval($_POST['land_id'] ?? 0);
    $buyer = trim($_POST['buyer'] ?? '');

    if ($land_id <= 0 || $buyer === '') {
        die('Land ID and buyer name are required. <a href="transfer.html">Go back</a>');
    }

    $stmt = $pdo->prepare('SELECT * FROM lands WHERE id = ?');
    $stmt->execute([$land_id]);
    $land = $stmt->fetch();

    if (!$land) {
        die('Land record not found. <a href="transfer.html">Try again</a>');
    }

    $seller = $land['owner_name'];
    if ($seller === $buyer) {
        die('The buyer must be different from the current owner. <a href="transfer.html">Try again</a>');
    }

    $stmt = $pdo->prepare('SELECT current_hash FROM transactions WHERE land_id = ? ORDER BY id DESC LIMIT 1');
    $stmt->execute([$land_id]);
    $lastTransaction = $stmt->fetch();

    $previous_hash = $lastTransaction ? $lastTransaction['current_hash'] : '0';
    $date = date('Y-m-d H:i:s');
    $current_hash = hashBlock($land_id, $seller, $buyer, $date, $previous_hash);

    $stmt = $pdo->prepare('INSERT INTO transactions (land_id, seller, buyer, date, current_hash, previous_hash, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())');
    $stmt->execute([$land_id, $seller, $buyer, $date, $current_hash, $previous_hash]);

    $stmt = $pdo->prepare('UPDATE lands SET owner_name = ? WHERE id = ?');
    $stmt->execute([$buyer, $land_id]);

    echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>Transfer Complete</title><link rel="stylesheet" href="css/style.css"></head><body><div class="page"><section class="form-card"><h1>Ownership Transferred</h1><p>Land ID: ' . escape($land_id) . '</p><p>Seller: ' . escape($seller) . '</p><p>Buyer: ' . escape($buyer) . '</p><p>The transaction has been stored with a new blockchain hash.</p><a class="btn btn-primary" href="dashboard.html">Back to Dashboard</a></section></div></body></html>';
    exit;
}
echo 'Invalid request method.';
?>