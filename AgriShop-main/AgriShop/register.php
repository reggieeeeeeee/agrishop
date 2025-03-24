<?php 
include "database.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    

    // Handle Image Upload
    $profile_image_path = "uploads/default.png"; // Default profile picture
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
        $image_name = time() . '_' . $_FILES['profile_image']['name'];
        $image_tmp = $_FILES['profile_image']['tmp_name'];
        $upload_dir = "uploads/";

        // Gumawa ng uploads directory kung wala pa
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        move_uploaded_file($image_tmp, $upload_dir . $image_name);
        $profile_image_path = $upload_dir . $image_name;
    }

    // I-save sa database ang user kasama ang image path
    $sql = "INSERT INTO users (fullname, username, password,profile_image) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $fullname, $username, $password,$profile_image_path);

    if ($stmt->execute()) {
        echo "<script>alert('Registration successful! You can now login.'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Error in registration!'); window.location='register.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h2>Signup</h2>
        <form action="register.php" method="POST" enctype="multipart/form-data">
            <input type="text" name="fullname" placeholder="Full Name" required>
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            

            <!-- File input para sa profile picture -->
            <input type="file" name="profile_image" accept="image/*">

            <button type="submit">Register</button>
        </form>
        <a href="index.php">Back to login</a>
    </div>
</body>
</html>
