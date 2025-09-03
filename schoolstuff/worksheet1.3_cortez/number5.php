<?php
include 'db_connect.php';

$sql = "SELECT country, COUNT(*) AS total_customers 
        FROM customers 
        GROUP BY country";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Country</th><th>Total Customers</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['country']}</td>
                <td>{$row['total_customers']}</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "No results found.";
}
$conn->close();
?>
