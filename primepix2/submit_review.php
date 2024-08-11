<?php
session_start(); 
include 'db/db.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $movie_id = $_POST['movie_id'];
    
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
    } else {
        die('User is not logged in. Please log in to submit a review.');
    }
    
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    $stmt = $mysqli->prepare("INSERT INTO reviews (movie_id, user_id, rating, comment) VALUES (?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("iiis", $movie_id, $user_id, $rating, $comment);
        $stmt->execute();
        $stmt->close();
        header("Location: movie.php?id=" . $movie_id);
        exit;
    } else {
        echo "Error: " . $mysqli->error;
    }
}
?>
