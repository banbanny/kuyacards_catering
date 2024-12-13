<?php
require_once('config.php');

try {
    // Create a new PDO instance
    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    
    // Set PDO options for security and performance
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Enable exceptions on errors
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // Fetch data as associative arrays
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); // Use real prepared statements
} catch (PDOException $e) {
    // Log the error and show a generic message
    error_log("Database connection error: " . $e->getMessage());
    die('Database connection failed. Please try again later.');
}
?>