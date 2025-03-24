<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "wimu";

// Create connection
$kapcsolat = mysqli_connect($servername, $username, $password, $dbname);
mysqli_set_charset($kapcsolat, "utf8"); // utf8 karakterkódolás beállítása

// Check connection
if (!$kapcsolat) {
    die("Connection failed: " . mysqli_connect_error());
}

?>