<?php
    include("php/catering_db.php");

    // Query to fetch customer details
    $stmt = $db->prepare("SELECT id, email, full_name, address, contact_number, role FROM users");
    $stmt->execute();
    $customer_results = $stmt->fetchAll(PDO::FETCH_ASSOC);

      // Fetch all customer data
      $customer_results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Query to fetch pending orders
    $stmt = $db->prepare("SELECT id, item_name, quantity, price, total, user_id, created_at, status FROM pending_orders");
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
