<?php

require_once('php/catering_db.php');

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: home.php"); // Redirect to home if already logged in
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['login'])) {
        // Sanitize email and password
        $email = htmlspecialchars(strip_tags($_POST['email']));
        $password = $_POST['password'];

        // Check if both email and password are provided
        if (empty($email) || empty($password)) {
            $login_error = "Please enter both email and password.";
            error_log("Login error: Both email and password are required.");
        } else {
            // Fetch user data from the database
            try {
                $sql = "SELECT * FROM users WHERE email = ?";
                $stmt = $db->prepare($sql);
                $stmt->execute([$email]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user) {
                    // Direct password comparison (since we are storing plain text passwords)
                    if ($password === $user['password']) {
                        // Password matches, set session and redirect
                        $_SESSION['user_id'] = $user['user_id'];  
                        $_SESSION['email'] = $user['email'];      
                        header("Location: index.php");  
                        exit();
                    } else {
                        $login_error = "Invalid Email or Password!";
                        error_log("Login failed: Incorrect password for email: " . $email);
                    }
                } else {
                    $login_error = "Invalid Email or Password!";
                    error_log("Login failed: No user found with email: " . $email);
                }
            } catch (PDOException $e) {
                error_log("Database error: " . $e->getMessage());  // Log database-related errors
                $login_error = "There was an error with the database. Please try again.";
            }
        }
    }

    if (isset($_POST['signup'])) {
        // Check if email and password are provided
        if (!empty($_POST['email']) && !empty($_POST['password'])) {
            $email = htmlspecialchars(strip_tags($_POST['email']));
            $password = htmlspecialchars(strip_tags($_POST['password']));
    
            // Insert plain text password (no hashing)
            $sql = "INSERT INTO users (email, password) VALUES (?, ?)";
            $stmtinsert = $db->prepare($sql);
    
            try {
                $result = $stmtinsert->execute([$email, $password]); // Don't hash the password
                if ($result) {
                    header("Location: home.php?signup=success"); // Redirect to login form
                    exit();
                } else {
                    echo '<div class="alert alert-danger">Signup failed. Please try again.</div>';
                }
            } catch (PDOException $e) {
                echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
            }
        } else {
            echo '<div class="alert alert-danger">Both email and password are required.</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login / Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        .form-container {
            display: none;
        }

        .form-container.active {
            display: block;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Kuya Card's Catering</a>
    </div>
</nav>

<div class="container mt-5 d-flex justify-content-center">
    <div class="card shadow-lg" style="width: 28rem; border-radius: 10px; overflow: hidden;">
        <div class="card-header text-center bg-light">
            <!-- Logo -->
            <img src="logo.png" alt="Kuya Card's Catering Logo" class="img-fluid my-3" style="height: 80px;">
            <!-- Company Name -->
            <h4 class="fw-bold mb-3">Kuya Card's Catering</h4>
        </div>

        <div class="card-body">
            <!-- Buttons for Login and Sign Up -->
            <div class="d-flex justify-content-around mb-4">
                <button class="btn btn-primary" id="showLogin">Login</button>
                <button class="btn btn-success" id="showSignup">Sign Up</button>
            </div>

            <!-- Login Form -->
            <form id="loginForm" class="form-container active" action="home.php" method="POST">
                <h5 class="text-center mb-3">Login</h5>
                <div class="mb-3">
                    <label for="login-email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="login-email" name="email" required />
                </div>
                <div class="mb-3">
                    <label for="login-password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="login-password" name="password" required />
                </div>
                <?php if (isset($login_error)) {
                    echo '<div class="alert alert-danger">' . $login_error . '</div>';
                } ?>
                <button type="submit" class="btn btn-primary w-100" name="login">Login</button>
            </form>

            <!-- Sign Up Form -->
            <form id="signupForm" class="form-container d-none" action="home.php" method="POST">
                <h5 class="text-center mb-3">Sign Up</h5>
                <div class="mb-3">
                    <label for="signup-email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="signup-email" name="email" required />
                </div>
                <div class="mb-3">
                    <label for="signup-password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="signup-password" name="password" required />
                </div>
                <button type="submit" class="btn btn-success w-100" name="signup">Sign Up</button>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript to Toggle Forms -->
<script>
    const loginForm = document.getElementById('loginForm');
    const signupForm = document.getElementById('signupForm');
    const showLogin = document.getElementById('showLogin');
    const showSignup = document.getElementById('showSignup');

    showLogin.addEventListener('click', () => {
        loginForm.classList.remove('d-none');
        signupForm.classList.add('d-none');
    });

    showSignup.addEventListener('click', () => {
        signupForm.classList.remove('d-none');
        loginForm.classList.add('d-none');
    });
</script>


<!-- Footer -->
<footer class="text-center mt-5">
    <p>&copy; 2024 Kuya Card's Catering</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const loginForm = document.getElementById('loginForm');
    const signupForm = document.getElementById('signupForm');
    const showLogin = document.getElementById('showLogin');
    const showSignup = document.getElementById('showSignup');

    showLogin.addEventListener('click', () => {
        loginForm.classList.add('active');
        signupForm.classList.remove('active');
        // Reset the login form fields
        document.getElementById('login-email').value = '';
        document.getElementById('login-password').value = '';
    });

    showSignup.addEventListener('click', () => {
        signupForm.classList.add('active');
        loginForm.classList.remove('active');
        // Reset the signup form fields
        document.getElementById('signup-email').value = '';
        document.getElementById('signup-password').value = '';
    });
</script>

</body>
</html>
