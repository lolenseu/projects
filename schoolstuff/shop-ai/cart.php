<?php
// Fetch cart items for logged-in users
if ($action === 'get_cart' && $isLoggedIn) {
    $userId = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT c.product_id, c.quantity, p.name, p.price, p.product_img FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id=?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $cart = [];
    while ($row = $result->fetch_assoc()) {
        $cart[] = [
            'id' => $row['product_id'],
            'name' => $row['name'],
            'price' => $row['price'],
            'img' => 'data:image/jpeg;base64,' . base64_encode($row['product_img']),
            'quantity' => $row['quantity']
        ];
    }
    echo json_encode(['success' => true, 'cart' => $cart]);
    exit();
}

// Handle AJAX request for cart items
if ($action === 'add_to_cart' && $isLoggedIn && isset($_POST['product_id'])) {
    $userId = $_SESSION['user_id'];
    $productId = intval($_POST['product_id']);

    $stmt = $conn->prepare("SELECT * FROM cart WHERE user_id=? AND product_id=?");
    $stmt->bind_param("ii", $userId, $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE user_id=? AND product_id=?");
        $stmt->bind_param("ii", $userId, $productId);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)");
        $stmt->bind_param("ii", $userId, $productId);
        $stmt->execute();
    }
    echo json_encode(['success' => true]);
    exit();
}

if ($action === 'decrease_quantity' && $isLoggedIn && isset($_POST['product_id'])) {
    $userId = $_SESSION['user_id'];
    $productId = intval($_POST['product_id']);
    $stmt = $conn->prepare("UPDATE cart SET quantity = quantity - 1 WHERE user_id=? AND product_id=? AND quantity > 1");
    $stmt->bind_param("ii", $userId, $productId);
    $stmt->execute();
    $conn->query("DELETE FROM cart WHERE user_id=$userId AND product_id=$productId AND quantity < 1");
    echo json_encode(['success' => true]);
    exit();
}

if ($action === 'increase_quantity' && $isLoggedIn && isset($_POST['product_id'])) {
    $userId = $_SESSION['user_id'];
    $productId = intval($_POST['product_id']);
    $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE user_id=? AND product_id=?");
    $stmt->bind_param("ii", $userId, $productId);
    $stmt->execute();
    echo json_encode(['success' => true]);
    exit();
}

if ($action === 'remove_from_cart' && $isLoggedIn && isset($_POST['product_id'])) {
    $userId = $_SESSION['user_id'];
    $productId = intval($_POST['product_id']);
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id=? AND product_id=?");
    $stmt->bind_param("ii", $userId, $productId);
    $stmt->execute();
    echo json_encode(['success' => true]);
    exit();
}

if ($action === 'clear_cart' && $isLoggedIn) {
    $userId = $_SESSION['user_id'];
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id=?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    echo json_encode(['success' => true]);
    exit();
}
?>