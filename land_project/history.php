<?php
require 'db_production.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $land_id = intval($_POST['land_id'] ?? 0);

    if ($land_id <= 0) {
        die('Valid Land ID is required. <a href="history.html">Go back</a>');
    }

    $stmt = $pdo->prepare('SELECT * FROM lands WHERE id = ?');
    $stmt->execute([$land_id]);
    $land = $stmt->fetch();

    if (!$land) {
        die('Land record not found. <a href="history.html">Try again</a>');
    }

    $stmt = $pdo->prepare('SELECT * FROM transactions WHERE land_id = ? ORDER BY id ASC');
    $stmt->execute([$land_id]);
    $transactions = $stmt->fetchAll();

    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Land History - LandChain</title>
    <link rel="stylesheet" href="css/style.css" />
</head>
<body>
    <header class="navbar">
        <div class="brand">LandChain</div>
        <nav>
            <a href="dashboard.html">Dashboard</a>
            <a href="history.html" class="active">History</a>
            <a href="verify.html">Verify</a>
            <a href="add_land.html">Add Land</a>
        </nav>
    </header>

    <main class="page">
        <section class="form-card">
            <h1>Land History</h1>
            <p>Ownership and blockchain transaction chain for land ID <?php echo escape($land_id); ?>.</p>
            <p><strong>Current Owner:</strong> <?php echo escape($land['owner_name']); ?></p>
            <p><strong>Survey Number:</strong> <?php echo escape($land['survey_number']); ?></p>
            <p><strong>Location:</strong> <?php echo escape($land['location']); ?></p>
            <p><strong>Area:</strong> <?php echo escape($land['area']); ?></p>

            <?php if (count($transactions) === 0) : ?>
                <p>No transactions found for this land.</p>
            <?php else : ?>
                <div class="feature-grid">
                    <?php foreach ($transactions as $tx) : ?>
                        <article class="feature-card">
                            <h2>Transaction #<?php echo escape($tx['id']); ?></h2>
                            <p><strong>Date:</strong> <?php echo escape($tx['date']); ?></p>
                            <p><strong>Seller:</strong> <?php echo escape($tx['seller']); ?></p>
                            <p><strong>Buyer:</strong> <?php echo escape($tx['buyer']); ?></p>
                            <p><strong>Previous Hash:</strong> <?php echo escape($tx['previous_hash']); ?></p>
                            <p><strong>Current Hash:</strong> <?php echo escape($tx['current_hash']); ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

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