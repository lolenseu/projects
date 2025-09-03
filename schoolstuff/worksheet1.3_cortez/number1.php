<?php
include 'db_connect.php';

$sql = "SELECT customername, contactname, address, city, postalcode 
        FROM customers 
        WHERE country = 'Brazil'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Customer Name</th><th>Contact Name</th><th>Address</th><th>City</th><th>Postal Code</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['customername']}</td>
                <td>{$row['contactname']}</td>
                <td>{$row['address']}</td>
                <td>{$row['city']}</td>
                <td>{$row['postalcode']}</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "No results found.";
}
$conn->close();
?>
