<?php
require_once __DIR__ . '/../includes/config.php';

function getConnection() {
    global $mysqli;
    return $mysqli;
}

try {
    $pdo = new PDO('mysql:host=localhost;dbname=primepix', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

$mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
?>
