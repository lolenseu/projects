<?php
include 'db_connect.php';

$sql = "SELECT customername, city, country 
        FROM customers 
        WHERE city IN ('London', 'Berlin', 'Madrid', 'Caracas')";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Customer Name</th><th>City</th><th>Country</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['customername']}</td>
                <td>{$row['city']}</td>
                <td>{$row['country']}</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "No results found.";
}
$conn->close();
?>
