<?php

// Start the session
session_start();

// Database connection
include 'connection.php';

// Count users
$userResult = $conn->query("SELECT COUNT(id) AS user_count FROM users");
$userRow = $userResult->fetch_assoc();
$userCount = $userRow['user_count'];

// Count products
$productResult = $conn->query("SELECT COUNT(id) AS product_count FROM products");
$productRow = $productResult->fetch_assoc();
$productCount = $productRow['product_count'];

// Count pending orders
$pendingResult = $conn->query("SELECT COUNT(id) AS pending_count FROM pending_orders WHERE status = 'pending'");
$pendingRow = $pendingResult->fetch_assoc();
$pendingCount = $pendingRow['pending_count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Statistics</title>

    <link href="https://fonts.googleapis.com/css?family=Poppins" rel="stylesheet">

    <link href="styles/style.css" rel="stylesheet">
    <link href="styles/navbar.css" rel="stylesheet">
    <link href="styles/admin-navbar.css" rel="stylesheet">
    <link href="styles/admin-containers.css" rel="stylesheet">
    <style>
        .stats-container {
            margin: 60px auto 0 auto;
            max-width: 600px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 16px rgba(0,0,0,0.08);
            padding: 40px 30px;
            text-align: center;
        }
        .stat-box {
            margin: 24px 0;
            padding: 24px 0;
            border-radius: 12px;
            background: #f5f5f5;
            font-size: 1.5em;
            color: #2d4739;
            font-weight: 600;
            box-shadow: 0 1px 6px rgba(86,156,113,0.07);
        }
        .stat-label {
            display: block;
            font-size: 1em;
            color: #888;
            font-weight: 400;
            margin-top: 8px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="admin-header">
        <div class="logo">ShopAI</div>
        <ul class="nav-links" id="nav-links">
            <li><a href="admin-statistics.php" class="active">Statistics</a></li>
            <li><a href="admin-products.php">Products</a></li>
            <li><a href="admin-pending.php">Home</a></li>
        </ul>
        <form method="POST" action="admin.php" style="display:inline;">
            <input type="hidden" name="action" value="logout">
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </header>

    <div class="stats-container">
        <h2>Admin Statistics</h2>
        <div class="stat-box">
            <?php echo $userCount; ?>
            <span class="stat-label">Total Users</span>
        </div>
        <div class="stat-box">
            <?php echo $pendingCount; ?>
            <span class="stat-label">Pending Orders</span>
        </div>
        <div class="stat-box">
            <?php echo $productCount; ?>
            <span class="stat-label">Total Products</span>
        </div>
    </div>
</body>
</html>