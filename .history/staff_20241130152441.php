<?php
// Include your database connection file
include("php/catering_db.php"); // Assuming catering_db.php is in the "php" folder

session_start(); // Make sure to start the session if you're using sessions

if (isset($_POST['login'])) {
    $email = $_POST['email']; // No need to escape input when using prepared statements
    $password = $_POST['password'];

    // Query to check the staff credentials
    $query = "SELECT id FROM staff WHERE staff_email = :email AND staff_password = :password";

    try {
        // Prepare and execute the query
        $stmt = $db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->execute();

        // Check if the user exists
        if ($stmt->rowCount() > 0) {
            // Staff exists
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $_SESSION['staff_id'] = $row["id"]; // Store staff ID in session
            header("Location: staff_index.php"); // Redirect to staff page
            exit(); // Ensure no further code is executed after redirect
        } else {
            echo '<script>alert("Invalid email or password!");</script>';
        }
    } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
    }
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
      integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65"
      crossorigin="anonymous"
    />
    <link rel="stylesheet" href="style.css">
    <title>Kuya Card's Catering</title>
</head>
<body>
    <header id="header" class="menu-container">
        <div class="logo-box">
            <svg id="header-img" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 477.867 477.867" style="enable-background:new 0 0 477.867 477.867;" xml:space="preserve"><g><g></g></g></svg>
        </div>
    </header>
    
    <main class="container">
        <section class="hero container">
            <h1 class="hero-title-primary">Kuya Card's Catering</h1>
        </section>
    </main>

    <div>
        <svg class="waves" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 24 150 28" preserveAspectRatio="none" shape-rendering="auto">
            <defs>
                <path id="gentle-wave" d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z" />
            </defs>
            <g class="parallax">
                <use xlink:href="#gentle-wave" x="48" y="0" fill="rgba(255,255,255,0.7)" />
                <use xlink:href="#gentle-wave" x="48" y="3" fill="rgba(255,255,255,0.5)" />
                <use xlink:href="#gentle-wave" x="48" y="5" fill="rgba(255,255,255,0.3)" />
                <use xlink:href="#gentle-wave" x="48" y="7" fill="#fff" />
            </g>
        </svg>
    </div>

    <section class="content d-flex justify-content-center">
        <div class="w-50">
            <br><br>
            <form method="post">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
                <button class="btn btn-danger" name="login">Login</button>
            </form>
            <br><br>
        </div>
    </section>
    
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
</body>
</html>
