<?php

// Start the session
session_start();

// Database connection
include 'connection.php';

// Fetch Products
$sql = "SELECT * FROM products";
$result = $conn->query($sql);

// Handle Add Product
if (isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    if (isset($_FILES['product_img']['tmp_name']) && $_FILES['product_img']['tmp_name']) {
        $image = file_get_contents($_FILES['product_img']['tmp_name']);
    } else {
        $image = null;
    }

    $result = $conn->query("SELECT MAX(id) AS max_id FROM products");
    $row = $result->fetch_assoc();
    $nextId = $row['max_id'] ? $row['max_id'] + 1 : 1;

    $stmt = $conn->prepare("INSERT INTO products (id, name, price, description, product_img) VALUES (?, ?, ?, ?, ?)");
    $null = null;
    $stmt->bind_param("isdsb", $nextId, $name, $price, $description, $null);
    $stmt->send_long_data(4, $image );


    if ($stmt->execute()) {
        header("Location: admin.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Handle Edit Product
if (isset($_POST['edit_product'])) {
    $id = $_POST['product_id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    if (!empty($_FILES['product_img']['tmp_name'])) {
        $image = file_get_contents($_FILES['product_img']['tmp_name']);
        $stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, description = ?, product_img = ? WHERE id = ?");
        $null = null;
        $stmt->bind_param("sdsbi", $name, $price, $description, $null, $id);
        $stmt->send_long_data(3, $image);
    } else {
        $stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, description = ? WHERE id = ?");
        $stmt->bind_param("sdsi", $name, $price, $description, $id);
    }    

    if ($stmt->execute()) {
        echo "<script>alert('Product updated successfully!'); window.location.href='admin.php';</script>";
    } else {
        echo "<script>alert('Update failed.');</script>";
    }
    $stmt->close();
}

// Handle Delete Product
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $stmt->close();
        // Reset AUTO_INCREMENT to the next available number
        $conn->query("ALTER TABLE products AUTO_INCREMENT = 1");
        header("Location: admin.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Handle logout
if (isset($_POST['action']) && $_POST['action'] === 'logout') {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopAI - Admin Panel</title>

    <link href="https://fonts.googleapis.com/css?family=Poppins" rel="stylesheet">
    
    <link href="styles/style.css" rel="stylesheet">
    <link href="styles/navbar.css" rel="stylesheet">
    <link href="styles/admin-navbar.css" rel="stylesheet">
    <link href="styles/admin-containers.css" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header class="admin-header">
        <div class="logo">ShopAI</div>
        <form method="POST" action="admin.php" style="display:inline;">
            <input type="hidden" name="action" value="logout">
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </header>

    <!-- Main Content -->
    <div class="admin-container">

        <!-- Add Product Popover -->
        <button popovertarget="add-product-popover" class="add-product-btn">Add Product</button>

        <div popover id="add-product-popover" class="add-product-popover-container">
        <button popovertarget="add-product-popover" popovertargetaction="hide" class="add-product-popover-close-btn" aria-label="Close">&times;</button>
            <h3>Add Product</h3>
            <form method="POST" enctype="multipart/form-data" class="add-product-form">
                <label for="name">Product Name:</label>
                <input type="text" id="name" name="name" required><br><br>

                <label for="price">Price:</label>
                <input type="number" id="price" name="price" step="0.01" required><br><br>

                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="4" required></textarea><br><br>

                <label for="product_img">Product Image:</label>
                <input type="file" id="product_img" name="product_img" accept="product_img/*" required><br><br>

                <button type="submit" name="add_product">Add Product</button>
            </form>
        </div>
        
        <!-- Product List -->
        <h3>Products</h3>
        <?php while ($row = $result->fetch_assoc()): ?>
            <?php
            $imageData = base64_encode($row['product_img']);
            $imageSrc = "data:product_img/jpeg;base64," . $imageData;
            ?>
            <div class="product-row">
                <img src="<?php echo $imageSrc; ?>" alt="Product Image">
                <div class="product-info">
                    <h4><?php echo $row['name']; ?></h4>
                    <p>Price: ₱<?php echo number_format($row['price'], 2); ?></p>
                    <p>Description: <?php echo $row['description']; ?></p>
                </div>
                <div class="product-actions">
                    <button 
                        type="button" 
                        class="edit-btn"
                        data-id="<?php echo $row['id']; ?>"
                        data-name="<?php echo htmlspecialchars($row['name'], ENT_QUOTES); ?>"
                        data-price="<?php echo $row['price']; ?>"
                        data-description="<?php echo htmlspecialchars($row['description'], ENT_QUOTES); ?>"
                        popovertarget="edit-product-popover"
                    >
                        Edit
                    </button>
                    <form method="GET" action="admin.php" style="display: inline;">
                        <input type="hidden" name="delete" value="<?php echo $row['id']; ?>">
                        <button type="submit" class="delete-btn">Delete</button>
                    </form>
                </div>
            </div>
        <?php endwhile; ?>

        <!-- Edit Product Popover -->
        <div popover id="edit-product-popover" class="edit-product-popover-container">
            <h3>Edit Product</h3>
            <button popovertarget="edit-product-popover" popovertargetaction="hide" class="edit-product-popover-close-btn" aria-label="Close">&times;</button>
            
            <form method="POST" enctype="multipart/form-data" action="admin.php" class="edit-product-form">
                <input type="hidden" name="product_id" id="edit-product-id">

                <label for="edit-name">Product Name:</label>
                <input type="text" id="edit-name" name="name"><br><br>

                <label for="edit-price">Price:</label>
                <input type="number" id="edit-price" name="price" step="0.01"><br><br>

                <label for="edit-description">Description:</label>
                <textarea id="edit-description" name="description" rows="4"></textarea><br><br>

                <label for="edit-product_img">Product Image:</label>
                <input type="file" id="edit-product_img" name="product_img" accept="product_img/*"><br><br>

                <button type="submit" name="edit_product" style>Update Product</button>
            </form>
        </div>
    </div>

    <script src="scripts/admin.js"></script>
</body>
</html>