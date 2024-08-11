<?php
include 'db/db.php';

if (isset($_GET['movie_id'])) {
    $movie_id = $_GET['movie_id'];
    $stmt = $mysqli->prepare("SELECT r.rating, r.comment, u.username FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.movie_id = ?");
    $stmt->bind_param("i", $movie_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $reviews = [];
    while ($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }

    echo json_encode($reviews);
}
?>
