<?php
session_start();
include('include/config.php');
if (!isset($_SESSION['alogin']) || strlen((string)$_SESSION['alogin']) === 0) {
    header('Location: index.php');
    exit();
}

date_default_timezone_set('Asia/Kolkata');
$currentTime = date('d-m-Y h:i:s A', time());

// Handle status update
if (isset($_POST['update_status'])) {
    $id = intval($_GET['id']);
    $status = $_POST['status'];
    
    $query = mysqli_query($con, "UPDATE contact_queries SET status='$status' WHERE id='$id'");
    
    if ($query) {
        $_SESSION['msg'] = "Query status updated successfully!";
    } else {
        $_SESSION['msg'] = "Error updating query status: " . mysqli_error($con);
    }
}

// Handle reply submission
if (isset($_POST['send_reply'])) {
    $id = intval($_GET['id']);
    $reply_message = mysqli_real_escape_string($con, $_POST['reply_message']);
    $admin_id = $_SESSION['alogin'];
    
    // Insert reply into conversation table
    $insert_reply = mysqli_query($con, "INSERT INTO query_conversations 
                                      (query_id, sender_type, message, created_at, admin_id) 
                                      VALUES ('$id', 'admin', '$reply_message', NOW(), '$admin_id')");
    
    if ($insert_reply) {
        // Update query status to replied
        mysqli_query($con, "UPDATE contact_queries SET status='replied' WHERE id='$id'");
        $_SESSION['msg'] = "Reply sent successfully!";
    } else {
        $_SESSION['msg'] = "Error sending reply: " . mysqli_error($con);
    }
}

$id = intval($_GET['id']);
$query = mysqli_query($con, "SELECT * FROM contact_queries WHERE id='$id'");
$queryData = mysqli_fetch_array($query);

// Get conversation history
$conversation_query = mysqli_query($con, "SELECT * FROM query_conversations 
                                        WHERE query_id='$id' 
                                        ORDER BY created_at ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin| View Contact Query</title>
    <link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    <link type="text/css" href="css/theme.css" rel="stylesheet">
    <link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
    <link type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600' rel='stylesheet'>
    <style>
        .conversation {
            margin-bottom: 20px;
            border: 1px solid #eee;
            padding: 15px;
            border-radius: 5px;
        }
        .customer-message {
            background-color: #f5f5f5;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }
        .admin-message {
            background-color: #e1f5fe;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }
        .message-time {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>
<body>
<?php include('include/header.php');?>

    <div class="wrapper">
        <div class="container">
            <div class="row">
<?php include('include/sidebar.php');?>                
            <div class="span9">
                    <div class="content">

                        <div class="module">
                            <div class="module-head">
                                <h3>View Contact Query</h3>
                            </div>
                            <div class="module-body">

                                <?php if(isset($_SESSION['msg'])) { ?>
                                <div class="alert alert-success">
                                    <button type="button" class="close" data-dismiss="alert">×</button>
                                    <strong>Success!</strong> <?php echo htmlentities($_SESSION['msg']); unset($_SESSION['msg']); ?>
                                </div>
                                <?php } ?>

                                <div class="conversation">
                                    <h4>Query Details</h4>
                                    <div class="control-group">
                                        <label class="control-label"><strong>Name:</strong></label>
                                        <div class="controls">
                                            <?php echo htmlentities($queryData['name']); ?>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label"><strong>Email:</strong></label>
                                        <div class="controls">
                                            <?php echo htmlentities($queryData['email']); ?>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label"><strong>Phone:</strong></label>
                                        <div class="controls">
                                            <?php echo htmlentities($queryData['phone']); ?>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label"><strong>Subject:</strong></label>
                                        <div class="controls">
                                            <?php echo htmlentities($queryData['subject']); ?>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label"><strong>Original Message:</strong></label>
                                        <div class="controls customer-message">
                                            <?php echo nl2br(htmlentities($queryData['message'])); ?>
                                            <div class="message-time">
                                                <?php echo htmlentities($queryData['created_at']); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="conversation">
                                    <h4>Conversation History</h4>
                                    <?php if(mysqli_num_rows($conversation_query) > 0) { 
                                        while($conv = mysqli_fetch_array($conversation_query)) {
                                            $messageClass = ($conv['sender_type'] == 'admin') ? 'admin-message' : 'customer-message';
                                    ?>
                                        <div class="<?php echo $messageClass; ?>">
                                            <?php echo nl2br(htmlentities($conv['message'])); ?>
                                            <div class="message-time">
                                                <?php echo htmlentities($conv['created_at']); ?>
                                                <?php if($conv['sender_type'] == 'admin') {
                                                    echo " (Admin)";
                                                } ?>
                                            </div>
                                        </div>
                                    <?php } 
                                    } else { ?>
                                        <p>No conversation history yet.</p>
                                    <?php } ?>
                                </div>

                                <div class="conversation">
                                    <h4>Reply to Customer</h4>
                                    <form method="post">
                                        <div class="control-group">
                                            <label class="control-label" for="reply_message">Your Reply</label>
                                            <div class="controls">
                                                <textarea id="reply_message" name="reply_message" class="span8" rows="5" required></textarea>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label" for="status">Status</label>
                                            <div class="controls">
                                                <select name="status" class="span4" required>
                                                    <option value="read" <?php echo ($queryData['status'] == 'read') ? 'selected' : ''; ?>>Read</option>
                                                    <option value="unread" <?php echo ($queryData['status'] == 'unread') ? 'selected' : ''; ?>>Unread</option>
                                                    <option value="replied" <?php echo ($queryData['status'] == 'replied') ? 'selected' : ''; ?>>Replied</option>
                                                    <option value="resolved" <?php echo ($queryData['status'] == 'resolved') ? 'selected' : ''; ?>>Resolved</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <div class="controls">
                                                <button type="submit" name="send_reply" class="btn btn-success">Send Reply</button>
                                                <button type="submit" name="update_status" class="btn btn-primary">Update Status Only</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>

                    </div><!--/.content-->
                </div><!--/.span9-->
            </div>
        </div><!--/.container-->
    </div><!--/.wrapper-->

<?php include('include/footer.php');?>

    <script src="scripts/jquery-1.9.1.min.js" type="text/javascript"></script>
    <script src="scripts/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
    <script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="scripts/flot/jquery.flot.js" type="text/javascript"></script>
</body>
</html>