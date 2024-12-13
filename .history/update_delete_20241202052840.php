<?php
// Include the database connection
include("php/catering_db.php");

// Check if the delete_id or update_id parameter is set in the URL
if (isset($_GET['delete_id'])) {
    // Handle Delete
    $package_id = $_GET['delete_id'];

    // Prepare a statement to delete the package based on the ID
    $stmt = $db->prepare("DELETE FROM packages WHERE id = :package_id");
    $stmt->bindParam(':package_id', $package_id, PDO::PARAM_INT);

    // Execute the deletion query
    if ($stmt->execute()) {
        // If successful, redirect back to the previous page
        header("Location: staff_index.php"); // Change this to the correct page if needed
        exit();
    } else {
        // Handle any error that occurs during the deletion process
        echo "Error deleting package.";
    }
} elseif (isset($_GET['update_id'])) {
    // Handle Update
    $package_id = $_GET['update_id'];

    // You can either use a form or handle the update directly here
    // This example shows a simple update for demonstration purposes
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Get updated data from POST (assuming form fields)
        $new_name = $_POST['package_name'];
        $new_description = $_POST['description'];
        $new_price = $_POST['price'];

        // Prepare an update query
        $stmt = $db->prepare("UPDATE packages SET name = :name, description = :description, price = :price WHERE id = :package_id");
        $stmt->bindParam(':name', $new_name);
        $stmt->bindParam(':description', $new_description);
        $stmt->bindParam(':price', $new_price);
        $stmt->bindParam(':package_id', $package_id, PDO::PARAM_INT);

        // Execute the update query
        if ($stmt->execute()) {
            // If successful, redirect back to the previous page
            header("Location: staff_index.php"); // Change this to the correct page if needed
            exit();
        } else {
            echo "Error updating package.";
        }
    } else {
        // Show form to update the package details
        $stmt = $db->prepare("SELECT * FROM packages WHERE id = :package_id");
        $stmt->bindParam(':package_id', $package_id, PDO::PARAM_INT);
        $stmt->execute();
        $package = $stmt->fetch(PDO::FETCH_ASSOC);
    }
} else {
    // If no action is specified, redirect back to the previous page
    header("Location: staff_index.php");
    exit();
}
?>