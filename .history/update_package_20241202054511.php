<?php
// Include the database connection
include("php/catering_db.php");

// Check if form data is posted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $package_id = $_POST['package_id'];
    $package_name = $_POST['package_name'];
    $package_description = $_POST['package_description'];
    $package_price = $_POST['package_price'];

    // Prepare the UPDATE query to modify the package
    $stmt = $db->prepare("UPDATE packages SET name = :name, description = :description, price = :price WHERE id = :id");
    $stmt->bindParam(':id', $package_id, PDO::PARAM_INT);
    $stmt->bindParam(':name', $package_name, PDO::PARAM_STR);
    $stmt->bindParam(':description', $package_description, PDO::PARAM_STR);
    $stmt->bindParam(':price', $package_price, PDO::PARAM_STR);

    // Execute the query and check if the update was successful
    if ($stmt->execute()) {
        // If the update is successful, redirect to the previous page
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit(); // Ensure no further code is executed after redirection
    } else {
        // If there was an error updating, show an error message
        echo "Error updating package.";
    }
}
?>
