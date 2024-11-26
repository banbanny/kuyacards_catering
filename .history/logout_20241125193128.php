<?php
session_start();
session_destroy(); // Destroy all session data
header('Location: login_signu.php'); // Redirect to the homepage after logout
exit();
?>
