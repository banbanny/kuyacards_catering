<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: home.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cart</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container">
  <h2>Your Cart</h2>
  <table class="table">
    <thead>
      <tr>
        <th>Item</th>
        <th>Quantity</th>
        <th>Price</th>
        <th>Total</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody id="cart-items">
      <!-- Cart items will be injected here by JavaScript -->
    </tbody>
  </table>
  <p id="cart-total"></p>
  <button class="btn btn-primary" onclick="window.location.href='book.php';">Proceed to Payment</button>
</div>

<script>
  // Function to render the cart items and display them in the table
  function renderCart() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const cartItemsContainer = document.getElementById('cart-items');
    let total = 0;

    cartItemsContainer.innerHTML = ''; // Clear existing items before rendering new ones

    if (cart.length === 0) {
      cartItemsContainer.innerHTML = '<tr><td colspan="5">Your cart is empty.</td></tr>';
      document.getElementById('cart-total').textContent = 'Total: ₱0';
      return;
    }

    cart.forEach((item, index) => {
      const itemTotal = item.price * item.quantity;
      total += itemTotal;

      cartItemsContainer.innerHTML += `
        <tr>
          <td>${item.name}</td>
          <td>
            <button class="btn btn-secondary btn-sm" onclick="updateQuantity(${index}, -1)">-</button>
            <span>${item.quantity}</span>
            <button class="btn btn-secondary btn-sm" onclick="updateQuantity(${index}, 1)">+</button>
          </td>
          <td>₱${item.price}</td>
          <td>₱${itemTotal}</td>
          <td>
            <button class="btn btn-danger btn-sm" onclick="removeFromCart(${index})">Remove</button>
          </td>
        </tr>
      `;
    });

    document.getElementById('cart-total').textContent = 'Total: ₱' + total;
  }

  // Function to update item quantity
  function updateQuantity(index, change) {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];

    cart[index].quantity += change;
    if (cart[index].quantity <= 0) {
      cart.splice(index, 1); // Remove the item if quantity is 0
    }

    localStorage.setItem('cart', JSON.stringify(cart));
    renderCart(); // Re-render the cart with updated quantities
  }

  // Function to remove an item from the cart
  function removeFromCart(index) {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];

    cart.splice(index, 1); // Remove the item at the given index
    localStorage.setItem('cart', JSON.stringify(cart));
    renderCart(); // Re-render the cart after removal
  }

  // Load cart items on page load
  document.addEventListener('DOMContentLoaded', function() {
    renderCart();
  });

  // Dummy function for proceeding to payment
  function proceedToPayment() {
    alert("Proceeding to payment...");
  }
</script>

</html>
