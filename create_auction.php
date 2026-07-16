<?php
session_start();
require 'db_connect.php';

// Security: Only logged-in sellers can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'seller') {
    echo "Access Denied. Only sellers can create auctions.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $seller_id = $_SESSION['user_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $starting_price = $_POST['starting_price'];

    // Convert the HTML datetime-local input to a MySQL friendly format
    $end_time = date('Y-m-d H:i:s', strtotime($_POST['end_time']));

    // Set the starting price as the current price, and status to active
    $current_price = $starting_price;
    $status = 'active';

    // Insert into the 'auction' table (matching your phpMyAdmin spelling)
    $sql = "INSERT INTO auctions (seller_id, title, description, starting_price, current_price, end_time, status) 
            VALUES ('$seller_id', '$title', '$description', '$starting_price', '$current_price', '$end_time', '$status')";

    if ($conn->query($sql) === TRUE) {
        echo "<p style='color: green;'>Auction created successfully!</p>";
    } else {
        echo "<p style='color: red;'>Error: " . $conn->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Auction</title>
</head>
<body>
<h2>List a New Item for Auction</h2>

<form method="POST" action="create_auction.php">
    <label>Item Title:</label><br>
    <input type="text" name="title" required><br><br>

    <label>Description:</label><br>
    <textarea name="description" rows="4" required></textarea><br><br>

    <label>Starting Price (₹):</label><br>
    <input type="number" step="0.01" name="starting_price" required><br><br>

    <label>Auction End Date & Time:</label><br>
    <input type="datetime-local" name="end_time" required><br><br>

    <button type="submit">Create Listing</button>
</form>

<br>
<a href="dashboard.php">Back to Dashboard</a>
</body>
</html>