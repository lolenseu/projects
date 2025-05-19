<?php

// Start the session
session_start();

// Database connection
include 'connection.php';

// Handle Logout
include 'admin-logout.php';

// Fetch admin user profile (assuming admin is logged in)
$adminId = $_SESSION['user_id'] ?? null;
$adminData = null;
if ($adminId) {
    $stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
    $stmt->bind_param("i", $adminId);
    $stmt->execute();
    $adminData = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['new_status'])) {
    $orderId = intval($_POST['order_id']);
    $newStatus = $_POST['new_status'];
    $stmt = $conn->prepare("UPDATE pending_orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $newStatus, $orderId);
    $stmt->execute();
    $stmt->close();
    header("Location: admin-pending.php");
    exit();
}

// Fetch pending orders with user and product info
$pendingSql = "
    SELECT po.id, po.user_id, po.product_id, po.quantity, po.status, po.order_time,
           u.username, u.email,
           p.name AS product_name, p.price, p.product_img
    FROM pending_orders po
    JOIN users u ON po.user_id = u.id
    JOIN products p ON po.product_id = p.id
    WHERE po.status = 'pending'
    ORDER BY po.order_time DESC
";
$pendingOrders = $conn->query($pendingSql);

// Fetch completed/delivered orders
$completedSql = "
    SELECT po.id, po.user_id, po.product_id, po.quantity, po.status, po.order_time,
           u.username, u.email,
           p.name AS product_name, p.price, p.product_img
    FROM pending_orders po
    JOIN users u ON po.user_id = u.id
    JOIN products p ON po.product_id = p.id
    WHERE po.status IN ('completed', 'delivered')
    ORDER BY po.order_time DESC
";
$completedOrders = $conn->query($completedSql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ShopAI - Admin - Status</title>

    <link href="https://fonts.googleapis.com/css?family=Poppins" rel="stylesheet">

    <link href="styles/style.css" rel="stylesheet">
    <link href="styles/navbar.css" rel="stylesheet">
    <link href="styles/admin-navbar.css" rel="stylesheet">
    <link href="styles/admin-containers.css" rel="stylesheet">
    <style>
        .admin-container {
            margin: 40px auto 0 auto;
            max-width: 1100px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 16px rgba(0,0,0,0.08);
            padding: 32px 24px;
        }
        .admin-flex-row {
            display: flex;
            gap: 24px;
        }
        .admin-profile {
            flex: 1;
            background: #f5f5f5;
            border-radius: 12px;
            padding: 24px;
            min-width: 180px;
            max-width: 220px;
            box-shadow: 0 1px 6px rgba(86,156,113,0.07);
            height: fit-content;
        }
        .admin-orders {
            flex: 2.5;
            background:rgb(224, 224, 224);
            border-radius: 12px;
            padding: 0 12px;
        }
        .admin-status-editor {
            flex: 1.2;
            background: #f5f5f5;
            border-radius: 12px;
            padding: 24px;
            min-width: 180px;
            max-width: 260px;
            box-shadow: 0 1px 6px rgba(86,156,113,0.07);
            height: fit-content;
        }
        .order-row {
            display: flex;
            align-items: center;
            border-bottom: 1px solid #eee;
            padding: 12px 0;
            gap: 16px;
        }
        .order-row img {
            width: 48px;
            height: 48px;
            object-fit: cover;
            border-radius: 6px;
        }
        .order-info {
            flex: 2;
        }
        .order-actions {
            flex: 1;
            text-align: right;
        }
        .status-badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 8px;
            font-size: 0.95em;
            color: #fff;
            background: #569c71;
        }
        .status-badge.completed { background: #2d4739; }
        .status-badge.delivered { background: #1e88e5; }
        .status-badge.pending { background: #e5a11e; }
        .admin-container h3 {
            margin-top: 0;
            margin-bottom: 18px;
            color: #555;
            font-size: 1.5em;
        }
        p {
            margin: 0;
            color: #555;
            font-size: 1em;
        }
        .admin-container + .admin-container {
            margin-top: 36px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="admin-header">
        <div class="logo">ShopAI</div>
        <ul class="nav-links" id="nav-links">
            <li><a href="admin-statistics.php">Statistics</a></li>
            <li><a href="admin-status.php" class="active">Status</a></li>
            <li><a href="admin-products.php">Products</a></li>
        </ul>
        <form method="POST" action="admin-logout.php" style="display:inline;">
            <input type="hidden" name="action" value="logout">
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </header>

    <!-- First Container: Pending Orders -->
    <div class="admin-container">
        <h3>Pending</h3>
        <div class="admin-flex-row">
            <div class="admin-orders">
                <h4>Pending Orders</h4>
                <?php if ($pendingOrders->num_rows > 0): ?>
                    <?php while ($order = $pendingOrders->fetch_assoc()): ?>
                        <div class="order-row">
                            <img src="data:image/jpeg;base64,<?php echo base64_encode($order['product_img']); ?>" alt="Product">
                            <div class="order-info">
                                <div><strong><?php echo htmlspecialchars($order['product_name']); ?></strong> (x<?php echo $order['quantity']; ?>)</div>
                                <div>₱<?php echo number_format($order['price'], 2); ?></div>
                                <div>User: <?php echo htmlspecialchars($order['username']); ?> (<?php echo htmlspecialchars($order['email']); ?>)</div>
                                <div>Order Time: <?php echo $order['order_time']; ?></div>
                            </div>
                            <div class="order-actions">
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <select name="new_status">
                                        <option value="pending" <?php if($order['status']=='pending') echo 'selected'; ?>>Pending</option>
                                        <option value="completed">Completed</option>
                                        <option value="delivered">Delivered</option>
                                    </select>
                                    <button type="submit" class="edit-btn">Update</button>
                                </form>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No pending orders.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Second Container: Completed/Delivered Orders Log -->
    <div class="admin-container">
        <h3>Completed & Delivered Orders</h3>
        <?php if ($completedOrders->num_rows > 0): ?>
            <?php while ($order = $completedOrders->fetch_assoc()): ?>
                <div class="order-row">
                    <img src="data:image/jpeg;base64,<?php echo base64_encode($order['product_img']); ?>" alt="Product">
                    <div class="order-info">
                        <div><strong><?php echo htmlspecialchars($order['product_name']); ?></strong> (x<?php echo $order['quantity']; ?>)</div>
                        <div>₱<?php echo number_format($order['price'], 2); ?></div>
                        <div>User: <?php echo htmlspecialchars($order['username']); ?> (<?php echo htmlspecialchars($order['email']); ?>)</div>
                        <div>Order Time: <?php echo $order['order_time']; ?></div>
                    </div>
                    <div class="order-actions">
                        <span class="status-badge <?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No completed or delivered orders.</p>
        <?php endif; ?>
    </div>

    <script src="scripts/admin.js"></script>
</body>
</html>