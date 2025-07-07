<?php
session_start();
include('include/config.php');
if(strlen($_SESSION['alogin'])==0) { 
    header('location:index.php');
}
else{
    date_default_timezone_set('Asia/Kolkata');
    $currentTime = date('d-m-Y h:i:s A', time());
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin| Today's Orders</title>
    <link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    <link type="text/css" href="css/theme.css" rel="stylesheet">
    <link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
    <link type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="scripts/datatables/jquery.dataTables.min.js"></script>
    
    <style>
        /* Table container */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            margin-bottom: 20px;
        }
        
        /* Table styling */
        .datatable-1 {
            min-width: 1200px;
            display: table !important;
        }
        
        /* Order grouping style */
        #ordersTable{
            text-align:center;
        }
        .order-group {
            background-color: #f9f9f9;
            border-left: 4px solid #428bca;
        }
        
        .order-group td {
            font-weight: bold;
        }
        
        .order-item td {
            padding-left: 30px !important;
        }
        
        .order-number {
            font-weight: bold;
            color: #428bca;
        }
        
        /* Status badges */
        .status-badge {
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .status-pending {
            background-color: #f0ad4e;
            color: white;
        }
        
        .status-processing {
            background-color: #5bc0de;
            color: white;
        }
        
        .status-completed {
            background-color: #5cb85c;
            color: white;
        }
        
        .status-cancelled {
            background-color: #d9534f;
            color: white;
        }
        
        .status-shipped {
            background-color: #337ab7;
            color: white;
        }
        
        .status-out-for-delivery {
            background-color: #8e44ad;
            color: white;
        }
        
        .status-delivered {
            background-color: #2ecc71;
            color: white;
        }
        
        /* Alert messages */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        
        .alert-success {
            color: #3c763d;
            background-color: #dff0d8;
            border-color: #d6e9c6;
        }
        
        .alert-error {
            color: #a94442;
            background-color: #f2dede;
            border-color: #ebccd1;
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
                        <?php if(isset($_SESSION['success_msg'])): ?>
                            <div class="alert alert-success">
                                <?php echo $_SESSION['success_msg']; unset($_SESSION['success_msg']); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if(isset($_SESSION['error_msg'])): ?>
                            <div class="alert alert-error">
                                <?php echo $_SESSION['error_msg']; unset($_SESSION['error_msg']); ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="module">
                            <div class="module-head">
                                <h3>Today's Orders</h3>
                            </div>
                            <div class="module-body">
                            
                                <div class="table-responsive">
                                    <table id="ordersTable" class="datatable-1 table table-bordered table-striped display">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Order No.</th>
                                                <th>Customer</th>
                                                <th>Contact</th>
                                                <th>Shipping Address</th>
                                                <th>Products</th>
                                                <th>Qty</th>
                                                <th>Price</th>
                                                <th>Total</th>
                                                <th>Status</th>
                                                <th>Order Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $f1 = "00:00:00";
                                            $from = date('Y-m-d')." ".$f1;
                                            $t1 = "23:59:59";
                                            $to = date('Y-m-d')." ".$t1;
                                            
                                            // First get all orders
                                            $ordersQuery = mysqli_query($con, "SELECT 
                                                o.id, o.order_number, o.orderDate, o.final_amount, o.orderStatus,
                                                u.name AS username,
                                                u.email AS useremail,
                                                u.contactno AS usercontact,
                                                u.shippingAddress, 
                                                u.shipping_building, 
                                                u.shipping_house, 
                                                u.shipping_landmark
                                            FROM orders o
                                            JOIN users u ON o.userId = u.id
                                            WHERE o.orderDate BETWEEN '$from' AND '$to'
                                            ORDER BY o.orderDate DESC");
                                            
                                            $cnt = 1;
                                            $prev_order_id = null;
                                            
                                            while($order = mysqli_fetch_array($ordersQuery)) {
                                                $orderId = $order['id'];
                                                
                                                // Get all items for this order
                                                $itemsQuery = mysqli_query($con, "SELECT 
                                                    p.productName,
                                                    oi.quantity,
                                                    p.productPrice,
                                                    oi.shippingCharge
                                                FROM order_items oi
                                                JOIN products p ON oi.productId = p.id
                                                WHERE oi.orderId = '$orderId'");
                                                
                                                $firstItem = true;
                                                $itemCount = mysqli_num_rows($itemsQuery);
                                                
                                                while($item = mysqli_fetch_array($itemsQuery)) {
                                                    $statusClass = 'status-' . strtolower(str_replace(' ', '-', $order['orderStatus']));
                                            ?>
                                                    <tr class="<?php echo $firstItem ? 'order-group' : 'order-item'; ?>">
                                                        <td><?php echo $firstItem ? htmlentities($cnt) : ''; ?></td>
                                                        <td>
                                                            <?php if($firstItem) { ?>
                                                                <span class="order-number"><?php echo htmlentities($order['order_number']); ?></span>
                                                            <?php } ?>
                                                        </td>
                                                        <td><?php echo $firstItem ? htmlentities($order['username']) : ''; ?></td>
                                                        <td><?php echo $firstItem ? htmlentities($order['usercontact']) : ''; ?></td>
                                                        <td><?php echo $firstItem ? htmlentities($order['shippingAddress'].", ".$order['shipping_building'].", ".$order['shipping_house']) : ''; ?></td>
                                                        <td><?php echo htmlentities($item['productName']); ?></td>
                                                        <td><?php echo htmlentities($item['quantity']); ?></td>
                                                        <td><?php echo htmlentities($item['productPrice']); ?></td>
                                                        <td><?php echo $firstItem ? htmlentities($order['final_amount']) : ''; ?></td>
                                                        <td>
                                                            <?php if($firstItem) { ?>
                                                                <span class="status-badge <?php echo $statusClass; ?>">
                                                                    <?php echo htmlentities($order['orderStatus']); ?>
                                                                </span>
                                                            <?php } ?>
                                                        </td>
                                                        <td><?php echo $firstItem ? htmlentities($order['orderDate']) : ''; ?></td>
                                                        <td>
                                                            <?php if($firstItem) { ?>
                                                                <a href="updateorder.php?oid=<?php echo htmlentities($order['id']); ?>" title="Update order" target="_blank"><i class="icon-edit"></i></a>
                                                            <?php } ?>
                                                        </td>
                                                    </tr>
                                            <?php 
                                                    $firstItem = false;
                                                }
                                                $cnt++;
                                            } 
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
$(document).ready(function() {
    // Initialize DataTables with both scrollbars
    var table = $('#ordersTable').DataTable({
        "scrollX": true,
        "scrollY": "500px",
        "scrollCollapse": true,
        "paging": true,
        "pageLength": 10,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "dom": '<"top"lf>rt<"bottom"ip>',
        "language": {
            "paginate": {
                "first": "First",
                "previous": "Previous",
                "next": "Next",
                "last": "Last"
            },
            "search": "_INPUT_",
            "searchPlaceholder": "Search records..."
        },
        "columnDefs": [
            { "orderable": false, "targets": [0, 1, 2, 3, 4, 8, 9, 10, 11] }
        ]
    });
});
</script>

</body>
<?php } ?>