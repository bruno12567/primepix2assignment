<?php
session_start();
include 'db/db.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $movie_id = intval($_POST['movie_id']);

    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
        exit;
    }

    $user_id = $_SESSION['user_id'];

    $stmt = $mysqli->prepare("SELECT COUNT(*) FROM favorites WHERE user_id = ? AND movie_id = ?");
    if ($stmt) {
        $stmt->bind_param("ii", $user_id, $movie_id);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Movie already in favorites']);
            exit;
        }

        $stmt = $mysqli->prepare("INSERT INTO favorites (user_id, movie_id) VALUES (?, ?)");
        if ($stmt) {
            $stmt->bind_param("ii", $user_id, $movie_id);
            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Movie added to favorites']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to add movie to favorites']);
            }
            $stmt->close();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error preparing statement']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error preparing statement']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
