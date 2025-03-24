<?php 
session_start();
include 'post.php';

if (!isset($_SESSION['username'])) {
    die("Error: User not logged in.");
}

$conn = new mysqli("localhost", "root", "", "agrishop");

$user = $_SESSION['username']; 

$sql_user = "SELECT profile_image FROM users WHERE username = ?";
$stmt = $conn->prepare($sql_user);
$stmt->bind_param("s", $user);
$stmt->execute();
$result_user = $stmt->get_result();
$user_data = $result_user->fetch_assoc();

$profile_image = !empty($user_data['profile_image']) ? htmlspecialchars($user_data['profile_image']) : 'uploads/default.png';

?>



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
    <!-- Post Input Box -->

    <div class="post-container">
    <img src="<?php echo $profile_image; ?>" class="profile-pic">




    <div class="post-box" data-toggle="modal" data-target="#postModal">
        Click to post
    </div>
</div>



    
        <!-- Posts Container -->
        <div class="posts">
            <?php
            $conn = new mysqli("localhost", "root", "", "agrishop");
            $sql = "SELECT posts.*, users.fullname, users.profile_image
                    FROM posts 
                    JOIN users ON posts.username = users.username 
                    WHERE posts.category='buying' 
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
