<?php
// Database connection
include('connection.php');

// Response message
$messageok = '';
$messagebad = '';

// Check if the user is logged in and the 'id' and 'currentpassword' are set
if (isset($_GET['id']) && isset($_POST['currentpassword'])) {
    $userId = $_GET['id'];
    $currentPassword = $_POST['currentpassword'];

    // Fetch the user's current details from the database
    $sql = "SELECT * FROM users_data WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Check if the entered current password matches the stored password
        if (password_verify($currentPassword, $user['password'])) {

            // Delete the user if password is correct
            $deleteSql = "DELETE FROM users_data WHERE id = ?";
            $deleteStmt = $conn->prepare($deleteSql);
            $deleteStmt->bind_param("i", $userId);
            $deleteStmt->execute();

            // Execute the statement
            if ($stmt->execute()) {
              $messageok = 'Your account has been successfully deleted.';
            } else {
                $messagebad = "Error: " . $conn->error;
            }
        } else {
            $messagebad = 'Incorrect password. Please try again.';
        }
    } else {
        $messagebad = 'User not found. Please check the user ID.';
    }
} else {
    $messagebad = 'User ID or password not provided.';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Delete Status</title>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <link rel="stylesheet" href="../css/header.css">
  <link rel="stylesheet" href="../css/delete_handler.css">
  <link rel="stylesheet" href="../css/footer.css">
  <link rel="icon" href="../img/icon.png">
</head>
<body>
  <!-- Header -->
  <div class="header">
    <a href="#" class="logo"><img src="../img/2024.png">ESports<br>Festival</a>
    <div class="header-right">
      <a href="../index.html">Home</a>
      <a href="../about.php">About</a>
      <a href="../register.html">Register</a>
      <a href="../contact.html">ContactUs</a>
    </div><br>
    <div class="searchbox">
      <form action="search_handler.php" method="GET">
        <button type="submit">Search</button>
        <input type="text" name="search" placeholder="Enter a keyword..." required>
      </form>
    </div>
  </div>

  <!-- Content -->
  <div class="content">
    <div class="content-box">
    <h2>Delete Status</h2>
      <?php if ($messageok): ?>
        <p><?php echo $messageok; ?></p>
        <a href="../index.html">Go back to Home</a>
      <?php elseif ($messagebad): ?>
        <p><?php echo $messagebad; ?></p>
        <a href="javascript:history.back()">Go back to Profile</a>
      <?php endif; ?>
    </div>
  </div>

  <!-- Footer -->
  <footer>
    <h4>© Copyright 2024 | ESports Festival</h4>
  </footer>
</body>
</html>
