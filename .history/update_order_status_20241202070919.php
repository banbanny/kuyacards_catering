<?php
// Include the database connection
include("php/catering_db.php");

// Check if the request contains the necessary parameters
if (isset($_POST['order_id']) && isset($_POST['status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    // Prepare the UPDATE query to modify the status of the order
    $stmt = $db->prepare("UPDATE pending_orders SET status = :status WHERE id = :order_id");
    $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
    $stmt->bindParam(':status', $status, PDO::PARAM_STR);

    // Execute the query and check if the update was successful
    if ($stmt->execute()) {
        echo "Order status updated successfully.";
    } else {
        echo "Error updating order status.";
    }
}

if (isset($_POST['order_id']) && isset($_POST['delivery_status'])) {
    $order_id = $_POST['order_id'];
    $delivery_status = $_POST['delivery_status'];

    // Prepare the UPDATE query to modify the delivery status of the order
    $stmt = $db->prepare("UPDATE pendgin_orders SET delivery_status = :delivery_status WHERE id = :order_id");
    $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
    $stmt->bindParam(':delivery_status', $delivery_status, PDO::PARAM_STR);

    // Execute the query and check if the update was successful
    if ($stmt->execute()) {
        echo "Delivery status updated successfully.";
    } else {
        echo "Error updating delivery status.";
    }
}
?>
