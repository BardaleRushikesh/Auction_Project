<?php
session_start();
require 'db_connect.php';

// Security check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'seller') {
    echo "Access Denied. Only sellers can view this page.";
    exit();
}

$seller_id = $_SESSION['user_id'];

// Fetch only the auctions that belong to this specific seller
$sql = "SELECT * FROM auctions WHERE seller_id = '$seller_id'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Auctions</title>
</head>
<body>
<h2>My Listed Items</h2>

<table border="1" cellpadding="10">
    <tr>
        <th>Item Title</th>
        <th>Starting Price</th>
        <th>Current Highest Bid</th>
        <th>End Time</th>
        <th>Status</th>
    </tr>

    <?php
    // Loop through the database results and create a table row for each item
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                        <td>" . $row['title'] . "</td>
                        <td>₹" . $row['starting_price'] . "</td>
                        <td>₹" . $row['current_price'] . "</td>
                        <td>" . $row['end_time'] . "</td>
                        <td>" . ucfirst($row['status']) . "</td>
                      </tr>";
        }
    } else {
        // If the query returns 0 rows
        echo "<tr><td colspan='5'>You haven't listed any items for auction yet.</td></tr>";
    }
    ?>
</table>

<br>
<a href="dashboard.php">Back to Dashboard</a>
</body>
</html>