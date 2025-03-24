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
    <style>
        .post-image-square {
            width: 300px;
            height: 300px;
            object-fit: cover;
        }

        .post-details, #confirmMessage {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            border: 1px solid #ccc;
            z-index: 1000;
        }

        .message-input {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
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
                <span class-bar"></span>
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
                <li><a href="#">Item 5</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<div id="postModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Post Your Item</h4>
            </div>
            <div class="modal-body">
                <form action="upload.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="category">Select Category:</label>
                        <select class="form-control" name="category" required>
                            <option value="selling">Selling</option>
                            <option value="buying">Buying</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="description">Description:</label>
                        <textarea class="form-control" name="description" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="file">Upload Image/Video:</label>
                        <input type="file" class="form-control" name="file" accept="image/*,video/*" required>
                    </div>

                    <button type="submit" class="btn btn-primary" name="submit">Post</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="postDetails" class="post-details">
    <p id="postDescription"></p>
    <h2>Interested?</h2>
    <button id="messageSellerButton">Message</button>
    <button id="backButton">Back</button>
</div>

<div id="confirmMessage" class="message-popup">
    <h2>Message Seller?</h2>
    <button id="confirmSendMessage">Open message</button>
    <button id="cancelSendMessage">Cancel</button>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

<script>
    // Function to show the messaging popup
    function showConfirmMessage() {
        $('#confirmMessage').show();
    }

    // Function to hide the messaging popup
    function hideConfirmMessage() {
        $('#confirmMessage').hide();
    }

    $(document).on('click', '.post', function() {
        var description = $(this).data('description');
        var image = $(this).data('image');
        var sellerId = $(this).data('seller-id');

        $('#postDescription').text(description);
        $('#postImage').attr('src', image);
        $('#messageSellerButton').data('seller-id', sellerId);
        $('#postDetails').show();
    });

    $('#backButton').click(function() {
        $('#postDetails').hide();
    });

    $('#messageSellerButton').click(function() {
        showConfirmMessage();
    });

    $('#cancelSendMessage').click(function() {
        hideConfirmMessage();
    });

    $('#confirmSendMessage').click(function(){
        var sellerId = $('#messageSellerButton').data('seller-id');
        window.location.href = "message.php?seller_id=" + sellerId;
    });

</script>
</body>
</html>