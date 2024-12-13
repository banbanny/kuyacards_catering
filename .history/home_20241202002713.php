<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once('php/catering_db.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Check if the user is an admin
$isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;

// Handle package actions (add, edit, delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $packageId = isset($_POST['package_id']) ? $_POST['package_id'] : null;
        $packageName = isset($_POST['package_name']) ? $_POST['package_name'] : '';
        $packagePrice = isset($_POST['price']) ? $_POST['price'] : 0;
        $packageDescription = isset($_POST['description']) ? $_POST['description'] : '';

        if ($action === 'add' && $isAdmin) {
            $stmt = $pdo->prepare("INSERT INTO packages (name, description, price) VALUES (?, ?, ?)");
            $stmt->execute([$packageName, $packageDescription, $packagePrice]);
        } elseif ($action === 'edit' && $isAdmin && $packageId) {
            $stmt = $pdo->prepare("UPDATE packages SET name = ?, description = ?, price = ? WHERE id = ?");
            $stmt->execute([$packageName, $packageDescription, $packagePrice, $packageId]);
        } elseif ($action === 'delete' && $isAdmin && $packageId) {
            $stmt = $pdo->prepare("DELETE FROM packages WHERE id = ?");
            $stmt->execute([$packageId]);
        }

        // Redirect to prevent form resubmission
        header("Location: home.php");
        exit();
    }
}

// Fetch packages for display
$stmt = $pdo->prepare("SELECT * FROM packages");
$stmt->execute();
$packages = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="style.css">
    <title>Kuya Card's Catering</title>
</head>
<body>
    <header id="header" class="menu-container">
        <div class="logo-box">
            <svg id="header-img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 477.867 477.867"></svg>
        </div>
        <nav id="nav-bar">
            <input class="menu-btn" type="checkbox" id="menu-btn" />
            <label class="menu-icon" for="menu-btn"><span class="nav-icon"></span></label>
            <ul class="menu">
                <li><a href="#">HOME</a></li>
                <li><a href="#about" class="nav-link">ABOUT US</a></li>
                <li><a href="#features" class="nav-link">CONTACT US</a></li>
                <li><a href="#pricing" class="nav-link">ORDER</a></li>
                <li>
                    <a href="cart.php" class="nav-link">
                        <span id="cart-count">
                            <?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>
                        </span>
                    </a>
                </li>
                <?php if (isset($_SESSION['email'])): ?>
                    <li><span class="nav-link">Hello, <?php echo htmlspecialchars($_SESSION['email']); ?></span></li>
                    <li><a href="logout.php" class="nav-link">LOG OUT</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main class="container">
        <!-- Admin Package Management Section -->
        <?php if ($isAdmin): ?>
        <section id="admin-packages" class="my-5">
            <h2>Manage Packages</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Package Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($packages as $package): ?>
                    <tr>
                        <td><?php echo $package['id']; ?></td>
                        <td><?php echo htmlspecialchars($package['name']); ?></td>
                        <td><?php echo htmlspecialchars($package['description']); ?></td>
                        <td>&#8369;<?php echo number_format($package['price'], 2); ?></td>
                        <td>
                            <form method="POST" style="display: inline-block;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="package_id" value="<?php echo $package['id']; ?>">
                                <button class="btn btn-danger btn-sm" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <!-- Add Package Form -->
            <h3>Add New Package</h3>
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="mb-3">
                    <label for="package_name" class="form-label">Package Name</label>
                    <input type="text" class="form-control" id="package_name" name="package_name" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="price" class="form-label">Price</label>
                    <input type="number" class="form-control" id="price" name="price" step="0.01" required>
                </div>
                <button type="submit" class="btn btn-success">Add Package</button>
            </form>
        </section>
        <?php endif; ?>

        <!-- Customer Order Section -->
        <section id="pricing" class="pricing-container">
            <h2 class="text-center">ORDER HERE</h2>
            <div class="flex-container">
                <?php foreach ($packages as $package): ?>
                <div class="flex-item">
                    <ul class="package">
                        <li class="header"><?php echo htmlspecialchars($package['name']); ?></li>
                        <li class="gray">&#8369;<?php echo number_format($package['price'], 2); ?><br><span class="month">Good for Multiple Persons</span></li>
                        <li><?php echo htmlspecialchars($package['description']); ?></li>
                        <form method="POST">
                            <input type="hidden" name="package_name" value="<?php echo $package['name']; ?>">
                            <input type="hidden" name="price" value="<?php echo $package['price']; ?>">
                            <li><button type="submit">Add to Cart</button></li>
                        </form>
                    </ul>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>

    <footer>
        <div class="footer-container">Kuya Card's Catering - Serving Davao's finest!</div>
    </footer>
</body>
</html>
