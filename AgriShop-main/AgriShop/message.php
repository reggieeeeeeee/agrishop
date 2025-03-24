<?php
session_start();

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "agrishop";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

$activeUser = $_SESSION['username'];

// Function to fetch messages between two users
function getMessages($conn, $user1, $user2) {
    $sql = "SELECT * FROM messages WHERE (sender = ? AND receiver = ?) OR (sender = ? AND receiver = ?) ORDER BY timestamp ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $user1, $user2, $user2, $user1);
    $stmt->execute();
    $result = $stmt->get_result();
    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
    return $messages;
}

// Function to send a message
function sendMessage($conn, $sender, $receiver, $message) {
    $sql = "INSERT INTO messages (sender, receiver, message, timestamp) VALUES (?, ?, ?, CURRENT_TIMESTAMP)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $sender, $receiver, $message);
    $stmt->execute();
}

// Function to delete a message
function deleteMessage($conn, $messageId, $activeUser) {
    $sql = "DELETE FROM messages WHERE id = ? AND sender = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $messageId, $activeUser);
    $stmt->execute();
}

// Function to get conversations (users you've messaged)
function getConversations($conn, $activeUser) {
    $sql = "SELECT DISTINCT receiver FROM messages WHERE sender = ? UNION SELECT DISTINCT sender FROM messages WHERE receiver = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $activeUser, $activeUser);
    $stmt->execute();
    $result = $stmt->get_result();
    $conversations = [];
    while ($row = $result->fetch_assoc()) {
        $conversations[] = $row['receiver'] ?? $row['sender'];
    }
    return $conversations;
}

// Handle sending a message
if (isset($_POST['send_message'])) {
    $receiver = $_POST['receiver'];
    $message = $_POST['message'];
    sendMessage($conn, $activeUser, $receiver, $message);
}

// Handle deleting a message
if (isset($_POST['delete_message'])) {
    $messageId = $_POST['message_id'];
    deleteMessage($conn, $messageId, $activeUser);
}

// Get the user to message (if set)
$receiverToMessage = isset($_GET['user']) ? $_GET['user'] : null;

// Get messages for display
$messages = [];
if ($receiverToMessage) {
    $messages = getMessages($conn, $activeUser, $receiverToMessage);
}

// Get conversations
$conversations = getConversations($conn, $activeUser);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Messaging</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .messenger-container {
            display: flex;
            height: 100vh;
        }

        .conversations {
            width: 250px;
            border-right: 1px solid #ccc;
            padding: 10px;
            overflow-y: auto;
            background-color: #f8f8f8; /* Light background */
        }

        .conversation-item {
            padding: 8px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
        }

        .conversation-item:hover {
            background-color: #f0f0f0;
        }

        .messages {
            flex-grow: 1;
            padding: 20px;
            overflow-y: auto;
            background-color: #fff; /* White background */
            display: flex;
            flex-direction: column;
        }

        .message-input-area {
            display: flex;
            margin-top: auto; /* Push to bottom */
            align-items: center; /* Vertically align items */
        }

        .message-input {
            flex-grow: 1;
            padding: 10px;
            border: 1px solid #ccc; /* Border for input */
            border-radius: 5px;
            margin-right: 5px; /* Add some space between input and button */
        }

        .message {
    margin-bottom: 10px;
    padding: 8px;
    border-radius: 5px;
    display: flex; /* Using flexbox for layout */
    justify-content: space-between; /* Space between message and settings */
    align-items: flex-start; /* Align items to the top */
        }
        .message span {
    flex-grow: 1; /* Allow message text to take up available space */
        }

        .message-settings {
        flex-shrink: 0; /* Prevent settings from shrinking */
        }

        .sent {
            background-color: #dcf8c6;
            text-align: left;
        }

        .received {
            background-color: #e6e6e6;
            text-align: left;
        }

        .navbar {
            background-color: #3498db; /* Blue navbar */
            border-bottom: 1px solid #2980b9;
            position: sticky; /* Make it stick */
            top: 0; /* Stick to the top */
            z-index: 100; /* Ensure it's above other elements */
        }

        .navbar-brand {
            font-size: 24px;
            font-weight: bold;
            color: white; /* White text */
        }

        .navbar-nav > li > a {
            padding-top: 15px;
            padding-bottom: 15px;
            color: white; /* White text */
        }

        .navbar-nav > li > a:hover {
            background-color: #2980b9; /* Darker blue on hover */
        }

        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgb(0,0,0); /* Fallback color */
            background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
        }

        .modal-content {
            background-color: #fefefe;
            margin: 20vh auto; /* Adjust top margin for vertical centering */
            padding: 20px;
            border: 1px solid #888;
            width: 400px; /* Reduced width */
            max-width: 90%; /* Ensure it's responsive */
        }

        .modal-content h2 {
            font-size: 1.2em; /* Smaller heading */
            margin-bottom: 10px;
        }

        .modal-content label {
            font-size: 0.9em; /* Smaller label text */
            display: block;
            margin-bottom: 5px;
        }

        .modal-content input[type="text"],
        .modal-content textarea {
            width: calc(100% - 12px); /* Adjust width */
            padding: 6px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing:border-box; /* Include padding and border in element's total width and height */
            font-size: 0.9em; /* Smaller input text */
        }

        .modal-content button {
            padding: 8px 15px; /* Smaller button padding */
            font-size: 0.9em; /* Smaller button text */
            margin-top: 10px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .timestamp {
        font-size: 0.7em; /* Make the timestamp smaller */
        color: #888;
        margin-top: 5px; /* Add some space between the message and timestamp */
        text-align: right;
        }

        #deleteConfirmationModal .modal-content {
            background-color: #fefefe;
            margin: 20vh auto;
            padding: 20px;
            border: 1px solid #888;
            width: 400px;
            max-width: 90%;
        }

        #deleteConfirmationModal h2 {
            font-size: 1.2em;
            margin-bottom: 10px;
        }

        #deleteConfirmationModal p {
            margin-bottom: 15px;
        }

        #deleteConfirmationModal button {
            margin-right: 10px;
        }

        .message-settings {
            position: relative;
            display: inline-block;
        }

        .settings-icon {
            cursor: pointer;
            font-size: 1.2em;
            margin-left: 10px;
        }

        .settings-dropdown {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 120px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            padding: 8px 12px;
            z-index: 1;
            right: 0;
        }

        .settings-dropdown button {
            display: block;
            width: 100%;
            text-align: left;
            border: none;
            background-color: transparent;
            padding: 5px 0;
            cursor: pointer;
            color: red;
        }

    </style>
</head>
<body>

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
                <li><a href="#" data-toggle="modal" data-target="#postModal">POST</a></li>
                <li><a href="buying.php">BUYING</a></li>
                <li><a href="selling.php">SELLING</a></li>
                <li><a href="message.php">MESSAGE</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="messenger-container">
    <div class="conversations">
        <button id="newMessageBtn" class="btn btn-primary btn-sm" style="margin-bottom: 10px;">New Message</button>

        <div id="newMessageModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>New Message</h2>
                <form method="post">
                    <label for="receiver">To:</label>
                    <input type="text" name="receiver" id="receiver" required>
                    <label for="message">Message:</label>
                    <textarea name="message" required></textarea>
                    <button type="submit" name="send_message" class="btn btn-primary">Send</button>
                    <button type="button" class="btn btn-secondary" id="cancelMessageBtn">Cancel</button>
                </form>
            </div>
        </div>

        <script>
            var modal = document.getElementById("newMessageModal");
            var btn = document.getElementById("newMessageBtn");
            var span = document.getElementsByClassName("close")[0];
            var cancelBtn = document.getElementById("cancelMessageBtn");

            btn.onclick = function() {
                modal.style.display = "block";
            }

            span.onclick = function() {
                modal.style.display = "none";
            }

            cancelBtn.onclick = function() {
                modal.style.display = "none";
            }

            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }
        </script>

        <?php foreach ($conversations as $conversation): ?>
            <div class="conversation-item" onclick="window.location.href='message.php?user=<?php echo htmlspecialchars($conversation); ?>'">
                <?php echo htmlspecialchars($conversation); ?>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="messages">
    <?php if ($receiverToMessage): ?>
        <h2>Conversation with <?php echo htmlspecialchars($receiverToMessage); ?></h2>
        <?php foreach ($messages as $message): ?>
            <div class="message <?php echo ($message['sender'] == $activeUser) ? 'sent' : 'received'; ?>">
                <span><?php echo htmlspecialchars($message['message']); ?></span>
                <span class="timestamp">
                    <?php echo date('Y-m-d H:i:s', strtotime($message['timestamp'])); ?>
                </span>
                <?php if ($message['sender'] == $activeUser): ?>
                    <div class="message-settings">
                        <span class="settings-icon" onclick="toggleSettings(<?php echo $message['id']; ?>)">&#8942;</span>
                        <div class="settings-dropdown" id="settings-<?php echo $message['id']; ?>">
                            <button class="delete-btn" onclick="showDeleteConfirmation(<?php echo $message['id']; ?>)">Delete</button>
                            <button class="cancel-btn" onclick="toggleSettings(<?php echo $message['id']; ?>)">Cancel</button>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        <div id="deleteConfirmationModal" class="modal">
            <div class="modal-content">
                <span class="close" id="closeDeleteModal">&times;</span>
                <h2>Delete Message?</h2>
                <p>Are you sure you want to delete this message?</p>
                <form method="post" id="deleteForm">
                    <input type="hidden" name="message_id" id="deleteMessageId" value="">
                    <button type="submit" name="delete_message" class="btn btn-danger">Delete</button>
                    <button type="button" class="btn btn-secondary" id="cancelDeleteBtn">Cancel</button>
                </form>
            </div>
        </div>
        <form method="post" class="message-input-area">
            <input type="hidden" name="receiver" value="<?php echo htmlspecialchars($receiverToMessage); ?>">
            <input type="text" name="message" class="message-input" placeholder="Type your message..." required>
            <button type="submit" name="send_message" class="btn btn-primary btn-sm">Send</button>
        </form>
    <?php endif; ?>
</div>

<script>
    var deleteModal = document.getElementById("deleteConfirmationModal");
    var closeDeleteModal = document.getElementById("closeDeleteModal");
    var cancelDeleteBtn = document.getElementById("cancelDeleteBtn");
    var deleteMessageIdInput = document.getElementById("deleteMessageId");

    function toggleSettings(messageId) {
        var settingsDropdown = document.getElementById('settings-' + messageId);
        if (settingsDropdown.style.display === "block") {
            settingsDropdown.style.display = "none";
        } else {
            settingsDropdown.style.display = "block";
        }
    }

    function showDeleteConfirmation(messageId) {
        deleteMessageIdInput.value = messageId;
        deleteModal.style.display = "block";
        var settingsDropdown = document.getElementById('settings-' + messageId);
        settingsDropdown.style.display = "none";
    }

    closeDeleteModal.onclick = function() {
        deleteModal.style.display = "none";
    }

    cancelDeleteBtn.onclick = function() {
        deleteModal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == deleteModal) {
            deleteModal.style.display = "none";
        }
    }
</script>
</body>
</html>

<?php
$conn->close();
?>