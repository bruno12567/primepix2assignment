<?php
require_once 'config.php';

function getTopMovies() {
    $url = "https://api.themoviedb.org/3/movie/top_rated?api_key=" . TMDB_API_KEY . "&language=en-US&page=1";
    return fetchData($url);
}

function getPopularMovies() {
    $url = "https://api.themoviedb.org/3/movie/popular?api_key=" . TMDB_API_KEY . "&language=en-US&page=1";
    return fetchData($url);
}

function getUpcomingMovies() {
    $url = "https://api.themoviedb.org/3/movie/upcoming?api_key=" . TMDB_API_KEY . "&language=en-US&page=1";
    return fetchData($url);
}

function getMoviesByCategory($category) {
    $category = urlencode($category);
    $url = "https://api.themoviedb.org/3/genre/$category/movies?api_key=" . TMDB_API_KEY . "&language=en-US";
    return fetchData($url);
}

function searchMovies($query) {
    $query = urlencode($query);
    $url = "https://api.themoviedb.org/3/search/movie?api_key=" . TMDB_API_KEY . "&query=" . $query . "&language=en-US&page=1";
    return fetchData($url);
}

function fetchFromTMDB($endpoint) {
    $api_key = TMDB_API_KEY; 
    $base_url = "https://api.themoviedb.org/3/";
    
    if (strpos($endpoint, '?') !== false) {
        $url = "$base_url$endpoint&api_key=$api_key";
    } else {
        $url = "$base_url$endpoint?api_key=$api_key";
    }

    $response = file_get_contents($url);
    if ($response === FALSE) {
        die('Error occurred while fetching data from TMDB.');
    }

    return json_decode($response, true);
}


function getGenreList() {
    $url = "https://api.themoviedb.org/3/genre/movie/list?api_key=" . TMDB_API_KEY . "&language=en-US";
    $response = file_get_contents($url);
    if ($response === FALSE) {
        return null; 
    }
    return json_decode($response, true);
}


function fetchData($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}
?>
