<?php
session_start();
include 'db/db.php';

if (isset($_GET['username'])) {
    $username = $_GET['username'];
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        echo 'Username is already taken.';
    } else {
        echo 'Username is available.';
    }
}
?>
