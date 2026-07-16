<?php
// XAMPP default database credentials
$servername = "localhost";
$username = "root";
$password = ""; // Leave this empty for XAMPP
$dbname = "auction_db";

// Create the connection (Similar to JDBC in Java)
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if the connection works
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
