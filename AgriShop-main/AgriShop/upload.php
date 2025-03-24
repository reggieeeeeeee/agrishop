<?php
session_start(); // Start session para magamit ang username ng naka-login
$servername = "localhost";
$username = "root"; 
$password = "";
$dbname = "agrishop";

// Connect sa Database
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check kung may naka-login
if (!isset($_SESSION["username"])) {
    die("You must be logged in to upload a post.");
}

$logged_in_user = $_SESSION["username"];

// Kunin ang fullname ng user mula sa database
$sql_user = "SELECT fullname FROM users WHERE username = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("s", $logged_in_user);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user_data = $result_user->fetch_assoc();
$fullname = $user_data["fullname"];

if (isset($_POST["submit"])) {
    $category = $_POST["category"];
    $description = $_POST["description"];
    $targetDir = "uploads/"; // Folder para sa uploads
    $fileName = basename($_FILES["file"]["name"]);
    $targetFilePath = $targetDir . $fileName;
    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

    // Allowed file types (images & videos)
    $allowedTypes = array("jpg", "jpeg", "png", "gif", "mp4", "mov", "avi");

    if (in_array($fileType, $allowedTypes)) {
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)) {
            // I-save sa database gamit ang tunay na username at fullname
            $sql = "INSERT INTO posts (username, category, description, file_path) 
                    VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $logged_in_user, $category, $description, $targetFilePath);

            if ($stmt->execute()) {
                echo "File uploaded successfully!";
            } else {
                echo "Error: " . $conn->error;
            }
        } else {
            echo "Error uploading file.";
        }
    } else {
        echo "Invalid file type.";
    }
}

$conn->close();
?>
