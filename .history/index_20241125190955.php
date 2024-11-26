<?php


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
          <li><a href="login_signup.php" class="nav-link">LOG IN</a></li>

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
          <li>LOG
        
        </ul>
      </nav>
      <!--   navbar -->
    </header>
    <!-- header ends -->
    
    <main class="container">
      <section class="hero container">
        <h1 class="hero-title-primary">Kuya Card's </h1>
        <p class="hero-title-sub">Home of Delicacies</p>
    
        <button onclick="window.location.href='login_signup.php';">Order Now</button>
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

    <!--Waves end-->
    <!--Header ends-->

    <!--Content starts-->
    <section class="content">
    
      <div id="about" class="inner-content">
        <div class="inner-content-text content__TextLeft">
          <div  class="inter-content-subtitle">
            About
          </div>
    
          <div class="inner-content-title">
            Kuya Card's Food and Catering Services
          </div>
    
          <div class="inner-content-para">
            <p> We offer a different food variants depending on your taste. We believe that everyone
              deserves a meal made with care and passion.
            </p>
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
          <div  class="inter-content-subtitle">
            Contact Us
          </div>
    
          <div class="inner-content-title">
         
          </div>
    
          <div class="inner-content-para">
                  <p>
            <span class="external-link">
              <a href="https://www.facebook.com/kuyacardsdavao" target="_blank">Visit our website</a>
            </span>
          </p>
              <p>
                TM: 0975-5472-424<br>
                Smart: 0919-7440-424<br>
                Globe: 0995-6558-716
              </p>
          </div>
        </div>
    
        <div class="inner-image-container container__ImageLeft">
          <div class="inner-content-image content__ImageLeft">
            <img class="section-images" src="pics/slide3.jpg" style="object-position: 50% 50%;">
          </div>
        </div>
      </div>
    
    </section>
    
    <section id="pricing" class="pricing-container">
      <div  class="pricing-title">
        <h2>ORDER HERE</h2>
      </div>
      <div class="flex-container">
      <div class="flex-item">
        <ul class="package">
          <li class="header">PACKAGE A</li>  
          <li class="gray"><sup class="dolla">&#8369;</sup>5,500<sup class="dolla"></sup><br><span class="month">Good For 10 Persons<span></li>
          <li class="features first-feat">Pancit Guisado</li>
          <li class="features">Chicken Afritada</li>
          <li class="features">Chicken Cordon Bleu</li>
          <li class="features">Buttered Chicken</li>
          <li class="features">Pork Menudo</li>
          <li>
          <button onclick="addToCart('PACKAGE A', 5500)">Add to Cart</button></li>
            
          </li>
        </ul>
      </div>
      <div class="flex-item">
        <ul class="package">
          <li class="header">PACKAGE B</li>
          <li class="gray">
            <sup class="dolla">&#8369;</sup>6,400<br><span class="month">Good For 15 Persons</span>
          </li>
          <li class="features first-feat">Bihon Guisado</li>
          <li class="features">Buttered Chicken</li>
          <li class="features">Chicken Cordon Bleu</li>
          <li class="features">Chopsuey</li>
          <li class="features">Salted Shrimp</li>
          <li>
          <button onclick="addToCart('PACKAGE B', 6400)">Add to Cart</button>
          </li>
        </ul>
      </div>

      <div class="flex-item">
       <ul class="package">
          <li class="header">PACKAGE C <br></li>
          <li class="gray"><sup class="dolla">&#8369;</sup>8,050<br><span class="month">Good For 20 Persons<span></li>
          <li class="features first-feat">Salted Shrimp</li>
          <li class="features">Chopsuey </li>
          <li class="features">Kinilaw/Sinuglaw</li>
          <li class="features">Bam-i Guisado</li>
          <li class="features">Camaron Rebosado</li>
          <li>
          <button onclick="addToCart('PACKAGE C', 8050)">Add to Cart</button></li>
            
          </li>
        </ul>
      </div>
    </div>
    </section>
    <script
      src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
      integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3"
      crossorigin="anonymous"
    ></script>
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"
      integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V"
      crossorigin="anonymous"
    ></script>
    <script>
  // Function to add items to cart
  function addToCart(packageName, price) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    const itemIndex = cart.findIndex(item => item.name === packageName);

    if (itemIndex > -1) {
      cart[itemIndex].quantity += 1; // If item exists, increase quantity
    } else {
      cart.push({ name: packageName, price: price, quantity: 1 });
    }

    localStorage.setItem('cart', JSON.stringify(cart));
    alert(${packageName} added to cart!);
  }

  // Function to proceed to cart page
  function goToCart() {
    window.location.href = 'cart.php';
  }
</script>
<script>
  // Load cart count from localStorage when the page loads
  document.addEventListener('DOMContentLoaded', function() {
    updateCartCount(); // Initialize the cart count on page load
  });

  // Function to add items to the cart
  function addToCart(name, price) {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    
    // Check if the item is already in the cart
    const existingItemIndex = cart.findIndex(item => item.name === name);
  
    if (existingItemIndex !== -1) {
      // If the item is already in the cart, increase its quantity
      cart[existingItemIndex].quantity += 1;
    } else {
      // If the item is not in the cart, add it with quantity 1
      cart.push({ name, price, quantity: 1 });
    }
    
    // Save the updated cart to localStorage
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartCount(); // Update cart count after adding an item
  }

  // Function to update cart count in the navigation
  function updateCartCount() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const totalItems = cart.reduce((total, item) => total + item.quantity, 0); // Sum all quantities
    document.getElementById('cart-count').textContent = totalItems; // Update the cart count span
  }

  // Function to update item quantity in the cart
  function updateQuantity(index, change) {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];

    if (cart[index].quantity + change > 0) {
      cart[index].quantity += change;
    }

    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartCount(); // Update cart count after quantity change
    renderCart(); // Call to refresh the cart display, if implemented
  }

  // Function to remove item from the cart
  function removeFromCart(index) {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];

    cart.splice(index, 1); // Remove the item
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartCount(); // Update cart count after item removal
    renderCart(); // Call to refresh the cart display, if implemented
  }

  // Placeholder for renderCart function, implement it if needed to display the cart contents
  function renderCart() {
    // Example: code here to dynamically render the cart items on the page
  }
</script>

  </body>
</html>