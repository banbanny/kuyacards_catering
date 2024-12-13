<?php
// Include the database connection
include("php/catering_db.php");

// Check if the delete_id is set in the URL
if (isset($_GET['delete_id'])) {
    $package_id = $_GET['delete_id'];

    // Prepare the DELETE query to remove the package from the database
    $stmt = $db->prepare("DELETE FROM packages WHERE id = :package_id");
    $stmt->bindParam(':package_id', $package_id, PDO::PARAM_INT);

    // Execute the query and check if the deletion was successful
    if ($stmt->execute()) {
        // If the deletion is successful, redirect to the previous page
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit(); // Ensure no further code is executed after redirection
    } else {
        // If there was an error deleting, show an error message
        echo "Error deleting package.";
    }
} else {
    // If no delete_id is passed in the URL, show an error message
    echo "Invalid package ID.";
}
?>
