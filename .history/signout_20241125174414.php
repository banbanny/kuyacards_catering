<?php
session_start();
session_unset(); // Remove all session variables
session_destroy(); // Destroy the session
header("Location: login_signup.php"); // Redirect to login page
exit();
?>
