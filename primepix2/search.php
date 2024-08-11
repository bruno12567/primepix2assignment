<?php
session_start(); 

$isLoggedIn = isset($_SESSION['user_id']);
?>


<?php
include 'includes/tmdb.php';
include 'includes/header.php';


$search_results = [];
$search_query = '';

if (isset($_GET['query'])) {
    $search_query = trim($_GET['query']);

    if (!empty($search_query)) {
        $search_results = searchMovies($search_query);
        $search_results = isset($search_results['results']) ? $search_results['results'] : [];
    }
}
?>

<!DOCTYPE html>        
<main class="container mt-4">
    <h2>Search Results</h2><br>
    <div class="row">
        <?php if (!empty($search_results)): ?>
            <?php foreach ($search_results as $movie): ?>
                <div class="col-md-3 mb-4">
                    <div class="card">
                        <?php if (!empty($movie['poster_path'])): ?>
                            <img src="https://image.tmdb.org/t/p/w500<?php echo htmlspecialchars($movie['poster_path']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($movie['title']); ?>">
                        <?php else: ?>
                            <img src="https://via.placeholder.com/500x750?text=No+Image" class="card-img-top" alt="No image available">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($movie['title']); ?></h5>
                            <p class="card-text"><strong>Rating:</strong> <?php echo htmlspecialchars($movie['vote_average']); ?>/10</p>
                            <p class="card-text"><?php echo htmlspecialchars($movie['overview']); ?></p>
                            <a href="movie.php?id=<?php echo htmlspecialchars($movie['id']); ?>" class="btn btn-primary">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No movies found matching your query.</p>
        <?php endif; ?>
    </div>
</main>
<br><br><br><br>

<?php include 'includes/footer.php'; ?>


    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>
