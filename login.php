<?php
// Always start the session at the very top of any file that needs user data
session_start();
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Find the user in the database
    $sql = "SELECT id, password, role FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Check if the typed password matches the hashed password in the DB
        if (password_verify($password, $row['password'])) {

            // Set session variables (the user is now logged in!)
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['username'] = $username;

            // Redirect to the dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            echo "<p style='color: red;'>Incorrect password.</p>";
        }
    } else {
        echo "<p style='color: red;'>User not found.</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Auction Platform</title>
</head>
<body>
<h2>Login to Your Account</h2>
<form method="POST" action="login.php">
    <label>Username:</label><br>
    <input type="text" name="username" required><br><br>

    <label>Password:</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Login</button>
</form>
<p>Don't have an account? <a href="register.php">Register here</a></p>
</body>
</html>