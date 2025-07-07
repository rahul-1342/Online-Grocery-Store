<?php
session_start();
require_once('includes/config.php');

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['id'];
$replies = [];

$user_email_query = mysqli_query($con, "SELECT email FROM users WHERE id='$user_id'");
if ($user_email_query && mysqli_num_rows($user_email_query) > 0) {
    $user_email = mysqli_fetch_assoc($user_email_query)['email'];

    $replies_query = mysqli_query($con, "
        SELECT cq.subject, qc.message, qc.created_at 
        FROM query_conversations qc
        JOIN contact_queries cq ON qc.query_id = cq.id
        WHERE cq.email='$user_email' AND qc.sender_type='admin'
        ORDER BY qc.created_at DESC
    ");

    while ($row = mysqli_fetch_assoc($replies_query)) {
        $replies[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Replies</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <style>
        body {
            padding: 20px;
            font-family: Arial, sans-serif;
        }

        .reply-box {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            box-shadow: 0 0 5px rgba(0,0,0,0.05);
        }

        .reply-box h5 {
            margin: 0 0 8px;
            font-size: 16px;
            font-weight: bold;
        }

        .reply-box p {
            margin: 0;
            font-size: 14px;
            color: #444;
        }

        .reply-box small {
            display: block;
            color: #888;
            margin-top: 8px;
            font-size: 12px;
        }

        .no-replies {
            text-align: center;
            padding: 30px;
            color: #999;
        }

        .back-btn {
            margin-top: 20px;
            display: inline-block;
        }
    </style>
</head>
<body>

<h3>Admin Replies</h3>

<?php if (count($replies) > 0): ?>
    <?php foreach ($replies as $reply): ?>
        <div class="reply-box">
            <h5><?php echo htmlentities($reply['subject']); ?></h5>
            <p><?php echo nl2br(htmlentities($reply['message'])); ?></p>
            <small><?php echo htmlentities($reply['created_at']); ?></small>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="no-replies">
        <i class="fa fa-bell-o fa-2x"></i>
        <p>No new replies from admin.</p>
    </div>
<?php endif; ?>

<a href="index.php" class="btn btn-primary back-btn"><i class="fa fa-arrow-left"></i> Back to Home</a>

</body>
</html>
