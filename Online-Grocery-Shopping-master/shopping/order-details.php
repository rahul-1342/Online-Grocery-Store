<?php 
session_start();
error_reporting(0);
include('includes/config.php');
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
        <title>Order History</title>
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
        <!-- Add Toastr CSS -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
        <!-- Add SweetAlert CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
        <link href='http://fonts.googleapis.com/css?family=Roboto:300,400,500,700' rel='stylesheet' type='text/css'>
        <link rel="shortcut icon" href="assets/images/favicon.ico">
        
        <script>
        function openTrackWindow(url) {
            window.open(url, '_blank');
            return false;
        }
        </script>
    </head>
    <body class="cnt-home">
        <header class="header-style-1">
            <?php include('includes/top-header.php');?>
            <?php include('includes/main-header.php');?>
            <?php include('includes/menu-bar.php');?>
        </header>

        <div class="breadcrumb">
            <div class="container">
                <div class="breadcrumb-inner">
                    <ul class="list-inline list-unstyled">
                        <li><a href="index.php">Home</a></li>
                        <li class='active'>Order Details</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="body-content outer-top-xs">
            <div class="container">
                <div class="row inner-bottom-sm">
                    <div class="col-md-12 shopping-cart">
                        <div class="table-responsive">
                            <form name="orderhistory" method="post" id="orderHistoryForm">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Image</th>
                                            <th>Product Name</th>
                                            <th>Quantity</th>
                                            <th>Price Per Unit</th>
                                            <th>Grand Total</th>
                                            <th>Payment Method</th>
                                            <th>Order Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $showError = false;
                                        if(isset($_POST['orderid']) && isset($_POST['email'])) {
                                            $orderid = intval($_POST['orderid']);
                                            $email = mysqli_real_escape_string($con, $_POST['email']);
                                            
                                            $ret = mysqli_query($con, "SELECT t.email, t.id FROM (SELECT usr.email, odrs.id FROM users AS usr JOIN orders AS odrs ON usr.id=odrs.userId) AS t WHERE t.email='$email' AND t.id='$orderid'");
                                            
                                            if(mysqli_num_rows($ret) > 0) {
                                                $query = mysqli_query($con, "SELECT products.productImage1 AS pimg1, products.productName AS pname, orders.productId AS opid, orders.quantity AS qty, products.productPrice AS pprice, orders.paymentMethod AS paym, orders.orderDate AS odate, orders.id AS orderid FROM orders JOIN products ON orders.productId=products.id WHERE orders.id='$orderid' AND orders.paymentMethod IS NOT NULL");
                                                
                                                $cnt = 1;
                                                $hasResults = false;
                                                while($row = mysqli_fetch_array($query)) {
                                                    $hasResults = true;
                                                    $price = $row['pprice'];
                                                    $qty = $row['qty'];
                                                    $total = $qty * $price;
                                        ?>
                                        <tr>
                                            <td><?php echo $cnt;?></td>
                                            <td class="cart-image">
                                                <a class="entry-thumbnail" href="product-details.php?pid=<?php echo $row['opid'];?>">
                                                    <?php 
                                                    $imagePath = "admin/productimages/".htmlentities($row['opid'])."/".htmlentities($row['pimg1']);
                                                    if(file_exists($imagePath) && !empty($row['pimg1'])): ?>
                                                        <img src="<?php echo $imagePath; ?>" alt="<?php echo htmlentities($row['pname']); ?>" width="84" height="146">
                                                    <?php else: ?>
                                                        <img src="assets/images/default-product.jpg" alt="Default Product Image" width="84" height="146">
                                                    <?php endif; ?>
                                                </a>
                                            </td>
                                            <td>
                                                <a href="product-details.php?pid=<?php echo $row['opid'];?>">
                                                    <?php echo htmlentities($row['pname']);?>
                                                </a>
                                            </td>
                                            <td><?php echo htmlentities($qty); ?></td>
                                            <td>Rs. <?php echo htmlentities($price); ?></td>
                                            <td>Rs. <?php echo htmlentities($total); ?></td>
                                            <td><?php echo htmlentities($row['paym']); ?></td>
                                            <td><?php echo htmlentities($row['odate']); ?></td>
                                            <td>
                                                <a href="track-order.php?oid=<?php echo htmlentities($row['orderid']); ?>" target="_blank" class="btn btn-primary btn-sm">Track</a>
                                            </td>
                                        </tr>
                                        <?php 
                                                    $cnt++;
                                                }
                                                
                                                if(!$hasResults) {
                                                    $showError = true;
                                                    $errorMsg = "No order details found for this order ID";
                                                }
                                            } else {
                                                $showError = true;
                                                $errorMsg = "Either order ID or registered email is invalid";
                                            }
                                        } else {
                                            $showError = true;
                                            $errorMsg = "Please enter order details to view history";
                                        } 
                                        
                                        if($showError) {
                                        ?>
                                        <tr>
                                            <td colspan="9" class="text-center"><?php echo $errorMsg; ?></td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include('includes/footer.php');?>

        <script src="assets/js/jquery-1.11.1.min.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>
        <!-- Add Toastr JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        <!-- Add SweetAlert JS -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="assets/js/bootstrap-hover-dropdown.min.js"></script>
        <script src="assets/js/owl.carousel.min.js"></script>
        <script src="assets/js/echo.min.js"></script>
        <script src="assets/js/jquery.easing-1.3.min.js"></script>
        <script src="assets/js/bootstrap-slider.min.js"></script>
        <script src="assets/js/jquery.rateit.min.js"></script>
        <script src="assets/js/lightbox.min.js"></script>
        <script src="assets/js/bootstrap-select.min.js"></script>
        <script src="assets/js/wow.min.js"></script>
        <script src="assets/js/scripts.js"></script>
        
        <script>
        $(document).ready(function() {
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

            // Check if we should show an error message
            <?php if($showError): ?>
                toastr.error("<?php echo addslashes($errorMsg); ?>");
            <?php endif; ?>

            // Form submission handling
            $('#orderHistoryForm').on('submit', function(e) {
                var orderId = $('input[name="orderid"]').val().trim();
                var email = $('input[name="email"]').val().trim();
                
                if(!orderId || !email) {
                    toastr.error('Please fill in all required fields');
                    return false;
                }
                
                if(!isValidEmail(email)) {
                    toastr.error('Please enter a valid email address');
                    return false;
                }
                
                // Show loading indicator
                Swal.fire({
                    title: 'Loading Order Details',
                    html: 'Please wait while we retrieve your order information...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                return true;
            });
            
            // Function to validate email
            function isValidEmail(email) {
                var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return re.test(email);
            }
        });
        </script>
    </body>
</html>