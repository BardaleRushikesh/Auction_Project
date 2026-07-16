<?php
session_start();

// Security check: If there is no user_id in the session, kick them back to login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['role'];
$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>
<h2>Welcome, <?php echo $username; ?>!</h2>
<p>You are logged in as a: <strong><?php echo $role; ?></strong></p>

<hr>

<!-- Display different links based on the user's role -->
<?php if ($role == 'seller'): ?>
    <h3>Seller Menu</h3>
    <ul>
        <li><a href="create_auction.php">List a New Item for Auction</a></li>
        <li><a href="my_items.php">View My Auctions</a></li>
    </ul>

<?php elseif ($role == 'buyer'): ?>
    <h3>Buyer Menu</h3>
    <ul>
        <li><a href="view_auctions.php">Browse Active Auctions</a></li>
        <li><a href="my_bids.php">View My Bids</a></li>
    </ul>

<?php elseif ($role == 'admin'): ?>
    <h3>Admin Menu</h3>
    <ul>
        <li><a href="admin.php">Open System Dashboard</a></li>
    </ul>
<?php endif; ?>

<br>
<a href="logout.php"><button>Logout</button></a>
</body>
</html>