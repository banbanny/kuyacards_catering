<?php
// Include the database connection
include("php/catering_db.php");

// Check if the request contains the necessary parameters
if (isset($_POST['order_id']) && isset($_POST['delivery_status'])) {
    $order_id = $_POST['order_id'];
    $delivery_status = $_POST['delivery_status'];

    // Prepare the UPDATE query to modify the delivery status of the order
    $stmt = $db->prepare("UPDATE orders SET delivery_status = :delivery_status WHERE pending_order_id = :order_id");
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
