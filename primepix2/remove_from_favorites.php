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

    $stmt = $mysqli->prepare("DELETE FROM favorites WHERE user_id = ? AND movie_id = ?");
    if ($stmt) {
        $stmt->bind_param("ii", $user_id, $movie_id);
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode(['status' => 'success', 'message' => 'Movie removed from favorites']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Movie not found in favorites']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to remove movie from favorites']);
        }
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error preparing statement']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
