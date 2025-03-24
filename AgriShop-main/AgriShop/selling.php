


<!DOCTYPE html>
<html lang="en">
<head>
    <title>AgriShop: Farm Online Website</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- Navbar using Bootstrap  -->
<nav class="navbar navbar-default">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a href="index.php" class="navbar-brand">AgriShop: Farm Online Website</a>
        </div>

        <div class="collapse navbar-collapse" id="navbar-collapse">
            <ul class="nav navbar-nav navbar-right">
                <li><a href=post.php>POST</a><li>
                <li><a href=buying.php>BUYING</a><li>
                <li><a href=selling.php>SELLING</a></li>
                <li><a href=#>Item 4</a></li>
                <li><a href=#>Item 5</a></li>
                <li><a href=logout.php>Logout</a></li>
            </ul>
        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>
<div class="posts">
<?php
$conn = new mysqli("localhost", "root", "", "agrishop");
$sql = "SELECT posts.*, users.fullname, users.profile_image
        FROM posts 
        JOIN users ON posts.username = users.username 
        WHERE posts.category='selling' 
        ORDER BY posts.created_at DESC";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    echo "<div class='post'>";

    // Post header (Profile Picture + Name)
    echo "<div class='post-header'>";
    echo "<img src='" . htmlspecialchars($row['profile_image']) . "' class='profile-pic'>";
    echo "<p class='username'><strong>" . htmlspecialchars($row['fullname']) . "</strong></p>";
    echo "<span class='time' data-time='" . $row['created_at'] . "'></span>"; // Time ago
    echo "</div>"; // Close post-header
    
    // Post Content
    echo "<p class='description'>" . htmlspecialchars($row['description']) . "</p>";
    echo "<img src='" . htmlspecialchars($row['file_path']) . "' class='post-image'>";
    
    echo "</div>"; // Close post
    
}
$conn->close();
?>
    </div>
    </div>

    <script src="script.js"></script>
</body>
</html>