<div>
    <table border="1" width="75%">
        <tr>
            <th>ID</th>
            <th>Lastname</th>
            <th>Firstname</th>
            <th>Middlename</th>
            <th>Address</th>
            <th>Action</th>
        </tr>


    <?php
        include_once "connect.php";
        $sql_profile = "SELECT * FROM tbpersonal";
        $result_profile = mysqli_query($con, $sql_profile);

        if (mysqli_num_rows($result_profile) > 0) {
            while ($row = mysqli_fetch_array($result_profile)) {
                echo "<tr>";
                echo "<td>" . $row['ProfileID'] . "</td>";
                echo "<td>" . $row['lastname'] . "</td>";
                echo "<td>" . $row['firstname'] . "</td>";
                echo "<td>" . $row['middlename'] . "</td>";
                echo "<td>" . $row['address'] . "</td>";
                echo "<td>";

                echo "</td>";
                echo "</tr>";
            }
        }
    ?>
    </table>
</div>