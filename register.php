<?php
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // NEW LOGIC: Check if the username or email already exists
    $check_sql = "SELECT * FROM users WHERE username = '$user' OR email = '$email'";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        // If a row is found, it means the user or email is taken
        echo "<p style='color: red;'>Registration Failed: That username or email is already in use. Please choose another.</p>";
    } else {
        // If no rows are found, it is safe to insert the new user
        $sql = "INSERT INTO users (username, password, email, role) VALUES ('$user', '$pass', '$email', '$role')";

        if ($conn->query($sql) === TRUE) {
            echo "<p style='color: green;'>Registration successful! You can now login.</p>";
        } else {
            echo "<p style='color: red;'>Error: " . $conn->error . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - Auction Platform</title>
</head>
<body>
<!-- (Your HTML form remains exactly the same here) -->
<h2>Register a New Account</h2>
<form method="POST" action="register.php">
    <label>Username:</label><br>
    <input type="text" name="username" required><br><br>

    <label>Email:</label><br>
    <input type="email" name="email" required><br><br>

    <label>Password:</label><br>
    <input type="password" name="password" required><br><br>

    <label>Account Type:</label><br>
    <select name="role">
        <option value="buyer">Buyer</option>
        <option value="seller">Seller</option>
    </select><br><br>

    <button type="submit">Register</button>
</form>
</body>
</html>