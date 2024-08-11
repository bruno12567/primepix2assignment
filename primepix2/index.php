<?php
session_start(); 

$isLoggedIn = isset($_SESSION['user_id']);
?>

<?php
require_once __DIR__ . '/db/db.php';
include 'includes/header.php';
include 'includes/tmdb.php';

$top_movies = fetchFromTMDB("movie/popular");
$new_movies = fetchFromTMDB("movie/upcoming");
$recommendations = fetchFromTMDB("movie/top_rated");
$genres = getGenreList(); 

if (!$top_movies || !$new_movies || !$recommendations || !$genres) {
    echo 'Failed to fetch movie data from TMDB.';
    exit;
}

?>
<main class="container mt-4">
<div id="bannerCarousel" class="carousel slide" data-ride="carousel">
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="assets/images/image1.jpg" class="d-block w-100" alt="Image 1">
        </div>
        <div class="carousel-item">
            <img src="assets/images/image2.jpg" class="d-block w-100" alt="Image 2">
        </div>
        <div class="carousel-item">
            <img src="assets/images/image3.jpeg" class="d-block w-100" alt="Image 3">
        </div>
        <div class="carousel-item">
            <img src="assets/images/image4.jpeg" class="d-block w-100" alt="Image 3">
        </div>
        <div class="carousel-item">
            <img src="assets/images/image5.jpeg" class="d-block w-100" alt="Image 3">
        </div>
    </div>
</div>

<style>
   

</style>

<script>
    $('#bannerCarousel').carousel({
        interval: 2500, 
        ride: 'carousel'
    });
</script><br>
<!-- <h2>Categories</h2><br> -->
    <div class="category-btn-group mb-4" role="group" aria-label="Categories">
        <?php if (!empty($genres['genres'])): ?>
            <?php foreach ($genres['genres'] as $genre): ?>
                <a href="category.php?category=<?php echo urlencode($genre['name']); ?>" class="category-btn">
                    <?php echo htmlspecialchars($genre['name']); ?>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No categories available.</p>
        <?php endif; ?>
    </div>

    <br>
    <h2>Top Movies of the Month</h2><br>
    <div class="row">
        <?php if (!empty($top_movies['results'])): ?>
            <?php foreach (array_slice($top_movies['results'], 0, 8) as $movie): ?>
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
                            <p class="card-text"><strong>Score:</strong> <?php echo htmlspecialchars($movie['vote_average']); ?>/10</p>
                            <a href="movie.php?id=<?php echo htmlspecialchars($movie['id']); ?>" class="btn btn-primary">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No top movies found.</p>
        <?php endif; ?>
    </div>
    <br>        
    <br>        
    <h2>New Movies Coming</h2><br>
    <div class="row">
        <?php if (!empty($new_movies['results'])): ?>
            <?php foreach (array_slice($new_movies['results'], 0, 4) as $movie): ?>
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
                            <p class="card-text"><strong>Score:</strong> <?php echo htmlspecialchars($movie['vote_average']); ?>/10</p>
                            <a href="movie.php?id=<?php echo htmlspecialchars($movie['id']); ?>" class="btn btn-primary">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No new releases found.</p>
        <?php endif; ?>
    </div>
    <br>
    <h2>Top Recommendations</h2><br>
    <div class="row">
        <?php if (!empty($recommendations['results'])): ?>
            <?php foreach (array_slice($recommendations['results'], 0, 4) as $movie): ?>
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
            <p>No recommendations found.</p>
        <?php endif; ?>
    </div>
</main>
<br><br><br><br>
<?php include 'includes/footer.php'; ?>
