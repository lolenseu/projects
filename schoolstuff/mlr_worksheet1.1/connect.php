<?php

    $con = new mysqli("localhost", "root", "", "db_reg1");

    if ($con->connect_error) {
        die("Connection failed: " . $con->connect_error);
    }

    $con->set_charset("utf8");


?>