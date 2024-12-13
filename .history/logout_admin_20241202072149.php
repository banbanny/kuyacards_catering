<?php
// Start the session to access session variables
session_start();

// Check if a session exists and unset all session variables
 {
    // Uset session variables
    session_unset();
    
    // Destroy the session
    session_destroy();
}

// Redirect the user back to the login page or home page after logging out
header("Location: index.php"); // Replace login.php with your login page URL
exit();
?>
