<?php
session_start();
require 'db_connect.php';

// Security check: Kick out anyone who isn't an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    echo "Access Denied. Administrator privileges required.";
    exit();
}

$admin_username = $_SESSION['username'];

// --- REPORTING LOGIC --- //

// 1. Total Registered Users
$user_query = "SELECT COUNT(*) as total FROM users";
$user_result = $conn->query($user_query)->fetch_assoc();

// 2. Total Active Auctions
$active_query = "SELECT COUNT(*) as total FROM auctions WHERE status = 'active'";
$active_result = $conn->query($active_query)->fetch_assoc();

// 3. Total Sales (Revenue of all 'paid' items)
$revenue_query = "SELECT SUM(current_price) as total FROM auctions WHERE status = 'paid'";
$revenue_result = $conn->query($revenue_query)->fetch_assoc();
$total_revenue = $revenue_result['total'] ? $revenue_result['total'] : 0; // Fallback to 0 if null

// 4. Detailed Auction Report (Fetching all items for the admin to review)
$report_sql = "SELECT title, current_price, end_time, status FROM auctions ORDER BY end_time DESC";
$report_result = $conn->query($report_sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        .stats-container { display: flex; gap: 20px; margin-bottom: 30px; }
        .stat-box { border: 1px solid #333; padding: 15px; border-radius: 5px; background-color: #f9f9f9; width: 200px; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid black; padding: 10px; text-align: left; }
        th { background-color: #ddd; }
    </style>
</head>
<body>
<h2>System Administrator: <?php echo $admin_username; ?></h2>
<a href="logout.php"><button>Logout</button></a>

<hr>

<h3>Platform Analytics</h3>
<div class="stats-container">
    <div class="stat-box">
        <h4>Total Users</h4>
        <h2><?php echo $user_result['total']; ?></h2>
    </div>
    <div class="stat-box">
        <h4>Active Auctions</h4>
        <h2><?php echo $active_result['total']; ?></h2>
    </div>
    <div class="stat-box">
        <h4>Total Value Sold</h4>
        <h2>₹<?php echo $total_revenue; ?></h2>
    </div>
</div>

<h3>Complete Auction Report</h3>
<table>
    <tr>
        <th>Item Title</th>
        <th>Final/Current Price</th>
        <th>End Time</th>
        <th>Status</th>
    </tr>

    <?php
    if ($report_result->num_rows > 0) {
        while($row = $report_result->fetch_assoc()) {

            // Color code the status for the admin
            $color = "black";
            if ($row['status'] == 'active') $color = "blue";
            if ($row['status'] == 'paid') $color = "green";

            echo "<tr>
                        <td>" . $row['title'] . "</td>
                        <td>₹" . $row['current_price'] . "</td>
                        <td>" . $row['end_time'] . "</td>
                        <td style='color: $color; font-weight: bold;'>" . strtoupper($row['status']) . "</td>
                      </tr>";
        }
    } else {
        echo "<tr><td colspan='4'>No auctions exist in the database yet.</td></tr>";
    }
    ?>
</table>
</body>
</html>
