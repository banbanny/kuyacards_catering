conn.php

<?php
// Start the session (if you plan to use sessions)
session_start();

// Database connection details
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "catering_db";

// Attempt to connect to MySQL
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Check connection
if(!$conn) {
    die("Connection failed: " . mysqli_connect_error()); // More detailed error message
}

// Function to check if user is logged in
function check_login($conn) {
    if(isset($_SESSION['userID'])) {
        $id = $_SESSION['userID'];
        $query = "SELECT * FROM users WHERE userID = '$id' LIMIT 1";
        $result = mysqli_query($conn, $query);
        
        if($result && mysqli_num_rows($result) > 0) {
            $user_data = mysqli_fetch_assoc($result);
            return $user_data;
        } else {
            // User not found
            return null;
        }
    } else {
        // Redirect to login if session is not set
        header("Location: index.php");
        die;
    }
}
?>