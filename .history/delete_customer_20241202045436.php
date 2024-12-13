<?php
// Include the database connection
include("php/catering_db.php");

// Check if the delete_id parameter is set in the URL
if (isset($_GET['delete_id'])) {
    $customer_id = $_GET['delete_id'];

    // Prepare a statement to delete the customer based on the ID
    $stmt = $db->prepare("DELETE FROM users WHERE id = :customer_id");
    $stmt->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);

    // Execute the deletion query
    if ($stmt->execute()) {
        // If successful, redirect back to the previous page (staff_index.php or customers page)
        header("Location: staff_index.php"); // Change this to the correct page if needed
        exit();
    } else {
        // Handle any error that occurs during the deletion process
        echo "Error deleting customer.";
    }
} else {
    // If no ID is passed in the URL, redirect back to the customers page
    header("Location: staff_index.php");
    exit();
}
?>
