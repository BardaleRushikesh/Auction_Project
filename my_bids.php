<?php
session_start();
require 'db_connect.php';

// Security check: Only logged-in buyers
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'buyer') {
    echo "Access Denied. Only buyers can view this page.";
    exit();
}

$buyer_id = $_SESSION['user_id'];

// The SQL JOIN connects the 'bids' table (b) and 'auctions' table (a) using the auction ID
$sql = "SELECT b.auction_id, b.bid_amount, b.bid_time, a.title, a.current_price, a.status, a.end_time 
        FROM bids b
        JOIN auctions a ON b.auction_id = a.id
        WHERE b.user_id = '$buyer_id'
        ORDER BY b.bid_time DESC"; // Orders by newest bids first

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Bid History</title>
</head>
<body>
<h2>My Bidding History</h2>

<table border="1" cellpadding="10">
    <tr>
        <th>Item Title</th>
        <th>My Bid Amount</th>
        <th>Time Placed</th>
        <th>Item's Current Highest Price</th>
        <th>Auction Status</th>
    </tr>

    <?php
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {

            // Grab current time to compare against the auction end time
            $current_time = date('Y-m-d H:i:s');
            $is_ended = ($row['end_time'] < $current_time);

            $status_message = "";

            if ($is_ended) {
                // The auction is over. Did this user win?
                if ($row['bid_amount'] >= $row['current_price']) {
                    // They won! Show the checkout link.
                    $status_message = "<span style='color: green;'><strong>You Won!</strong> <br> <a href='checkout.php?id=" . $row['auction_id'] . "'><button>Pay Now</button></a></span>";
                } else {
                    // They lost.
                    $status_message = "<span style='color: gray;'>Auction Ended. You lost.</span>";
                }
            } else {
                // The auction is still active
                if ($row['bid_amount'] < $row['current_price']) {
                    $status_message = "<span style='color: red;'>Outbid</span>";
                } else {
                    $status_message = "<span style='color: green;'>Highest Bidder</span>";
                }
            }

            echo "<tr>
                        <td>" . $row['title'] . "</td>
                        <td>₹" . $row['bid_amount'] . "</td>
                        <td>" . $row['bid_time'] . "</td>
                        <td>₹" . $row['current_price'] . "</td>
                        <td>" . $status_message . "</td>
                      </tr>";
        }
    } else {
        echo "<tr><td colspan='5'>You have not placed any bids yet.</td></tr>";
    }
    ?>
</table>

<br>
<a href="dashboard.php">Back to Dashboard</a>
</body>
</html>