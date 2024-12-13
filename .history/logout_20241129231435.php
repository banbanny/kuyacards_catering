<?php
session_start();
session_unset();
session_destroy(); // Destroy the session to log the user out
header("Location: index.php"); // Redirect to home page after logging out
exit();
?>
<?php
session_start(); // Start the session if it's not already started

// Clear all session variables
session_unset();

// Destroy the session completely
session_destroy();

// Clear the session cookie (optional but recommended for security)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Redirect the user to the home page or login page after logout
header("Location: index.php");
exit();
?>
