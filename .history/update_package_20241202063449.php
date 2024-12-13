<?php
// Include the database connection
include("php/catering_db.php");

// Initialize message variable
$message = '';

$stmt1 = 

// Check if form data is posted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $package_id = $_GET['update_id'];
    $package_name = $_POST['package_name'];
    $package_description = $_POST['package_description'];
    $package_price = $_POST['package_price'];
    $package_items = $_POST['package_items'];

    // Prepare the UPDATE query to modify the package
    $stmt = $db->prepare("UPDATE packages SET package_name = :name, description = :description, price = :price, items = :items WHERE id = :id");
    $stmt->bindParam(':id', $package_id, PDO::PARAM_INT);
    $stmt->bindParam(':name', $package_name, PDO::PARAM_STR);
    $stmt->bindParam(':description', $package_description, PDO::PARAM_STR);
    $stmt->bindParam(':price', $package_price, PDO::PARAM_STR);
    $stmt->bindParam(':items', $package_items, PDO::PARAM_STR);

    // Execute the query and check if the update was successful
    if ($stmt->execute()) {
        // Success message and redirection
        $message = '<div class="alert alert-success" role="alert">Package updated successfully! You will be redirected.</div>';
        echo $message;
        header("Refresh:2; url=" . $_SERVER['HTTP_REFERER']); // Redirect after 2 seconds
        exit(); // Ensure no further code is executed after redirection
    } else {
        // Error message if update fails
        $message = '<div class="alert alert-danger" role="alert">Error updating package. Please try again.</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Package</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <!-- Form for updating package -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4>Update Package Details</h4>
                </div>
                <div class="card-body">
                    <!-- Display success or error message -->
                    <?php if ($message): ?>
                        <div class="mb-3">
                            <?php echo $message; ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Update form -->
                    <form method="POST">
                        <input type="hidden" name="package_id" value="<?php echo $package_id; ?>">

                        <!-- Package Name -->
                        <div class="mb-3">
                            <label for="package_name" class="form-label">Package Name</label>
                            <input type="text" class="form-control" id="package_name" name="package_name" value="" required>
                        </div>

                        <!-- Package Description -->
                        <div class="mb-3">
                            <label for="package_description" class="form-label">Package Description</label>
                            <textarea class="form-control" id="package_description" name="package_description" rows="3" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="package_items" class="form-label">Items</label>
                            <textarea class="form-control" id="package_items" name="package_items" rows="3" required></textarea>
                        </div>

                        <!-- Package Price -->
                        <div class="mb-3">
                            <label for="package_price" class="form-label">Package Price</label>
                            <input type="number" class="form-control" id="package_price" name="package_price" value="" required>
                        </div>

                        <button type="submit" class="btn btn-success w-100">Update Package</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
