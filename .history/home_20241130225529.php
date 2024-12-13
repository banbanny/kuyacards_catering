<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once('php/catering_db.php');

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    // Redirect to homepage or dashboard if already logged in
    header("Location: .php");
    exit();
}

// Function to add item to cart session
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['package_name'])) {
    $packageName = $_POST['package_name'];
    $packagePrice = $_POST['price'];
    
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Add package to session cart
    $_SESSION['cart'][] = ['name' => $packageName, 'price' => $packagePrice];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous" />
    <link rel="stylesheet" href="style.css">
    <title>Kuya Card's Catering</title>
</head>
<body>
    <header id="header" class="menu-container">
        <div class="logo-box">
            <svg id="header-img" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 477.867 477.867" style="enable-background:new 0 0 477.867 477.867;" xml:space="preserve">
                <g><g></g></g>
            </svg>
        </div>

        <!-- Navbar -->
        <nav id="nav-bar">
            <input class="menu-btn" type="checkbox" id="menu-btn" />
            <label class="menu-icon" for="menu-btn"><span class="nav-icon"></span></label>
            <ul class="menu">
                <li><a href="#">HOME</a></li>
                <li><a href="#about" class="nav-link">ABOUT US</a></li>
                <li><a href="#features" class="nav-link">CONTACT US</a></li>
                <li><a href="#pricing" class="nav-link">ORDER</a></li>
                <li>
                    <a href="cart.php" class="nav-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-cart">
                            <circle cx="9" cy="21" r="1"></circle>
                            <circle cx="20" cy="21" r="1"></circle>
                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61l1.18-7.39H6"></path>
                        </svg>
                        <span id="cart-count">
                            <?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>
                        </span> <!-- Cart count span -->
                    </a>
                </li>
                <?php if (isset($_SESSION['email'])): ?>
                    <!-- If the user is logged in, display their name -->
                    <li><span class="nav-link">Hello, <?php echo htmlspecialchars($_SESSION['email']); ?></span></li>
                    <li><a href="logout.php" class="nav-link">LOG OUT</a></li>
                <?php else: ?>
                    <!-- If the user is not logged in, show login/signup -->
                    <li><a href="home.php" class="nav-link">LOG IN</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <!-- Header ends -->

    <main class="container">
        <section class="hero container">
            <h1 class="hero-title-primary">Kuya Card's</h1>
            <p class="hero-title-sub">Home of Delicacies</p>
            <a href="#pricing">
                <button>Order Now</button>
            </a>
        </section>
    </main>

    <div class="wave">
    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 24 150 28" preserveAspectRatio="none" shape-rendering="auto">
        <defs>
            <linearGradient id="wave-gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" style="stop-color: lightblue; stop-opacity: 1" />
                <stop offset="100%" style="stop-color: darkblue; stop-opacity: 1" />
            </linearGradient>
            <path id="gentle-wave" d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z" />
        </defs>
        <g class="parallax">
        <use xlink:href="#gentle-wave" x="48" y="0" fill="url(#wave-gradient)" />
                <use xlink:href="#gentle-wave" x="48" y="3" fill="rgba(255,255,255,0.5)" />
                <use xlink:href="#gentle-wave" x="48" y="5" fill="rgba(255,255,255,0.3)" />
                <use xlink:href="#gentle-wave" x="48" y="7" fill="#fff" />
        </g>
    </svg>
</div>

    <section class="content">
        <div id="about" class="inner-content">
            <div class="inner-content-text content__TextLeft">
                <div class="inter-content-subtitle">About</div>
                <div class="inner-content-title">Kuya Card's Food and Catering Services</div>
                <div class="inner-content-para">
                    <p>We offer different food variants depending on your taste. We believe that everyone deserves a meal made with care and passion.</p>
                </div>
            </div>
            <div class="inner-image-container container__ImageRight">
                <img class="section-images" src="pics/1.png" style="object-position: 50% 50%;">
            </div>
        </div>

        <div id="features" class="inner-content">
            <div class="inner-content-text content__TextRight">
                <div class="inter-content-subtitle">Contact Us</div>
                <div class="inner-content-para">
                    <p>
                        <a href="https://www.facebook.com/kuyacardsdavao" target="_blank">Visit our Facebook page</a>
                    </p>
                    <p>
                        TM: 0975-5472-424<br>
                        Smart: 0919-7440-424<br>
                        Globe: 0995-6558-716
                    </p>
                </div>
            </div>
            <div class="inner-image-container container__ImageLeft">
                <img class="section-images" src="pics/slide3.jpg" style="object-position: 50% 50%;">
            </div>
        </div>
    </section>

    <section id="pricing" class="pricing-container">
        <h2 class="text-center" style="font-weight: bold;">ORDER HERE</h2>
        <div class="flex-container">
            <!-- Package A -->
            <div class="flex-item">
                <ul class="package">
                    <li class="header">PACKAGE A</li>
                    <li class="gray">&#8369;5,500<br><span class="month">Good For 10 Persons</span></li>
                    <li>Pancit Guisado</li>
                    <li>Chicken Afritada</li>
                    <li>Chicken Cordon Bleu</li>
                    <li>Buttered Chicken</li>
                    <li>Pork Menudo</li>
                    <form method="POST">
                        <input type="hidden" name="package_name" value="PACKAGE A">
                        <input type="hidden" name="price" value="5500">
                        <li><button type="submit">Add to Cart</button></li>
                    </form>
                </ul>
            </div>
            <!-- Package B -->
            <div class="flex-item">
                <ul class="package">
                    <li class="header">PACKAGE B</li>
                    <li class="gray">&#8369;6,400<br><span class="month">Good For 15 Persons</span></li>
                    <li>Bihon Guisado</li>
                    <li>Buttered Chicken</li>
                    <li>Chicken Cordon Bleu</li>
                    <li>Chopsuey</li>
                    <li>Salted Shrimp</li>
                    <form method="POST">
                        <input type="hidden" name="package_name" value="PACKAGE B">
                        <input type="hidden" name="price" value="6400">
                        <li><button type="submit">Add to Cart</button></li>
                    </form>
                </ul>
            </div>

            <!-- Package C -->
            <div class="flex-item">
                <ul class="package">
                    <li class="header">PACKAGE C</li>
                    <li class="gray">&#8369;8,050<br><span class="month">Good For 20 Persons</span></li>
                    <li>Salted Shrimp</li>
                    <li>Chopsuey</li>
                    <li>Kinilaw/Sinuglaw</li>
                    <li>Bam-i Guisado</li>
                    <li>Camaron Rebosado</li>
                    <form method="POST">
                        <input type="hidden" name="package_name" value="PACKAGE C">
                        <input type="hidden" name="price" value="8050">
                        <li><button type="submit">Add to Cart</button></li>
                    </form>
                </ul>
            </div>
        </div>
    </section>

    <footer>
        <div class="footer-container">
            <div>Kuya Card's Catering - Serving Davao's finest!</div>
        </div>
    </footer>
</body>
</html>