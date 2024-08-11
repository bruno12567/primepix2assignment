<?php
include 'db/db.php';

session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];

        $fav_stmt = $pdo->prepare("SELECT movie_id FROM favorites WHERE user_id = :user_id");
        $fav_stmt->bindParam(':user_id', $user['id']);
        $fav_stmt->execute();
        $favorites = $fav_stmt->fetchAll(PDO::FETCH_COLUMN, 0);

        $_SESSION['favorites'] = $favorites;

        header("Location: index.php");
        exit();
    } else {
        $error = "Invalid email or password";
    }
}

include 'includes/header.php';
include 'templates/login.php';
?>
