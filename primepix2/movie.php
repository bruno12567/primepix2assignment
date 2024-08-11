<?php
session_start(); 
include 'includes/header.php';
include 'includes/tmdb.php';
include 'db/db.php';

if (!isset($_GET['id'])) {
    echo 'Movie ID not provided';
    exit;
}


$movie_id = intval($_GET['id']);
$movie = fetchFromTMDB('movie/' . $movie_id);
if (!$movie) {
    echo 'Failed to fetch movie details from TMDB.';
    exit;
}

$credits = fetchFromTMDB("movie/$movie_id/credits");
if (!$credits) {
    echo 'Failed to fetch movie credits from TMDB.';
    exit;
}

$videos = fetchFromTMDB("movie/$movie_id/videos");
if (!$videos) {
    echo 'Failed to fetch movie videos from TMDB.';
    exit;
}

$director = 'Not Available';
$writer = 'Not Available';
$trailer = null;

foreach ($credits['crew'] as $crew_member) {
    if ($crew_member['job'] === 'Director') {
        $director = $crew_member['name'];
    }
    if ($crew_member['job'] === 'Writer' || $crew_member['department'] === 'Writing') {
        $writer = $crew_member['name'];
    }
}

foreach ($videos['results'] as $video) {
    if ($video['type'] === 'Trailer' && $video['site'] === 'YouTube') {
        $trailer = $video['key'];
        break;
    }
}

$favorite = false;
if (isset($_SESSION['user_id'])) {
    $query = "SELECT COUNT(*) FROM favorites WHERE user_id = ? AND movie_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("ii", $_SESSION['user_id'], $movie_id);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    if ($count > 0) {
        $favorite = true;
    }
    $stmt->close();
}


$query = "SELECT r.rating, r.comment, u.username FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.movie_id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$reviews_result = $stmt->get_result();
$reviews = $reviews_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$average_rating = isset($movie['vote_average']) ? $movie['vote_average'] : 0;
$max_rating = 10; 
$star_count = 10; 

$filled_stars = floor($average_rating / ($max_rating / $star_count));
$half_star = ($average_rating / ($max_rating / $star_count)) - $filled_stars >= 0.5;

?>

<main class="container mt-4">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <div class="row">
        <div class="col-md-4">
            <img src="https://image.tmdb.org/t/p/w500<?php echo htmlspecialchars($movie['poster_path']); ?>" class="img-fluid" alt="<?php echo htmlspecialchars($movie['title']); ?>">
        </div>
        <div class="col-md-8">
            <h2><?php echo htmlspecialchars($movie['title']); ?></h2>
            <p><strong>Average Rating:</strong> 
            <span class="rating-stars">
                <?php for ($i = 0; $i < $star_count; $i++): ?>
                    <?php if ($i < $filled_stars): ?>
                        <i class="fas fa-star"></i>
                    <?php elseif ($half_star && $i == $filled_stars): ?>
                        <i class="fas fa-star-half-alt"></i>
                    <?php else: ?>
                        <i class="far fa-star"></i>
                    <?php endif; ?>
                <?php endfor; ?>
            </span>
                <?php echo htmlspecialchars(number_format($average_rating, 1)); ?>/10
            </p>
            <p><strong>Release Date:</strong> <?php echo htmlspecialchars($movie['release_date']); ?></p>
            <p><strong>Language:</strong> <?php echo htmlspecialchars($movie['original_language']); ?></p>
            <p><strong>Budget:</strong> $<?php echo number_format($movie['budget'], 2); ?></p>
            <p><strong>Director:</strong> <?php echo htmlspecialchars($director); ?></p>
            <p><strong>Writer:</strong> <?php echo htmlspecialchars($writer); ?></p>
            <p><strong>Overview:</strong> <?php echo htmlspecialchars($movie['overview']); ?></p>
            <br>
            <?php if ($favorite): ?>
                <button class="btn btn-danger" onclick="removeFromFavorites(<?php echo $movie_id; ?>)">Remove from Favorites</button>
            <?php else: ?>
                <button class="btn btn-primary" onclick="addToFavorites(<?php echo $movie_id; ?>)">Add to Favorites</button>
            <?php endif; ?>
            <br><br>
            <h3>Cast:</h3>
            <div class="row">
                <?php foreach (array_slice($credits['cast'], 0, 10) as $cast_member): ?>
                    <div class="col-md-2 mb-3">
                        <?php if (!empty($cast_member['profile_path'])): ?>
                            <img src="https://image.tmdb.org/t/p/w185<?php echo htmlspecialchars($cast_member['profile_path']); ?>" class="img-fluid" alt="<?php echo htmlspecialchars($cast_member['name']); ?>">
                        <?php else: ?>
                            <img src="https://via.placeholder.com/185x278?text=No+Image" class="img-fluid" alt="No image available">
                        <?php endif; ?>
                        <p><?php echo htmlspecialchars($cast_member['name']); ?><br><small><?php echo htmlspecialchars($cast_member['character']); ?></small></p>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($trailer): ?>
                <h3>Trailer:</h3>
                <iframe width="560" height="315" src="https://www.youtube.com/embed/<?php echo htmlspecialchars($trailer); ?>" frameborder="0" allowfullscreen></iframe>
            <?php else: ?>
                <p>Trailer not available.</p>
            <?php endif; ?>
        </div>
    </div><br><br>

    <h3>Reviews</h3>
    <div id="reviews">
        <?php if (count($reviews) > 0): ?>
            <ul>
                <?php foreach ($reviews as $review): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($review['username']); ?>:</strong>
                        <div class="rating-stars">
                            <?php
                            $rating = (int) $review['rating'];
                            for ($i = 1; $i <= 10; $i++): ?>
                                <span class="star"><?php echo ($i <= $rating) ? '★' : '☆'; ?></span>
                            <?php endfor; ?>
                        </div>
                        <p><?php echo htmlspecialchars($review['comment']); ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No reviews yet.</p>
        <?php endif; ?>
    </div>

    <h3>Add Your Review</h3>
    <form method="POST" action="submit_review.php">
        <input type="hidden" name="movie_id" value="<?php echo htmlspecialchars($movie_id); ?>">
        <label>Rating:</label>
        <div class="rating">
            <input type="radio" id="star10" name="rating" value="10"><label for="star10" title="10 stars"></label>
            <input type="radio" id="star9" name="rating" value="9"><label for="star9" title="9 stars"></label>
            <input type="radio" id="star8" name="rating" value="8"><label for="star8" title="8 stars"></label>
            <input type="radio" id="star7" name="rating" value="7"><label for="star7" title="7 stars"></label>
            <input type="radio" id="star6" name="rating" value="6"><label for="star6" title="6 stars"></label>
            <input type="radio" id="star5" name="rating" value="5"><label for="star5" title="5 stars"></label>
            <input type="radio" id="star4" name="rating" value="4"><label for="star4" title="4 stars"></label>
            <input type="radio" id="star3" name="rating" value="3"><label for="star3" title="3 stars"></label>
            <input type="radio" id="star2" name="rating" value="2"><label for="star2" title="2 stars"></label>
            <input type="radio" id="star1" name="rating" value="1"><label for="star1" title="1 star"></label>
        </div>
        <br><br>
        <label for="comment">Comment:</label><br>
        <textarea id="comment" name="comment" required></textarea>
        <br>
        <button type="submit">Submit Review</button>
    </form>
    <br><br><br><br><br><br>
</main>

<script>
function addToFavorites(movieId) {
    fetch('add_to_favorites.php', {
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
            alert(data.message);
            location.reload(); 
        } else {
            alert(data.message);
        }
    });
}

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
            alert(data.message);
            location.reload(); 
        } else {
            alert(data.message);
        }
    });
}
</script>


<?php include 'includes/footer.php'; ?>
