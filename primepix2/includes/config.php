<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'primepix');

$mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if($mysqli === false){
    die("ERROR: Could not connect. " . $mysqli->connect_error);
}

define('TMDB_API_KEY', '0b9c876cee070f7b42383c077cd84d41');
define('TMDB_API_READ_ACCESS_TOKEN', 'eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiIwYjljODc2Y2VlMDcwZjdiNDIzODNjMDc3Y2Q4NGQ0MSIsIm5iZiI6MTcyMzAyNDEzNC4zMDQwNTUsInN1YiI6IjY2YjM0MTJlNjRiMWIyNWNhZjJiMTNjYSIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.2RusuTLGWmmzmEIatQb8UkiQNfsexi0IaF2vEZahl3c');
?>
