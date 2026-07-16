<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'buyer') {
    echo "Access Denied.";
    exit();
}

if (isset($_GET['id'])) {
    $auction_id = $_GET['id'];
} else {
    echo "No item selected for checkout.";
    exit();
}

// Fetch the winning item details
$sql = "SELECT * FROM auctions WHERE id = '$auction_id'";
$result = $conn->query($sql);
$item = $result->fetch_assoc();

// When the Razorpay pop-up succeeds, it sends a POST request with the payment ID
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['razorpay_payment_id'])) {

    $payment_id = $_POST['razorpay_payment_id'];

    // Update the database status to 'paid'
    $update_sql = "UPDATE auctions SET status = 'paid' WHERE id = '$auction_id'";

    if ($conn->query($update_sql) === TRUE) {
        echo "<h3 style='color: green;'>Payment Successful!</h3>";
        echo "<p><strong>Transaction ID:</strong> " . $payment_id . "</p>";
        echo "<p>Thank you for purchasing: " . $item['title'] . " for ₹" . $item['current_price'] . "</p>";
        echo "<a href='dashboard.php'>Return to Dashboard</a>";
        exit(); // Stop loading the page so the user only sees the receipt
    } else {
        echo "<p style='color: red;'>Database error: " . $conn->error . "</p>";
    }
}

// Razorpay strictly calculates money in smaller currency units (Paisa).
// We must multiply the Rupee amount by 100 before giving it to the script.
$amount_in_paisa = $item['current_price'] * 100;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Secure Checkout</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        .checkout-box { border: 1px solid #ccc; padding: 20px; width: 350px; border-radius: 8px; box-shadow: 2px 2px 10px rgba(0,0,0,0.1); }
        /* Hiding Razorpay's default ugly button to use a custom one if desired, though the default works fine too */
    </style>
</head>
<body>
<h2>Complete Your Purchase</h2>

<div class="checkout-box">
    <h3>Order Summary</h3>
    <p><strong>Winning Item:</strong> <?php echo $item['title']; ?></p>
    <p><strong>Total Due:</strong> ₹<?php echo $item['current_price']; ?></p>

    <hr>

    <!-- The Razorpay Client-Side Integration -->
    <form action="checkout.php?id=<?php echo $auction_id; ?>" method="POST">
        <script
            src="https://checkout.razorpay.com/v1/checkout.js"
            data-key="rzp_test_T9B7RaLD9cqR5o"
            data-amount="<?php echo $amount_in_paisa; ?>"
            data-currency="INR"
            data-name="College Auction Project"
            data-description="Payment for <?php echo $item['title']; ?>"
            data-theme.color="#3399cc">
        </script>
    </form>
</div>

<br>
<a href="dashboard.php" style="color: gray;">Cancel and return to dashboard</a>
</body>
</html>