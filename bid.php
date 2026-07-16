<?php
session_start();
require 'db_connect.php';

// Security check: Only logged-in buyers can bid
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'buyer') {
    echo "Access Denied. Only buyers can place bids.";
    exit();
}

$buyer_id = $_SESSION['user_id'];

// Grab the auction ID from the URL (e.g., bid.php?id=1)
if (isset($_GET['id'])) {
    $auction_id = $_GET['id'];
} else {
    echo "No item selected.";
    exit();
}

// Fetch the item's current details from the database
$sql = "SELECT * FROM auctions WHERE id = '$auction_id'";
$result = $conn->query($sql);
$item = $result->fetch_assoc();

// If the user submits a new bid via the form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_bid = $_POST['bid_amount'];
    $current_price = $item['current_price'];

    // THE LOGIC: Is the new bid actually higher than the current price?
    if ($new_bid > $current_price) {

        // 1. Update the 'auctions' table with the new highest price
        $update_sql = "UPDATE auctions SET current_price = '$new_bid' WHERE id = '$auction_id'";
        $conn->query($update_sql);

        // 2. Log this specific bid into the 'bids' table for the history report
        // We use MySQL's NOW() function to instantly grab the current date and time
        $insert_sql = "INSERT INTO bids (auction_id, user_id, bid_amount, bid_time) 
                       VALUES ('$auction_id', '$buyer_id', '$new_bid', NOW())";
        $conn->query($insert_sql);

        echo "<p style='color: green;'>Success! You are now the highest bidder.</p>";

        // Update the $item array so the HTML below immediately shows the new price
        $item['current_price'] = $new_bid;
    } else {
        echo "<p style='color: red;'>Invalid Bid: You must bid higher than ₹" . $current_price . ".</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Place a Bid</title>
</head>
<body>
<h2>Bidding on: <?php echo $item['title']; ?></h2>
<p><strong>Description:</strong> <?php echo $item['description']; ?></p>
<p><strong>Auction Ends:</strong> <?php echo $item['end_time']; ?></p>

<h3 style="color: blue;">Current Highest Bid: ₹<?php echo $item['current_price']; ?></h3>

<form method="POST" action="bid.php?id=<?php echo $auction_id; ?>">
    <label>Enter Your Bid (₹):</label><br>
    <input type="number" step="0.01" name="bid_amount" required><br><br>

    <button type="submit">Submit Bid</button>
</form>

<br>
<a href="view_auctions.php">Back to Active Auctions</a>
</body>
</html>