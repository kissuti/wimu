<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "wimu";

// Create connection
$kapcsolat = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$kapcsolat) {
    die("Connection failed: " . mysqli_connect_error());
}

?>