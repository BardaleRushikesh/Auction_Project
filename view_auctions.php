<?php
session_start();
require 'db_connect.php';

// Security check: Only logged-in buyers can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'buyer') {
    echo "Access Denied. Only buyers can view this page.";
    exit();
}

// Fetch all active auctions, regardless of who is selling them
$sql = "SELECT * FROM auctions WHERE status = 'active'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Browse Auctions</title>
</head>
<body>
<h2>Active Auctions</h2>

<table border="1" cellpadding="10">
    <tr>
        <th>Item Title</th>
        <th>Description</th>
        <th>Current Highest Bid</th>
        <th>End Time</th>
        <th>Action</th>
    </tr>

    <?php
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                        <td>" . $row['title'] . "</td>
                        <td>" . $row['description'] . "</td>
                        <td>₹" . $row['current_price'] . "</td>
                        <td>" . $row['end_time'] . "</td>
                        <!-- This link sends the specific auction ID to the bidding page -->
                        <td><a href='bid.php?id=" . $row['id'] . "'><button>Place Bid</button></a></td>
                      </tr>";
        }
    } else {
        echo "<tr><td colspan='5'>There are no active auctions at the moment.</td></tr>";
    }
    ?>
</table>

<br>
<a href="dashboard.php">Back to Dashboard</a>
</body>
</html>