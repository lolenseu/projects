<?php
session_start();
include 'connection.php';
include 'admin-logout.php';

// Fetch all users
$sql = "SELECT id, username, email, registration_date FROM users ORDER BY id DESC";
// If registration_date not exist, just fetch id and username, email.
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Kimjay&Madel - Admin - Users</title>
    <link href="https://fonts.googleapis.com/css?family=Poppins" rel="stylesheet">
    <link href="styles/style.css" rel="stylesheet">
    <link href="styles/navbar.css" rel="stylesheet">
    <link href="styles/admin-navbar.css" rel="stylesheet">
    <link href="styles/admin-users.css" rel="stylesheet">
</head>
<body>
    <header class="admin-header">
        <div class="logo">Kimjay&Madel</div>
        <ul class="nav-links" id="nav-links">
            <li><a href="admin-statistics.php">Statistics</a></li>
            <li><a href="admin-status.php">Status</a></li>
            <li><a href="admin-products.php">Products</a></li>
            <li><a href="admin-users.php" class="active">Users</a></li>
        </ul>
        <form method="POST" action="admin-logout.php" style="display:inline;">
            <input type="hidden" name="action" value="logout">
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </header>

    <div class="admin-container">
        <h3>All Users</h3>
        <?php if ($result->num_rows > 0): ?>
            <table class="user-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <?php if (/* registration_date column exists */ false): ?>
                        <th>Registered</th>
                        <?php endif; ?>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <?php if (isset($user['registration_date'])): ?>
                                <td><?php echo $user['registration_date']; ?></td>
                            <?php endif; ?>
                            <td>
                                <a href="admin-edit-user.php?id=<?php echo $user['id']; ?>" class="btn small">Edit</a>
                                <a href="admin-delete-user.php?id=<?php echo $user['id']; ?>" class="btn small danger" onclick="return confirm('Delete user?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No users found.</p>
        <?php endif; ?>
    </div>

    <script src="scripts/admin.js"></script>
</body>
</html>