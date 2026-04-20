<?php
require 'db_production.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        die('Email and password are required. <a href="login.html">Go back</a>');
    }

    $stmt = $pdo->prepare('SELECT id, name, password_hash FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        die('Invalid credentials. <a href="login.html">Try again</a>');
    }

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];





    header('Location: dashboard.html');
    exit;
}

echo 'Invalid request method.';
?>