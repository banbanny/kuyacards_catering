<?php
// Include database connection
include("php/catering_db.php");

// Initialize message variable
$message = '';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $package_name = $_POST['package_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $items = $_POST['items'];

    // Insert the new package into the database
    $stmt = $db->prepare("INSERT INTO packages (package_name, description, price, items) VALUES (:package_name, :description, :price, :items)");
    $stmt->bindParam(':package_name', $package_name, PDO::PARAM_STR);
    $stmt->bindParam(':description', $description, PDO::PARAM_STR);
    $stmt->bindParam(':price', $price, PDO::PARAM_STR);
    $stmt->bindParam(':items', $items, PDO::PARAM_STR);

    if ($stmt->execute()) {
        $message = '<div class="alert alert-success" role="alert">Package added successfully!</div>';
    } else {
        $message = '<div class="alert alert-danger" role="alert">Error adding package. Please try again.</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Package</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center">Add a New Package</h1>
    <?php if ($message) echo $message; ?>
    <form method="POST" action="add_package.php" class="mt-4">
        <div class="mb-3">
            <label for="package_name" class="form-label">Package Name</label>
            <input type="text" class="form-control" id="package_name" name="package_name" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">Price</label>
            <input type="number" step="0.01" class="form-control" id="price" name="price" required>
        </div>
        <div class="mb-3">
            <label for="items" class="form-label">Items (Use <code>&lt;li&gt;</code> for each item)</label>
            <textarea class="form-control" id="items" name="items" rows="3" placeholder="<li>Item 1</li><li>Item 2</li>" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Add Package</button>
        <a href="packages_list.php" class="btn btn-secondary">Back to Packages</a>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
