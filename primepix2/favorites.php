<?php
session_start();
include 'includes/header.php';
include 'includes/tmdb.php';
include 'db/db.php';

if (!isset($_SESSION['user_id'])) {
    die('User is not logged in.');
}

$user_id = $_SESSION['user_id'];

$query = "SELECT movie_id FROM favorites WHERE user_id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$favorite_ids_result = $stmt->get_result();
$favorite_ids = $favorite_ids_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

?>

<main class="container mt-4">
    <h1 class="mb-4">Your Favorite Movies</h1>

    <div id="favorites-list" class="row">
        <?php if (empty($favorite_ids)): ?>
            <p class="text-muted">You have no favorites yet.</p>
        <?php else: ?>
            <?php
            $api_key = '0b9c876cee070f7b42383c077cd84d41'; 
            $base_url = 'https://api.themoviedb.org/3';

            foreach ($favorite_ids as $row):
                $movie_id = intval($row['movie_id']);
                $url = "$base_url/movie/$movie_id?api_key=$api_key";
                $response = file_get_contents($url);

                if ($response === FALSE) {
                    echo '<div class="col-md-4 mb-4"><div class="alert alert-danger">Failed to fetch movie details for ID: ' . htmlspecialchars($movie_id) . '</div></div>';
                    continue;
                }

                $movie = json_decode($response, true);

                if (isset($movie['status_code']) && $movie['status_code'] == 34) {
                    echo '<div class="col-md-4 mb-4"><div class="alert alert-warning">Movie not found for ID: ' . htmlspecialchars($movie_id) . '</div></div>';
                    continue;
                }

                if ($movie):
                    $poster_path = isset($movie['poster_path']) ? 'https://image.tmdb.org/t/p/w500' . htmlspecialchars($movie['poster_path']) : 'https://via.placeholder.com/500x750?text=No+Image';
                    ?>
                    <div class="col-md-4 mb-4" id="movie-<?php echo htmlspecialchars($movie['id']); ?>">
                        <div class="card shadow-sm">
                            <img src="<?php echo $poster_path; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($movie['title']); ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($movie['title']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($movie['overview']); ?></p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="movie.php?id=<?php echo htmlspecialchars($movie['id']); ?>" class="btn btn-primary">View Movie</a>
                                    <button class="btn btn-danger" onclick="removeFromFavorites(<?php echo htmlspecialchars($movie['id']); ?>)">Remove from Favorites</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; endforeach; ?>
        <?php endif; ?>
    </div>
</main>

<script>
function removeFromFavorites(movieId) {
    fetch('remove_from_favorites.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({
            'movie_id': movieId
        })
    }).then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            document.getElementById('movie-' + movieId).remove();
        } else {
            alert(data.message);
        }
    }).catch(error => {
        console.error('Error:', error);
        alert('An error occurred while removing the movie.');
    });
}
</script>
<br><br><br>
