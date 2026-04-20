<?php
require 'db_production.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $survey_number = trim($_POST['survey_number'] ?? '');

    if ($survey_number === '') {
        die('Survey number is required. <a href="verify.html">Go back</a>');
    }

    $stmt = $pdo->prepare('SELECT * FROM lands WHERE survey_number = ?');
    $stmt->execute([$survey_number]);
    $land = $stmt->fetch();

    if (!$land) {
        die('Land record not found. <a href="verify.html">Try again</a>');
    }

    $land_id = $land['id'];
    $stmt = $pdo->prepare('SELECT * FROM transactions WHERE land_id = ? ORDER BY id ASC');
    $stmt->execute([$land_id]);
    $transactions = $stmt->fetchAll();

    $isValid = true;
    $last_hash = '0';
    $transactionRows = [];

    foreach ($transactions as $tx) {
        $expected = hashBlock($land_id, $tx['seller'], $tx['buyer'], $tx['date'], $last_hash);
        $valid = ($expected === $tx['current_hash'] && $tx['previous_hash'] === $last_hash);
        if (!$valid) {
            $isValid = false;
        }
        $transactionRows[] = [
            'id' => $tx['id'],
            'date' => $tx['date'],
            'seller' => $tx['seller'],
            'buyer' => $tx['buyer'],
            'previous_hash' => $tx['previous_hash'],
            'current_hash' => $tx['current_hash'],
            'valid' => $valid,
        ];
        $last_hash = $tx['current_hash'];
    }

    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Verify Land - LandChain</title>
    <link rel="stylesheet" href="css/style.css" />
</head>
<body>
    <header class="navbar">
        <div class="brand">LandChain</div>
        <nav>
            <a href="dashboard.html">Dashboard</a>
            <a href="history.html">History</a>
            <a href="verify.html" class="active">Verify</a>
            <a href="add_land.html">Add Land</a>
        </nav>
    </header>

    <main class="page">
        <section class="form-card">
            <h1>Verification Result</h1>
            <p><strong>Survey Number:</strong> <?php echo escape($survey_number); ?></p>
            <p><strong>Current Owner:</strong> <?php echo escape($land['owner_name']); ?></p>
            <p><strong>Status:</strong> <span style="color: <?php echo $isValid ? '#7cffb2' : '#ff7b7b'; ?>;"><?php echo $isValid ? 'Valid' : 'Tampered'; ?></span></p>

            <div class="feature-grid">
                <?php foreach ($transactionRows as $tx) : ?>
                    <article class="feature-card">
                        <h2>Transaction #<?php echo escape($tx['id']); ?></h2>
                        <p><strong>Date:</strong> <?php echo escape($tx['date']); ?></p>
                        <p><strong>Seller:</strong> <?php echo escape($tx['seller']); ?></p>
                        <p><strong>Buyer:</strong> <?php echo escape($tx['buyer']); ?></p>
                        <p><strong>Previous Hash:</strong> <?php echo escape($tx['previous_hash']); ?></p>
                        <p><strong>Current Hash:</strong> <?php echo escape($tx['current_hash']); ?></p>
                        <p><strong>Integrity:</strong> <?php echo $tx['valid'] ? 'Valid' : 'Tampered'; ?></p>
                    </article>
                <?php endforeach; ?>
            </div>

            <a class="btn btn-primary" href="dashboard.html">Back to Dashboard</a>
        </section>
    </main>
</body>
</html>
    <?php
    exit;
}

echo 'Invalid request method.';
?>