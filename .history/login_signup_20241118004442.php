<?php
// login.php
session_start();

// Check if the user is already logged in, redirect to dashboard if so
if (isset($_SESSION['user'])) {
    header("Location: dashboard.php"); // Redirect to a dashboard or home page
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
      crossorigin="anonymous"
    />
    <link rel="stylesheet" href="style.css">
    <title>Login - Kuya Card's Catering</title>
  </head>
  <body>
    <header id="header">
      <div class="logo-box">
        <!-- Logo goes here -->
      </div>
      <nav id="nav-bar">
        <!-- Navbar code goes here -->
      </nav>
    </header>

    <main class="container my-5">
      <h2 class="text-center">Login to Kuya Card's Catering</h2>

      <?php
      if (isset($_SESSION['error'])) {
          echo '<div class="alert alert-danger">'. $_SESSION['error'] .'</div>';
          unset($_SESSION['error']);
      }
      ?>

      <form action="login_action.php" method="POST" class="w-50 mx-auto">
        <div class="mb-3">
          <label for="username" class="form-label">Username</label>
          <input type="text" class="form-control" id="username" name="username" required>
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" class="form-control" id="password" name="password" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Login</button>
      </form>

      <div class="mt-3 text-center">
        <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
      </div>
    </main>

    <footer class="footer text-center py-4">
      <p>&copy; 2024 Kuya Card's Catering</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
