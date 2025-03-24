<?php
session_start();
if (isset($_SESSION['username'])) {
    header("Location: post.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgriShop Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="logo-container">
        <img src="image/logo.png" alt="Gordon College Logo" class="college-logo">
    </div>
    <div class="container">
    
        <h2>AgriShop: Farmer Online</h2>
        <h3>Selling Web Application</h3>
        <form action="login.php" method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <a href="register.php">Signup here</a>
    </div>
</body>
</html>