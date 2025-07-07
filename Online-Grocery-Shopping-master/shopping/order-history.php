<?php 
session_start();
error_reporting(0);
include('includes/config.php');

// Check if user is logged in
if(strlen($_SESSION['login']) == 0) {   
    header('location:login.php');
    exit();
}

// Include necessary libraries for notifications
$toastrCSS = '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">';
$sweetalertCSS = '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">';
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
        <title>Order History - Online Grocery Store</title>
        
        <!-- CSS Files -->
        <link rel="stylesheet" href="assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="assets/css/main.css">
        <link rel="stylesheet" href="assets/css/green.css">
        <link rel="stylesheet" href="assets/css/owl.carousel.css">
        <link rel="stylesheet" href="assets/css/owl.transitions.css">
        <link href="assets/css/lightbox.css" rel="stylesheet">
        <link rel="stylesheet" href="assets/css/animate.min.css">
        <link rel="stylesheet" href="assets/css/rateit.css">
        <link rel="stylesheet" href="assets/css/bootstrap-select.min.css">
        <link rel="stylesheet" href="assets/css/font-awesome.min.css">
        <?php echo $toastrCSS; ?>
        <?php echo $sweetalertCSS; ?>
        
        <!-- Fonts -->
        <link href='https://fonts.googleapis.com/css?family=Roboto:300,400,500,700' rel='stylesheet' type='text/css'>
        
        <!-- Favicon -->
        <link rel="shortcut icon" href="assets/images/favicon.ico">
        
        <style>
            .order-status {
                padding: 5px 10px;
                border-radius: 4px;
                font-weight: bold;
            }
            .status-pending {
                background-color: #fff3cd;
                color: #856404;
            }
            .status-processing {
                background-color: #cce5ff;
                color: #004085;
            }
            .status-shipped {
                background-color: #d4edda;
                color: #155724;
            }
            .status-delivered {
                background-color: #28a745;
                color: white;
            }
            .status-cancelled {
                background-color: #dc3545;
                color: white;
            }
            .thank-you-img {
                max-width: 100%;
                height: auto;
                display: block;
                margin: 0 auto;
            }
            .order-action-btn {
                padding: 5px 10px;
                border-radius: 4px;
                font-size: 14px;
            }
            .table-responsive {
                overflow-x: auto;
            }
            .order-highlight {
                background-color: #f8f9fa;
                transition: all 0.3s;
            }
            .order-highlight:hover {
                background-color: #e9ecef;
            }

            .order-header {
                background-color: #e9ecef;
                font-weight: bold;
            }
            .order-group-end {
                background-color: #f8f9fa;
    height: 10px;
    box-shadow: inset 0 5px 5px -5px rgba(0,0,0,0.1);
    
}
/* Table styling */
.table {
    border-collapse: collapse;
    width: 100%;
    margin-bottom: 1rem;
    background-color: transparent;
    border: 1px solid #dee2e6;
}

.table-bordered {
    border: 1px solid #dee2e6;
}

.table-bordered th,
.table-bordered td {
    border: 1px solid #dee2e6;
    padding: 12px;
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.075);
}

/* Header styling */
.thead-dark th {


    border-color: #454d55;
}

/* Order group styling */
.order-group {

    background-color: #f8f9fa;
}

/* Remove double borders between cells */
.table-bordered thead th,
.table-bordered thead td {
    border-bottom-width: 2px;
}

/* Add subtle shadow to table */
.table-responsive {
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
    border-radius: 4px;
    overflow: hidden;
}

/* Zebra striping for better readability */
.table-striped tbody tr:nth-of-type(odd) {
    background-color: rgba(0, 0, 0, 0.02);
}

/* Status badges */
.order-status {
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    display: inline-block;
    min-width: 80px;
    text-align: center;
}
.order-group + .order-group {
    border-top: 1px solid #78797b;
}
.table tbody tr td.text-center {
    border: 1px solid #dee2e6;
}
@media (max-width: 768px) {
    .table-responsive {
        border: 0;
    }
    .table thead {
        display: none;
    }
    .table tr {
        display: block;
        margin-bottom: 15px;
        border: 1px solid #dee2e6;
    }
    .table td {
        display: block;
        text-align: right;
        border-bottom: 1px solid #dee2e6;
    }
    .table td:before {
        content: attr(data-label);
        float: left;
        font-weight: bold;
    }
}

            
        </style>
    </head>
    <body class="cnt-home">
        <!-- ============================================== HEADER ============================================== -->
        <header class="header-style-1">
            <?php include('includes/top-header.php');?>
            <?php include('includes/main-header.php');?>
            <?php include('includes/menu-bar.php');?>
        </header>
        <!-- ============================================== HEADER : END ============================================== -->
        
        <div class="breadcrumb">
            <div class="container">
                <div class="breadcrumb-inner">
                    <ul class="list-inline list-unstyled">
                        <li><a href="index.php">Home</a></li>
                        <li class='active'>Order History</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="body-content outer-top-xs">
            <div class="container">
                <?php 
                // Check for success/error messages in URL
                if(isset($_GET['success'])) {
                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                            '.htmlspecialchars($_GET['success']).'
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                          </div>';
                }
                if(isset($_GET['error'])) {
                    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                            '.htmlspecialchars($_GET['error']).'
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                          </div>';
                }
                ?>
                
                <div class="row inner-bottom-sm">
                    <div class="col-md-12">
                        <h2 class="text-center mb-4">Your Order History</h2>
                        
                        <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Order ID</th>
                                        <th>Products</th>
                                        <th>Total Items</th>
                                        <th>Order Total</th>
                                        <th>Payment</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php 
// Get orders for the user
$orderQuery = mysqli_query($con, "SELECT 
    o.id as orderNumber, 
    o.order_number,
    o.paymentMethod as paym, 
    o.orderDate as odate, 
    o.orderStatus as status, 
    o.final_amount as grandTotal,
    COUNT(oi.id) as itemCount
FROM orders o
LEFT JOIN order_items oi ON o.id = oi.orderId
WHERE o.userId='".$_SESSION['id']."' 
AND o.is_grouped = 1
AND o.paymentMethod IS NOT NULL
GROUP BY o.id
ORDER BY o.orderDate DESC");

if(mysqli_num_rows($orderQuery) > 0) {
    $cnt = 1;
    while($order = mysqli_fetch_array($orderQuery)) {
        $statusClass = getStatusClass($order['status']);
        
        // Get products for this order
        $productsQuery = mysqli_query($con, "SELECT 
            p.productImage1 as pimg1, 
            p.productName as pname, 
            p.id as proid, 
            oi.quantity as qty, 
            p.productPrice as pprice
        FROM order_items oi 
        JOIN products p ON oi.productId=p.id 
        WHERE oi.orderId='".$order['orderNumber']."'");
        
        $totalItems = 0;
        $products = [];
        while($product = mysqli_fetch_array($productsQuery)) {
            $products[] = $product;
            $totalItems += $product['qty'];
        }
?>
<tr class="order-highlight order-group order-group-end">

    <td><?php echo $cnt; ?></td>
    <td><?php echo htmlspecialchars($order['orderNumber']); ?></td>
    <td>
        <div class="product-list">
            <?php foreach($products as $index => $product): ?>
                <div class="d-flex align-items-center mb-2">
                    <img src="admin/productimages/<?php echo htmlspecialchars($product['proid']);?>/<?php echo htmlspecialchars($product['pimg1']);?>" 
                         alt="<?php echo htmlspecialchars($product['pname']); ?>" 
                         width="40" class="mr-2">
                    <div>
                        <a href="product-details.php?pid=<?php echo htmlspecialchars($product['proid']);?>">
                            <?php echo htmlspecialchars($product['pname']); ?>
                        </a>
                        <div class="text-muted small">Qty: <?php echo htmlspecialchars($product['qty']); ?></div>
                    </div>
                </div>
                <?php if($index < count($products) - 1): ?>
                <hr class="my-2">
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </td>
    <td><?php echo $totalItems; ?></td>
    <td>₹<?php echo number_format($order['grandTotal'], 2); ?></td>
    <td><?php echo htmlspecialchars(ucfirst($order['paym'])); ?></td>
    <td><?php echo date('M d, Y h:i A', strtotime($order['odate'])); ?></td>
    <td>
        <span class="order-status <?php echo $statusClass; ?>">
            <?php echo htmlspecialchars(ucfirst($order['status'])); ?>
        </span>
    </td>
    <td>
                                            <button onclick="trackOrder('<?php echo $order['orderNumber']; ?>')" 
                                                    class="btn btn-sm btn-primary order-action-btn">
                                                <i class="fa fa-truck"></i> Track Order
                                            </button><br/><br/>
                                            <?php if($order['status'] == 'pending' || $order['status'] == 'processing'): ?>
                                            <button onclick="cancelOrder('<?php echo $order['orderNumber']; ?>')" 
                                                    class="btn btn-sm btn-danger order-action-btn mt-1">
                                                <i class="fa fa-times"></i> Cancel Order
                                            </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php 
                                            $cnt++;
                                        }
                                    } else {
                                    ?>
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <div class="alert alert-info">
                                                <i class="fa fa-info-circle"></i> You haven't placed any orders yet.
                                            </div>
                                            <a href="index.php" class="btn btn-primary">Start Shopping</a>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <?php if(mysqli_num_rows($orderQuery) > 0): ?>
                <div class="row mt-4">
                    <div class="col-md-12 text-center">
                        <img src="https://www.knowband.com/blog/wp-content/uploads/2020/03/THANKYOU-PAGE-2.png" 
                             alt="Thank you" class="thank-you-img" style="max-width: 400px;">
                        <h3 class="mt-3">Thank you for shopping with us!</h3>
                        <div class="mt-3">
                            <h4 style="color: #007bff;">STAY HOME STAY SAFE</h4>
                            <h4 style="color: #28a745;">Keep Shopping With OnlineGroceryStore.in</h4>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <?php include('includes/footer.php');?>

        <!-- JavaScript Libraries -->
        <script src="assets/js/jquery-1.11.1.min.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>
        <script src="assets/js/bootstrap-hover-dropdown.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        
        <script>
        // Initialize Toastr
        toastr.options = {
            "closeButton": true,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        // Check for URL parameters to show notifications
        $(document).ready(function() {
            const urlParams = new URLSearchParams(window.location.search);
            
            if(urlParams.has('success')) {
                toastr.success(urlParams.get('success'));
            }
            
            if(urlParams.has('error')) {
                toastr.error(urlParams.get('error'));
            }
        });

        // Track order function
        function trackOrder(orderNumber) {
            window.open('track-order.php?oid=' + orderNumber, '_blank');

        }

        // Cancel order function
        function cancelOrder(orderNumber) {
            Swal.fire({
                title: 'Confirm Order Cancellation',
                text: "Are you sure you want to cancel this entire order?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, cancel order!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // AJAX call to cancel order
                    $.ajax({
                        url: 'cancel-order.php',
                        type: 'POST',
                        data: { order_number: orderNumber },
                        dataType: 'json',
                        success: function(response) {
                            if(response.success) {
                                toastr.success(response.message);
                                // Reload after 2 seconds
                                setTimeout(function() {
                                    location.reload();
                                }, 2000);
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function() {
                            toastr.error('An error occurred while processing your request.');
                        }
                    });
                }
            });
        }
        </script>
    </body>
</html>

<?php
// Helper function to get status class
function getStatusClass($status) {
    switch(strtolower($status)) {
        case 'pending': return 'status-pending';
        case 'processing': return 'status-processing';
        case 'shipped': return 'status-shipped';
        case 'delivered': return 'status-delivered';
        case 'cancelled': return 'status-cancelled';
        default: return '';
    }
}
?>