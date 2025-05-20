<?php

// Start the session
session_start();

// Database connection
include 'connection.php';

// Handle Logout
include 'admin-logout.php';

// Count users
$userResult = $conn->query("SELECT COUNT(id) AS user_count FROM users");
$userRow = $userResult->fetch_assoc();
$userCount = $userRow['user_count'];

// Count products
$productResult = $conn->query("SELECT COUNT(id) AS product_count FROM products");
$productRow = $productResult->fetch_assoc();
$productCount = $productRow['product_count'];

// Count pending orders
$pendingResult = $conn->query("SELECT COUNT(id) AS pending_count FROM product_status WHERE status = 'pending'");
$pendingRow = $pendingResult->fetch_assoc();
$pendingCount = $pendingRow['pending_count'];

// Count completed and delivered orders
$completedResult = $conn->query("SELECT COUNT(id) AS completed_count FROM product_status WHERE status IN ('completed', 'delivered')");
$completedRow = $completedResult->fetch_assoc();
$completedCount = $completedRow['completed_count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ShopAI - Admin - Statistics</title>

    <link href="https://fonts.googleapis.com/css?family=Poppins" rel="stylesheet">

    <link href="styles/style.css" rel="stylesheet">
    <link href="styles/navbar.css" rel="stylesheet">
    <link href="styles/admin-navbar.css" rel="stylesheet">
    <link href="styles/admin-statistics.css" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header class="admin-header">
        <div class="logo">ShopAI</div>
        <ul class="nav-links" id="nav-links">
            <li><a href="admin-statistics.php" class="active">Statistics</a></li>
            <li><a href="admin-status.php">Status</a></li>
            <li><a href="admin-products.php">Products</a></li>
        </ul>
        <form method="POST" action="admin-logout.php" style="display:inline;">
            <input type="hidden" name="action" value="logout">
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </header>

    <!-- Main Content -->
    <div class="admin-container">
        <h3>Statistics</h3>
        <div class="stats-container">
            <div class="stat-box">
                <?php echo $userCount; ?>
                <span class="stat-label">Total Users</span>
            </div>
            <div class="stat-box">
                <?php echo $pendingCount; ?>
                <span class="stat-label">Pending Orders</span>
            </div>
            <div class="stat-box">
                <?php echo $completedCount; ?>
                <span class="stat-label">Completed Orders</span>
            </div>
            <div class="stat-box">
                <?php echo $productCount; ?>
                <span class="stat-label">Total Products</span>
            </div>
        </div>
        </div>

    <script src="scripts/admin.js"></script>
</body>
</html>