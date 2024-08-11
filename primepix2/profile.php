<?php
session_start();
include 'db/db.php';
include 'includes/header.php';
include 'includes/tmdb.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $mysqli->prepare("SELECT username, email, created_at FROM users WHERE id = ?");
if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    
    if (!$user) {
        echo "Error: User data not found.";
        exit();
    }
    
    $user_name = htmlspecialchars($user['username']);
} else {
    echo "Error: " . $mysqli->error;
    exit();
}

$stmt = $mysqli->prepare("
    SELECT r.rating, r.comment, r.movie_id 
    FROM reviews r 
    WHERE r.user_id = ?
");
if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $reviews = $stmt->get_result();
    $stmt->close();
} else {
    echo "Error: " . $mysqli->error;
    exit();
}

$stmt = $mysqli->prepare("
    SELECT movie_id 
    FROM favorites 
    WHERE user_id = ?
");
if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $favorites_result = $stmt->get_result();
    $favorite_ids = [];
    while ($row = $favorites_result->fetch_assoc()) {
        $favorite_ids[] = $row['movie_id'];
    }
    $stmt->close();
} else {
    echo "Error: " . $mysqli->error;
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update_profile'])) {
        $new_username = $_POST['username'];
        $new_email = $_POST['email'];
        $new_password = $_POST['password'];

        $query = "UPDATE users SET username = ?, email = ?" . ($new_password ? ", password = ?" : "") . " WHERE id = ?";
        $stmt = $mysqli->prepare($query);

        if ($new_password) {
            $new_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt->bind_param("sssi", $new_username, $new_email, $new_password, $user_id);
        } else {
            $stmt->bind_param("ssi", $new_username, $new_email, $user_id);
        }

        if ($stmt->execute()) {
            $_SESSION['user_name'] = $new_username; 
            header("Location: profile.php"); 
            exit();
        } else {
            echo "<p class='text-danger'>Error updating profile: " . $stmt->error . "</p>";
        }
        $stmt->close();
    }
}
?>

<main class="container mt-4">

<div class="profile-header mb-4">
    <h1>Welcome Back, <?php echo htmlspecialchars($user_name); ?>!</h1>
</div>

<div class="profile-info mb-4">
<h2>User Profile</h2><br>
    <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
    <p><strong>Member Since:</strong> <?php echo htmlspecialchars($user['created_at']); ?></p>
</div><br>

<h3>Your Favorite Movies</h3><br>
<div id="profile-favorites" class="container">
    <?php if (!empty($favorite_ids)): ?>
        <div class="row">
            <?php foreach ($favorite_ids as $movie_id): ?>
                <?php
                $url = "https://api.themoviedb.org/3/movie/$movie_id?api_key=0b9c876cee070f7b42383c077cd84d41";
                $response = file_get_contents($url);
                if ($response === FALSE) {
                    echo '<p class="text-danger">Failed to fetch movie details for ID: ' . htmlspecialchars($movie_id) . '</p>';
                    continue;
                }
                $movie = json_decode($response, true);
                ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <img src="https://image.tmdb.org/t/p/w500/<?php echo htmlspecialchars($movie['poster_path']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($movie['title']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($movie['title']); ?> (<?php echo htmlspecialchars($movie['release_date']); ?>)</h5>
                            <p class="card-text"><?php echo htmlspecialchars($movie['overview']); ?></p>
                            <a href="movie.php?id=<?php echo htmlspecialchars($movie['id']); ?>" class="btn btn-primary">View Movie</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-muted">You have no favorite movies yet.</p>
    <?php endif; ?>
</div>

<br><br>

<h3>Your Reviews</h3>
<div id="profile-reviews">
    <?php if ($reviews->num_rows > 0): ?>
        <ul class="list-unstyled">
            <?php while ($review = $reviews->fetch_assoc()): ?>
                <?php
                $movie = fetchFromTMDB("movie/" . $review['movie_id']);
                ?>
                <li class="media mb-4">
                    <img src="https://image.tmdb.org/t/p/w200/<?php echo htmlspecialchars($movie['poster_path']); ?>" class="mr-3" alt="<?php echo htmlspecialchars($movie['title']); ?>">
                    <div class="media-body">
                        <h5 class="mt-0 mb-1"><?php echo htmlspecialchars($movie['title']); ?> (<?php echo htmlspecialchars($movie['release_date']); ?>)</h5>
                        <div class="rating-stars">
                            <?php
                            $rating = (int) $review['rating'];
                            for ($i = 1; $i <= 10; $i++): ?>
                                <span class="star"><?php echo ($i <= $rating) ? '★' : '☆'; ?></span>
                            <?php endfor; ?>
                        </div>
                        <p><?php echo htmlspecialchars($review['comment']); ?></p>
                    </div>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>You have not reviewed any movies yet.</p>
    <?php endif; ?>
</div>
<br>

<h3>Update Profile Details</h3><br>
<form method="POST" action="" class="form-horizontal">
    <div class="form-group">
        <label for="username" class="form-label">New Username</label>
        <input type="text" id="username" name="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
    </div>
    <div class="form-group">
        <label for="email" class="form-label">New Email</label>
        <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
    </div>
    <div class="form-group">
        <label for="password" class="form-label">New Password (leave blank if not changing)</label>
        <input type="password" id="password" name="password" class="form-control">
    </div>
    <button type="submit" name="update_profile" class="btn btn-primary btn-lg">Update Profile</button>
</form>
<br><br><br>
</main>

<?php include 'includes/footer.php'; ?>
