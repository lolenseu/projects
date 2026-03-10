<?php
require_once 'includes/auth.php';

// Require login to access this page
requireLogin();

// Fetch user data using prepared statement
$sql = "SELECT username, email, created_at, last_login FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard • Instagram</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container dashboard-container">
        <div class="instagram-card">
            <div class="logo">
                <span class="instagram-logo">Instagram</span>
            </div>
            
            <div class="dashboard-header">
                <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>! 👋</h2>
                
                <div class="user-info">
                    <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                    <p><strong>Member since:</strong> <?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>
                    <p><strong>Last login:</strong> <?php echo $user['last_login'] ? date('F j, Y g:i A', strtotime($user['last_login'])) : 'First login'; ?></p>
                </div>
                
                <a href="logout.php"><button class="btn btn-logout">Log Out</button></a>
            </div>
        </div>
    </div>
</body>
</html>