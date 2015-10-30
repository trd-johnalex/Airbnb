<?php
$servername = "localhost";
$username = "trcorp_airbnb";
$password = "trscebu123";
$dbname = "trcorp_airbnb";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
} else {
    echo "Connected successfully <br>";
}