<?php
require 'db_production.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($name === '' || $email === '' || $password === '') {
        die('All fields are required. <a href="register.html">Go back</a>');
    }

    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);

    if ($stmt->fetch()) {
        die('Email already registered. <a href="register.html">Try again</a>');
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare('INSERT INTO users (name, email, password_hash, created_at) VALUES (?, ?, ?, NOW())');
    $stmt->execute([$name, $email, $passwordHash]);

    header('Location: login.html');
    exit;
}

echo 'Invalid request method.';
?>
