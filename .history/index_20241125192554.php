<?php
// Start the session to check login status
session_start();

// Assuming 'user_id' is stored in session upon login
$is_logged_in = isset($_SESSION['user_id']);

// If the user is not logged in, redirect to login page
if (!$is_logged_in) {
    header('Location: home.php');
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
      integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65"
      crossorigin="anonymous"
    />
    <link rel="stylesheet" href="style.css">
    <title>Kuya Card's Catering</title>
  </head>
  <body>
    <header id="header" class="menu-container">
      <div class="logo-box">
        <svg id="header-img" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 477.867 477.867" style="enable-background:new 0 0 477.867 477.867;" xml:space="preserve"><g><g>
    </g></g> </svg>
      </div>

      <!--   navbar -->
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
              <span id="cart-count">0</span> <!-- Cart count span -->
            </a>
          </li>

          <li><a href="logout.php" class="nav-link">LOG OUT</a></li>
        </ul>
      </nav>
      <!--   navbar -->
    </header>
    <!-- header ends -->

    <main class="container">
      <section class="hero container">
        <h1 class="hero-title-primary">Kuya Card's </h1>
        <p class="hero-title-sub">Home of Delicacies</p>
    
        <button onclick="window.location.href='order_page.php';">Order Now</button>
      </section>
    </main>

    <div>
      <svg class="waves" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 24 150 28" preserveAspectRatio="none" shape-rendering="auto">
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

    <!-- Waves end -->
    <!-- Content starts -->
    <section class="content">
      <div id="about" class="inner-content">
        <div class="inner-content-text content__TextLeft">
          <div  class="inter-content-subtitle">About</div>
          <div class="inner-content-title">Kuya Card's Food and Catering Services</div>
          <div class="inner-content-para">
            <p> We offer a variety of food depending on your taste. We believe that everyone deserves a meal made with care and passion.</p>
          </div>
        </div>
    
        <div class="inner-image-container container__ImageRight">
          <div class="inner-content-image content__ImageRight">
            <img class="section-images" src="pics/1.png" style="object-position: 50% 50%;">
          </div>
        </div>
      </div>
    
      <div id="features" class="inner-content">
        <div class="inner-content-text content__TextRight">
          <div  class="inter-content-subtitle">Contact Us</div>
          <div class="inner-content-para">
            <p><span class="external-link"><a href="https://www.facebook.com/kuyacardsdavao" target="_blank">Visit our website</a></span></p>
            <p>TM: 0975-5472-424<br>Smart: 0919-7440-424<br>Globe: 0995-6558-716</p>
          </div>
        </div>
    
        <div class="inner-image-container container__ImageLeft">
          <div class="inner-content-image content__ImageLeft">
            <img class="section-images" src="pics/slide3.jpg" style="object-position: 50% 50%;">
          </div>
        </div>
      </div>
    </section>

    <!-- Pricing section (only visible to logged-in users) -->
    <section id="pricing" class="pricing-container">
      <div class="pricing-title"><h2>ORDER HERE</h2></div>
      <div class="flex-container">
        <!-- Package A -->
        <div class="flex-item">
          <ul class="package">
            <li class="header">PACKAGE A</li>  
            <li class="gray"><sup class="dolla">&#8369;</sup>5,500<sup class="dolla"></sup><br><span class="month">Good For 10 Persons</span></li>
            <li class="features first-feat">Pancit Guisado</li>
            <li class="features">Chicken Afritada</li>
            <li class="features">Chicken Cordon Bleu</li>
            <li class="features">Buttered Chicken</li>
            <li class="features">Pork Menudo</li>
            <li><button onclick="addToCart('PACKAGE A', 5500)">Add to Cart</button></li>
          </ul>
        </div>
        
        <!-- Other packages can be added similarly -->
      </div>
    </section>
  </body>
</html>
