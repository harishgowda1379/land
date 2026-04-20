<?php
session_start();

$host = 'localhost';
$db = 'land_chain';
$user = 'root';
// If your MySQL root account has a password, set it here or export LAND_DB_PASSWORD.
$pass = getenv('LAND_DB_PASSWORD') ?: '';
$port = getenv('LAND_DB_PORT') ?: 3306;

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage() . '. Update db.php with the correct MySQL credentials or set LAND_DB_PASSWORD.');
}

function hashBlock($land_id, $seller, $buyer, $date, $previous_hash)
{
    return hash('sha256', $land_id . $seller . $buyer . $date . $previous_hash);
}

function escape($value)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
