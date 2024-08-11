<?php
include 'includes/header.php';
include 'includes/tmdb.php';

if (!isset($_GET['id'])) {
    echo 'Movie ID not provided';
    exit;
}

$movie_id = $_GET['id'];
$movie = fetchFromTMDB('/movie/' . $movie_id);
?>

<main>
    <h2><?php echo $movie['title']; ?></h2>
    <p><?php echo $movie['overview']; ?></p>
    <h3>Reviews</h3>
    <div id="reviews">
    </div>
    <h3>Add Your Review</h3>
    <form id="review-form">
        <input type="hidden" id="movie_id" value="<?php echo $movie_id; ?>">
        <label for="rating">Rating:</label>
        <input type="number" id="rating" name="rating" min="1" max="10" required>
        <br>
        <label for="comment">Comment:</label>
        <textarea id="comment" name="comment" required></textarea>
        <br>
        <button type="submit">Submit</button>
    </form>
</main>

<?php include 'includes/footer.php'; ?>
