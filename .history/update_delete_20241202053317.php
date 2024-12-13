<?php
// Include the database connection
include("php/catering_db.php");

// Check if an action (delete or update) has been requested
if (isset($_GET['delete_id'])) {
    // Handle Delete
    $package_id = $_GET['delete_id'];

    // Prepare a statement to delete the package based on the ID
    $stmt = $db->prepare("DELETE FROM packages WHERE id = :package_id");
    $stmt->bindParam(':package_id', $package_id, PDO::PARAM_INT);

    // Execute the deletion query
    if ($stmt->execute()) {
        // If successful, redirect back to the previous page
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    } else {
        echo "Error deleting package.";
    }
} elseif (isset($_GET['update_id'])) {
    // Handle Update
    $package_id = $_GET['update_id'];

    // Fetch the current package details to populate the form
    $stmt = $db->prepare("SELECT * FROM packages WHERE id = :package_id");
    $stmt->bindParam(':package_id', $package_id, PDO::PARAM_INT);
    $stmt->execute();
    $package = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if package exists
    if (!$package) {
        echo "Package not found.";
        exit();
    }

    // If the form is submitted to update the package
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Get updated data from POST (assuming form fields)
        $new_name = $_POST['package_name'];
        $new_description = $_POST['description'];
        $new_price = $_POST['price'];

        // Prepare an update query
        $update_stmt = $db->prepare("UPDATE packages SET name = :name, description = :description, price = :price WHERE id = :package_id");
        $update_stmt->bindParam(':name', $new_name);
        $update_stmt->bindParam(':description', $new_description);
        $update_stmt->bindParam(':price', $new_price);
        $update_stmt->bindParam(':package_id', $package_id, PDO::PARAM_INT);

        // Execute the update query
        if ($update_stmt->execute()) {
            // If successful, redirect back to the previous page
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        } else {
            echo "Error updating package.";
        }
    }
} else {
    // If no valid action is provided, redirect back to the previous page
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}
?>