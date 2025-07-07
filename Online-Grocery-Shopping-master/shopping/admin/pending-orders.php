<?php
session_start();
include('include/config.php');
if(strlen($_SESSION['alogin']) == 0) {    
    header('location:index.php');
    exit;
}

date_default_timezone_set('Asia/Kolkata');
$currentTime = date('d-m-Y h:i:s A', time());

// Delete order if requested
if (isset($_GET['del'])) {
    mysqli_query($con,"delete from orders where id='".$_GET['del']."'");
    $_SESSION['delmsg']="Order deleted successfully";
}

// Assign delivery boy if requested
if (isset($_POST['assign_delivery'])) {
    $order_id = intval($_POST['order_id']);
    $delivery_boy_id = intval($_POST['delivery_boy']);
    
    if($order_id > 0 && $delivery_boy_id > 0) {
        // Start transaction
        mysqli_begin_transaction($con);
        
        try {
            // Update order status and assign delivery boy
            $update_query = "UPDATE orders 
                           SET orderStatus='Assigned', 
                               delivery_boy_id = $delivery_boy_id 
                           WHERE id = $order_id";
            
            if(!mysqli_query($con, $update_query)) {
                throw new Exception("Order update failed: ".mysqli_error($con));
            }
            
            // Add to track history
            $history_query = "INSERT INTO ordertrackhistory(orderId, status, remark, postingDate) 
                            VALUES($order_id, 'Assigned', 'Assigned to delivery boy', NOW())";
            
            if(!mysqli_query($con, $history_query)) {
                throw new Exception("History update failed: ".mysqli_error($con));
            }
            
            mysqli_commit($con);
            $_SESSION['msg'] = "Order successfully assigned to delivery boy";
            
            // Debug log
            error_log("Order $order_id assigned to delivery boy $delivery_boy_id");
            
        } catch (Exception $e) {
            mysqli_rollback($con);
            $_SESSION['msg'] = "Error: ".$e->getMessage();
            error_log("Assignment error: ".$e->getMessage());
        }
    } else {
        $_SESSION['msg'] = "Invalid order or delivery boy selection";
    }
    
    header("Location: pending-orders.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Pending Orders</title>
    <link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    <link type="text/css" href="css/theme.css" rel="stylesheet">
    <link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
    <link type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600' rel='stylesheet'>
    <style>
        .table-responsive {
            overflow-x: auto;
            width: 100%;
        }
        .datatable-1 {
            min-width: 1200px;
            display: table !important;
        }
        .no-orders {
            padding: 15px;
            text-align: center;
            font-weight: bold;
            color: #666;
        }
        .delivery-boy-select {
            min-width: 150px;
        }
        .assign-btn {
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
                            <h3>Pending Orders</h3>
                        </div>
                        <div class="module-body">
                            <?php if(isset($_GET['del'])) { ?>
                                <div class="alert alert-error">
                                    <button type="button" class="close" data-dismiss="alert">×</button>
                                    <strong>Oh snap!</strong> <?php echo htmlentities($_SESSION['delmsg']); ?>
                                    <?php unset($_SESSION['delmsg']); ?>
                                </div>
                            <?php } ?>
                            
                            <?php if(isset($_SESSION['msg'])) { ?>
                                <div class="alert alert-success">
                                    <button type="button" class="close" data-dismiss="alert">×</button>
                                    <?php echo htmlentities($_SESSION['msg']); ?>
                                    <?php unset($_SESSION['msg']); ?>
                                </div>
                            <?php } ?>

                            <br />

                            <?php
$query = mysqli_query($con, "SELECT 
o.id, o.order_number, o.orderDate, o.final_amount, o.orderStatus, o.delivery_boy_id,
u.name AS username,
u.email AS useremail,
u.contactno AS usercontact,
u.shippingAddress, 
u.shipping_building, 
u.shipping_house, 
u.shipping_landmark,
p.productName,
oi.quantity,
p.productPrice,
oi.shippingCharge
FROM orders o
JOIN users u ON o.userId = u.id
JOIN order_items oi ON o.id = oi.orderId
JOIN products p ON oi.productId = p.id
WHERE o.orderStatus = 'Pending'
ORDER BY o.orderDate DESC");
                            
                            if(mysqli_num_rows($query) > 0) { ?>
                                <div class="table-responsive">
                                    <table id="ordersTable" cellpadding="0" cellspacing="0" border="0" class="datatable-1 table table-bordered table-striped display">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Email / Contact</th>
                                                <th>Shipping Address</th>
                                                <th>Product</th>
                                                <th>Qty</th>
                                                <th>Amount</th>
                                                <th>Order Date</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $cnt = 1;
                                            while($row = mysqli_fetch_array($query)) {
                                                $amount = $row['quantity'] * $row['productPrice'] + $row['shippingCharge'];
                                                $address = $row['shippingAddress'].', '.$row['shipping_house'].', '.$row['shipping_building'];
                                            ?>
                                                <tr>
                                                    <td><?php echo $cnt; ?></td>
                                                    <td><?php echo htmlentities($row['username']); ?></td>
                                                    <td><?php echo htmlentities($row['useremail']); ?><br><?php echo htmlentities($row['usercontact']); ?></td>
                                                    <td><?php echo htmlentities($address); ?></td>
                                                    <td><?php echo htmlentities($row['productName']); ?></td>
                                                    <td><?php echo htmlentities($row['quantity']); ?></td>
                                                    <td>₹<?php echo number_format($amount, 2); ?></td>
                                                    <td><?php echo htmlentities($row['orderDate']); ?></td>
                                                    <td><?php echo $row['orderStatus'] ? htmlentities($row['orderStatus']) : 'Pending'; ?></td>
                                        
                                                    <td>
                                                        <a href="updateorder.php?oid=<?php echo $row['id']; ?>" title="Update order">
                                                            <i class="icon-edit"></i>
                                                        </a>
                                                        <?php if($row['delivery_boy_id']) { ?>
                                                            <a href="track-delivery.php?order_id=<?php echo $row['id']; ?>" title="Track Delivery" style="margin-left: 5px;">
                                                                <i class="icon-map-marker"></i>
                                                            </a>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                            <?php $cnt++; } ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php } else { ?>
                                <div class="no-orders">
                                    <p>No pending orders found</p>
                                    <p><small>All orders may have been processed or marked as delivered.</small></p>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div><!--/.content-->
            </div><!--/.span9-->
        </div>
    </div><!--/.container-->
</div><!--/.wrapper-->

<?php include('include/footer.php');?>

<script src="scripts/jquery-1.9.1.min.js"></script>
<script src="scripts/jquery-ui-1.10.1.custom.min.js"></script>
<script src="bootstrap/js/bootstrap.min.js"></script>
<script src="scripts/datatables/jquery.dataTables.js"></script>

<script>
    $(document).ready(function() {
        console.log("Document is ready. Initializing DataTables...");

        setTimeout(function() {
            if ($.fn.DataTable.isDataTable('#ordersTable')) {
                $('#ordersTable').DataTable().destroy();
            }

            $('#ordersTable').DataTable({
                "scrollX": true,
                "pageLength": 25,
                "order": [[7, "desc"]], // Sort by Order Date Descending
                "autoWidth": false,
                "destroy": true
            });

            console.log("DataTables initialized successfully!");
        }, 500);
    });
</script>
</body>
</html>