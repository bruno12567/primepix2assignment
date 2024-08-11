$(document).ready(function() {
    $('#review-form').submit(function(event) {
        event.preventDefault();
        var movie_id = $('#movie_id').val();
        var rating = $('#rating').val();
        var comment = $('#comment').val();

        $.post('ajax_add_comment.php', {
            movie_id: movie_id,
            rating: rating,
            comment: comment
        }, function(data) {
            if (data === 'Success') {
                loadReviews(movie_id);
                $('#rating').val('');
                $('#comment').val('');
            } else {
                alert('Error submitting review');
            }
        });
    });

    function loadReviews(movie_id) {
        $.get('ajax_load_reviews.php', { movie_id: movie_id }, function(data) {
            var reviews = JSON.parse(data);
            var html = '';
            reviews.forEach(function(review) {
                html += '<p><strong>' + review.username + ':</strong> ' + review.comment + ' (' + review.rating + '/10)</p>';
            });
            $('#reviews').html(html);
        });
    }

    var movie_id = $('#movie_id').val();
    if (movie_id) {
        loadReviews(movie_id);
    }
});


function addToFavorites(movieId) {
    $.ajax({
        type: "POST",
        url: "add_to_favorites.php",
        data: { movie_id: movieId },
        success: function(response) {
            var data = JSON.parse(response);
            if (data.status === 'success') {
                alert(data.message);  // Display success message
                // Optionally update the UI to reflect the addition
            } else {
                alert('An error occurred: ' + data.message);
            }
        },
        error: function(xhr, status, error) {
            alert('An error occurred while adding the movie to favorites.');
        }
    });
}
