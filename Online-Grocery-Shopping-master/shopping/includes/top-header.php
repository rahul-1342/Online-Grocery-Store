<?php 
session_start();
include_once('includes/config.php');

$notification_count = 0;
if (isset($_SESSION['id'])) {
    $user_id = $_SESSION['id'];
    
    // Check if the user is logged in and has an email associated
    $user_email_query = mysqli_query($con, "SELECT email FROM users WHERE id='$user_id'");
    if ($user_email_query && mysqli_num_rows($user_email_query) > 0) {
        $user_email = mysqli_fetch_assoc($user_email_query)['email'];
        
        // Check if the necessary tables exist
        $tables_exist = true;
        if (!mysqli_query($con, "SELECT 1 FROM contact_queries LIMIT 1") || 
            !mysqli_query($con, "SELECT 1 FROM query_conversations LIMIT 1")) {
            $tables_exist = false;
        }
        
        if ($tables_exist) {
            // Check if is_read column exists
            $column_check = mysqli_query($con, "SHOW COLUMNS FROM query_conversations LIKE 'is_read'");
            $column_exists = (mysqli_num_rows($column_check) > 0);
            
            $query = "SELECT COUNT(*) as count FROM query_conversations 
                     WHERE query_id IN (SELECT id FROM contact_queries WHERE email='$user_email')
                     AND sender_type='admin'";
            
            if ($column_exists) {
                $query .= " AND is_read=0";
            }
            
            $notification_query = mysqli_query($con, $query);
            if ($notification_query) {
                $notification_data = mysqli_fetch_assoc($notification_query);
                $notification_count = $notification_data['count'];
            }
        }
    }
}
?>

<div class="top-bar animate-dropdown">
    <div class="container">
        <div class="header-top-inner">
            <div class="cnt-account">
                <ul class="list-unstyled">

                <?php if (isset($_SESSION['login']) && strlen($_SESSION['login']) > 0) { ?>
                    <li><a href="index.php  "><i class="icon fa fa-user"></i>Welcome - <?php echo htmlentities($_SESSION['username']); ?></a></li>
                <?php } ?>

                <li><a href="my-account.php"><i class="icon fa fa-user"></i>My Account</a></li>
                <li><a href="my-wishlist.php"><i class="icon fa fa-heart"></i>Wishlist</a></li>
                <li><a href="my-cart.php"><i class="icon fa fa-shopping-cart"></i>My Cart</a></li>
                
                <!-- Notification Bell -->
                <?php if (isset($_SESSION['id'])) { ?>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="icon fa fa-bell"></i>Notifications
                        <?php if ($notification_count > 0) { ?>
                            <span class="notification-badge"><?php echo $notification_count; ?></span>
                        <?php } ?>
                    </a>
                    <ul class="dropdown-menu notification-dropdown">
                        <?php if ($notification_count > 0) { 
                            $replies_query = mysqli_query($con, "SELECT cq.subject, qc.message, qc.created_at 
                                                              FROM query_conversations qc
                                                              JOIN contact_queries cq ON qc.query_id = cq.id
                                                              WHERE cq.email='$user_email' AND qc.sender_type='admin'
                                                              ORDER BY qc.created_at DESC LIMIT 5");
                            
                            while($reply = mysqli_fetch_array($replies_query)) { ?>
                            <li>
                                <div class="notification-item">
                                    <h5><?php echo htmlentities($reply['subject']); ?></h5>
                                    <p><?php echo nl2br(htmlentities(substr($reply['message'], 0, 100))); ?></p>
                                    <small><?php echo htmlentities($reply['created_at']); ?></small>
                                </div>
                            </li>
                            <li class="divider"></li>
                            <?php } ?>
                            <li><a href="view-replies.php" class="text-center">View All Notifications</a></li>
                        <?php } else { ?>
                            <li><div class="notification-item">No new notifications</div></li>
                        <?php } ?>
                    </ul>
                </li>
                <?php } ?>

                <?php if (!isset($_SESSION['login']) || strlen($_SESSION['login']) == 0) { ?>
                    <li><a href="login.php"><i class="icon fa fa-sign-in"></i>Login</a></li>
                <?php } else { ?>
                    <li><a href="#" id="logout-btn"><i class="icon fa fa-sign-out"></i>Logout</a></li>
                <?php } ?>	
                </ul>
            </div><!-- /.cnt-account -->

            <div class="cnt-block">
                <ul class="list-unstyled list-inline">
                    <li class="dropdown dropdown-small">
                        <a href="track-orders.php" class="dropdown-toggle"><span class="key">Track Order</span></a>
                        <a href="admin">Admin Login</a>
                    </li>
                </ul>
            </div>

            <div class="clearfix"></div>
        </div><!-- /.header-top-inner -->
    </div><!-- /.container -->
</div><!-- /.header-top -->

<style>
.notification-badge {
    background: #e74c3c;
    color: #fff;
    border-radius: 50%;
    padding: 2px 6px;
    font-size: 12px;
    position: relative;
    top: -10px;
    right: 5px;
}

.notification-dropdown {
    width: 300px;
    padding: 10px;
}

.notification-item {
    padding: 10px;
}

.notification-item h5 {
    margin: 0 0 5px 0;
    font-size: 14px;
}

.notification-item p {
    margin: 0;
    font-size: 13px;
    color: #666;
}

.notification-item small {
    color: #999;
    font-size: 12px;
}

.divider {
    height: 1px;
    background: #eee;
    margin: 5px 0;
}
</style>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const logoutBtn = document.getElementById('logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: "You will be logged out.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, logout',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'logout.php';
                }
            });
        });
    }
    
    // Initialize dropdown toggle for notifications
    $('.dropdown-toggle').dropdown();
});
</script>