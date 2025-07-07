<?php
session_start();
include_once 'include/config.php';

if(strlen($_SESSION['alogin'])==0) { 
    header('location:index.php');
    exit();
}

$oid = intval($_GET['oid']);

// Function to generate automatic status remarks
function generateStatusRemark($status, $orderData) {
    switch($status) {
        case 'Assigned':
            return "Delivery Partner: ".($orderData['delivery_boy_name'] ?? 'being assigned');
            
        case 'Processing':
            return "Your order is being prepared";
            
        case 'Shipped':
            return "Order shipped - on the way to your location";
            
        case 'Out for Delivery':
            $remark = "Out for delivery";
            if($orderData['delivery_boy_name']) {
                $remark .= " with ".$orderData['delivery_boy_name'];
            }
            return 'Your Order is Out of Delivery';
            
        case 'Delivered':
            $orderDate = new DateTime($orderData['orderDate']);
            $deliveryDate = new DateTime();
            $interval = $orderDate->diff($deliveryDate);
            
            $timeText = "";
            if($interval->d > 0) $timeText .= $interval->d." days ";
            if($interval->h > 0) $timeText .= $interval->h." hours ";
            if($interval->i > 0) $timeText .= $interval->i." minutes";
            
            return "Delivered successfully! Total delivery time: ".trim($timeText);
            
        case 'Cancelled':
            return "Order cancelled";
            
        default:
            return "Status updated to ".$status;
    }
}

// Process form submission
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle delivery boy assignment
    if(isset($_POST['assign_delivery'])) {
        $delivery_boy_id = intval($_POST['delivery_boy']);
        $remark = mysqli_real_escape_string($con, $_POST['remark'] ?? 'Delivery boy assigned');
        
        if($delivery_boy_id > 0) {
            mysqli_begin_transaction($con);
            try {
                // Get delivery boy name
                $boy = mysqli_fetch_assoc(mysqli_query($con, "SELECT name FROM delivery_boys WHERE id=$delivery_boy_id"));
                
                // Update order status and assign delivery boy
                $update_query = "UPDATE orders 
                               SET delivery_boy_id = $delivery_boy_id, 
                                   orderStatus = 'Assigned' 
                               WHERE id = $oid";
                
                if(!mysqli_query($con, $update_query)) {
                    throw new Exception("Order update failed: ".mysqli_error($con));
                }
                
                // Generate automatic remark
                $auto_remark = "Delivery Partner: ".$boy['name'];
                if(!empty($remark)) {
                    $auto_remark .= " (Note: $remark)";
                }
                
                // Add to track history
                $history_query = "INSERT INTO ordertrackhistory(orderId, status, remark, postingDate) 
                                VALUES($oid, 'Assigned', '$auto_remark', NOW())";
                
                if(!mysqli_query($con, $history_query)) {
                    throw new Exception("History update failed: ".mysqli_error($con));
                }
                
                mysqli_commit($con);
                $_SESSION['success_msg'] = "Order successfully assigned to delivery boy";
                
            } catch (Exception $e) {
                mysqli_rollback($con);
                $_SESSION['error_msg'] = "Error: ".$e->getMessage();
            }
            header("Location: updateorder.php?oid=$oid");
            exit;
        }
    }
    
    // Handle status update
    if(isset($_POST['submit2'])) {
        $status = mysqli_real_escape_string($con, $_POST['status']);
        $admin_remark = mysqli_real_escape_string($con, $_POST['remark']);
        
        // Get current order details
        $orderData = mysqli_fetch_assoc(mysqli_query($con, 
            "SELECT o.*, d.name as delivery_boy_name 
             FROM orders o 
             LEFT JOIN delivery_boys d ON o.delivery_boy_id = d.id 
             WHERE o.id='$oid'"));
        
        // Generate automatic remark
        $status_remark = generateStatusRemark($status, $orderData);
        
        // Combine with admin remark if provided
        $final_remark = $status_remark;
        if(!empty($admin_remark)) {
            $final_remark .= " (Note: $admin_remark)";
        }
        
        // Begin transaction
        mysqli_begin_transaction($con);
        
        try {
            // For parent orders
            if($orderData['is_grouped']) {
                $childUpdateQuery = "UPDATE orders SET orderStatus='$status'";
                if($status == 'Delivered') {
                    $childUpdateQuery .= ", delivery_confirmed_at=NOW()";
                }
                $childUpdateQuery .= " WHERE parent_order_id='$oid'";
                
                if(!mysqli_query($con, $childUpdateQuery)) {
                    throw new Exception("Child order update failed: ".mysqli_error($con));
                }
            }
            
            // Main order update
            $updateQuery = "UPDATE orders SET orderStatus='$status'";
            if($status == 'Delivered') {
                $updateQuery .= ", delivery_confirmed_at=NOW()";
            }
            $updateQuery .= " WHERE id='$oid'";
            
            if(!mysqli_query($con, $updateQuery)) {
                throw new Exception("Order update failed: ".mysqli_error($con));
            }
            
            // Track history with automatic remark
            $trackHistory = "INSERT INTO ordertrackhistory(orderId, status, remark, postingDate) 
                VALUES('$oid', '$status', '$final_remark', NOW())";
            if(!mysqli_query($con, $trackHistory)) {
                throw new Exception("History update failed: ".mysqli_error($con));
            }
            
            mysqli_commit($con);
            $_SESSION['success_msg'] = "Order #$oid updated to $status";
            header("Location: todays-orders.php");
            exit();
            
        } catch (Exception $e) {
            mysqli_rollback($con);
            $_SESSION['error_msg'] = $e->getMessage();
        }
    }
}

// Fetch order details
$order = mysqli_fetch_assoc(mysqli_query($con, 
    "SELECT o.*, d.name as delivery_boy_name 
     FROM orders o 
     LEFT JOIN delivery_boys d ON o.delivery_boy_id = d.id 
     WHERE o.id='$oid'"));

if(!$order) {
    $_SESSION['error_msg'] = "Order not found";
    header("Location: todays-orders.php");
    exit();
}

// Fetch tracking history
$trackHistory = mysqli_query($con, 
    "SELECT * FROM ordertrackhistory 
     WHERE orderId='$oid' 
     ORDER BY postingDate DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin| Update Order #<?php echo $oid; ?></title>
    <link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    <link type="text/css" href="css/theme.css" rel="stylesheet">
    <link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
    <link type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600' rel='stylesheet'>
    <style>
        .fontkink1 { font-weight: bold; padding-right: 10px; }
        .fontkink { color: #333; }
        .form-control {
            height: 34px;
            padding: 6px 12px;
            font-size: 14px;
            line-height: 1.42857143;
            color: #555;
            background-color: #fff;
            background-image: none;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
            transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
        }
        .status-form {
            padding: 20px;
            background: #f9f9f9;
            border-radius: 5px;
            margin-top: 20px;
        }
        textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        select { padding: 8px; border: 1px solid #ddd; border-radius: 4px; width: 100%; }
        .alert { margin: 10px 0; padding: 10px; border-radius: 4px; }
        .alert-error { background-color: #f2dede; border-color: #ebccd1; color: #a94442; }
        .alert-success { background-color: #dff0d8; border-color: #d6e9c6; color: #3c763d; }
        .status-badge {
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
            margin: 2px 0;
        }
        .delivery-boy-info {
            padding: 8px;
            background: #f0f7ff;
            border-radius: 4px;
            margin-top: 5px;
        }
        .system-remark {
            color: #31708f;
        }
        .admin-note {
            color: #8a6d3b;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <?php include('include/header.php'); ?>

    <div class="wrapper">
        <div class="container">
            <div class="row">
                <?php include('include/sidebar.php'); ?>                
                <div class="span9">
                    <div class="content">
                        <div class="module">
                            <div class="module-head">
                                <h3>Update Order #<?php echo $oid; ?></h3>
                            </div>
                            <div class="module-body">
                                <?php if(isset($_SESSION['error_msg'])): ?>
                                    <div class="alert alert-error">
                                        <?php echo $_SESSION['error_msg']; unset($_SESSION['error_msg']); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if(isset($_SESSION['success_msg'])): ?>
                                    <div class="alert alert-success">
                                        <?php echo $_SESSION['success_msg']; unset($_SESSION['success_msg']); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="status-form">
                                    <form name="updateticket" id="updateticket" method="post"> 
                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                            <tr height="50">
                                                <td colspan="2" style="padding-left:0px;">
                                                    <div style="font-size:16px;font-weight:bold;color:#0066cc;">
                                                        <b>Order Details</b>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr height="30">
                                                <td class="fontkink1"><b>Order ID:</b></td>
                                                <td class="fontkink"><?php echo $oid; ?></td>
                                            </tr>
                                            <tr height="30">
                                                <td class="fontkink1"><b>Current Status:</b></td>
                                                <td class="fontkink">
                                                    <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $order['orderStatus'])); ?>">
                                                        <?php echo htmlentities($order['orderStatus']); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                            
                                            <tr height="50">
                                                <td class="fontkink1">Delivery Boy:</td>
                                                <td class="fontkink">
                                                    <?php if(in_array($order['orderStatus'], ['Pending', 'Processing'])): ?>
                                                        <select name="delivery_boy" class="form-control span4" required>
                                                            <option value="">Select Delivery Boy</option>
                                                            <?php 
                                                            $boys = mysqli_query($con, "SELECT id, name FROM delivery_boys WHERE status='active'");
                                                            while($boy = mysqli_fetch_array($boys)) {
                                                                $selected = ($order['delivery_boy_id'] == $boy['id']) ? 'selected' : '';
                                                                echo "<option value='{$boy['id']}' $selected>{$boy['name']}</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                        <div style="margin-top:10px;">
                                                            <label>Assignment Note:</label>
                                                            <textarea name="remark" class="form-control" rows="2" placeholder="Special instructions for delivery..."></textarea>
                                                        </div>
                                                        <button type="submit" name="assign_delivery" class="btn btn-primary" style="margin-top:10px;">
                                                            <i class="icon-user"></i> Assign Delivery Boy
                                                        </button>
                                                    <?php else: ?>
                                                        <div class="delivery-boy-info">
                                                            <?php if($order['delivery_boy_id']): ?>
                                                                <strong><?php echo htmlentities($order['delivery_boy_name'] ?? 'Unknown'); ?></strong>
                                                                (ID: <?php echo $order['delivery_boy_id']; ?>)
                                                            <?php else: ?>
                                                                Not assigned
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            
                                            <?php if(mysqli_num_rows($trackHistory) > 0): ?>
                                                <tr>
                                                    <td colspan="2"><hr /></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2">
                                                        <h4>Status History</h4>
                                                        <table class="table table-bordered">
                                                            <tr>
                                                                <th>Status</th>
                                                                <th>Remark</th>
                                                                <th>Date</th>
                                                            </tr>
                                                            <?php while($history = mysqli_fetch_assoc($trackHistory)): ?>
                                                            <tr>
                                                                <td>
                                                                    <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $history['status'])); ?>">
                                                                        <?php echo htmlentities($history['status']); ?>
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    <?php 
                                                                    // Split system remark and admin note for styling
                                                                    $parts = explode("(Note:", $history['remark']);
                                                                    echo '<span class="system-remark">'.htmlentities(trim($parts[0])).'</span>';
                                                                    if(isset($parts[1])) {
                                                                        echo ' <span class="admin-note">('.htmlentities(trim($parts[1])).'</span>';
                                                                    }
                                                                    ?>
                                                                </td>
                                                                <td><?php echo htmlentities($history['postingDate']); ?></td>
                                                            </tr>
                                                            <?php endwhile; ?>
                                                        </table>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                            
                                            <?php if($order['orderStatus'] != 'Delivered'): ?>
                                                <tr>
                                                    <td colspan="2"><hr /></td>
                                                </tr>
                                                <tr height="50">
                                                    <td class="fontkink1">Status: </td>
                                                    <td class="fontkink">
                                                        <div class="control-group">
                                                            <div class="controls">
                                                                <select name="status" class="form-control span4" required>
                                                                    <option value="">Select Status</option>
                                                                    <option value="Pending" <?= $order['orderStatus']=='Pending'?'selected':'' ?>>Pending</option>
                                                                    <option value="Processing" <?= $order['orderStatus']=='Processing'?'selected':'' ?>>Processing</option>
                                                                    <option value="Assigned" <?= $order['orderStatus']=='Assigned'?'selected':'' ?>>Assigned</option>
                                                                    <option value="Shipped" <?= $order['orderStatus']=='Shipped'?'selected':'' ?>>Shipped</option>
                                                                    <option value="Out for Delivery" <?= $order['orderStatus']=='Out for Delivery'?'selected':'' ?>>Out for Delivery</option>
                                                                    <option value="Delivered" <?= $order['orderStatus']=='Delivered'?'selected':'' ?>>Delivered</option>
                                                                    <option value="Cancelled" <?= $order['orderStatus']=='Cancelled'?'selected':'' ?>>Cancelled</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="fontkink1">Note:</td>
                                                    <td class="fontkink" align="justify">
                                                        <textarea cols="50" rows="4" name="remark" placeholder="Add any additional notes for this status change..."></textarea>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="fontkink1">&nbsp;</td>
                                                    <td class="fontkink">
                                                        <button type="submit" name="submit2" class="btn btn-primary">
                                                            <i class="icon-ok icon-white"></i> Update Status
                                                        </button>
                                                        <button type="button" class="btn btn-danger" onclick="window.close();">
                                                            <i class="icon-remove icon-white"></i> Close
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="2" class="alert alert-success">
                                                        <i class="icon-ok icon-white"></i> This order has been delivered.
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        </table>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include('include/footer.php'); ?>

    <script src="scripts/jquery-1.9.1.min.js" type="text/javascript"></script>
    <script src="scripts/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
    <script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
</body>
</html>