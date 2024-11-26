<?php
session_start();
session_unset();
session_destroy(); // Destroy the session to log the user out
header("Location: logout.php"); // Redirect to home page after logging out
exit();
?>
