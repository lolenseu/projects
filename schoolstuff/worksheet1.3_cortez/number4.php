<?php
include 'db_connect.php';

$sql = "SELECT DISTINCT city 
        FROM customers 
        WHERE country = 'Venezuela'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>City</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>{$row['city']}</td></tr>";
    }
    echo "</table>";
} else {
    echo "No results found.";
}
$conn->close();
?>
