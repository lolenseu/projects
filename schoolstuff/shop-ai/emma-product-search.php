<?php
header('Content-Type: application/json');

// Database connection
include 'connection.php';

$query = trim($_GET['q'] ?? '');
if (!$query) {
    echo json_encode(['success' => false, 'error' => 'No query']);
    exit;
}

// Try exact, then LIKE, then SOUNDEX for fuzzy match
$stmt = $conn->prepare("SELECT id, name, price, product_img FROM products WHERE name LIKE CONCAT('%', ?, '%') OR SOUNDEX(name) = SOUNDEX(?) LIMIT 1");
$stmt->bind_param("ss", $query, $query);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode([
        'success' => true,
        'product' => [
            'id' => $row['id'],
            'name' => $row['name'],
            'price' => $row['price'],
            'img' => 'data:image/jpeg;base64,' . base64_encode($row['product_img'])
        ]
    ]);
} else {
    echo json_encode(['success' => false, 'error' => 'No product found']);
}
?>