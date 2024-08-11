<?php
session_start(); 

$isLoggedIn = isset($_SESSION['user_id']);
?>


<?php
include 'includes/header.php';
include 'includes/tmdb.php';

$category = isset($_GET['category']) ? $_GET['category'] : '';

if (!$category) {
    echo 'No category selected.';
    exit;
}

$genres = getGenreList();
if (!$genres) {
    echo 'Failed to fetch genre list from TMDB.';
    exit;
}

$genre_id = null;
foreach ($genres['genres'] as $genre) {
    if (strtolower($genre['name']) === strtolower($category)) {
        $genre_id = $genre['id'];
        break;
    }
}

if (!$genre_id) {
    echo 'Invalid genre selected.';
    exit;
}

$movies = fetchFromTMDB("discover/movie?with_genres=$genre_id");

if (!$movies) {
    echo 'Failed to fetch movies from TMDB.';
    exit;
}
?>
<br>
<main class="container">
    <h2>Movies in <?php echo htmlspecialchars(ucfirst($category)); ?> Category</h2><br>
    <div class="row">
        <?php if (!empty($movies['results'])): ?>
            <?php foreach ($movies['results'] as $movie): ?>
                <div class="col-md-3 mb-3">
                    <div class="card">
                        <?php if (!empty($movie['poster_path'])): ?>
                            <img src="https://image.tmdb.org/t/p/w500<?php echo htmlspecialchars($movie['poster_path']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($movie['title']); ?>">
                        <?php else: ?>
                            <img src="https://via.placeholder.com/500x750?text=No+Image" class="card-img-top" alt="No image available">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($movie['title']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($movie['overview']); ?></p> 
                            <p class="card-text"><strong>Rating:</strong> <?php echo htmlspecialchars($movie['vote_average']); ?>/10</p>
                            <a href="movie.php?id=<?php echo htmlspecialchars($movie['id']); ?>" class="btn btn-primary">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No movies found in this category.</p>
        <?php endif; ?>
    </div>
</main>
<br><br><br><br>



<?php include 'includes/footer.php'; ?>
