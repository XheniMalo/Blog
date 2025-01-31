<?php 

$servername = "localhost";
$username = "xhensila-malo";
$password = "1234";
$dbname = "atis";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>