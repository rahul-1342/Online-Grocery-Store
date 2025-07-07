<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['login']) == 0) {   
    header('location:login.php');
    exit;
}

// Code for Product deletion from wishlist
if(isset($_GET['del'])) {
    $wid = intval($_GET['del']);
    $query = mysqli_query($con, "DELETE FROM wishlist WHERE id='$wid' AND userId='".$_SESSION['id']."'");
}

if(isset($_GET['action']) && $_GET['action'] == "add") {
    $id = intval($_GET['id']);
    $query = mysqli_query($con, "DELETE FROM wishlist WHERE productId='$id' AND userId='".$_SESSION['id']."'");
    
    if(isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['quantity']++;
    } else {
        $sql_p = "SELECT * FROM products WHERE id={$id}";
        $query_p = mysqli_query($con, $sql_p);
        if(mysqli_num_rows($query_p) != 0) {
            $row_p = mysqli_fetch_array($query_p);
            $_SESSION['cart'][$row_p['id']] = array(
                "quantity" => 1, 
                "price" => $row_p['productPrice']
            );    
            $_SESSION['toast_message'] = "Item added to cart!";
header('location:my-wishlist.php');
exit;

        } else {
            $message = "Product ID is invalid";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="keywords" content="MediaCenter, Template, eCommerce">
    <meta name="robots" content="all">
    <title>My Wishlist</title>
    
    <!-- CSS -->
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
    <link href='http://fonts.googleapis.com/css?family=Roboto:300,400,500,700' rel='stylesheet' type='text/css'>
    <link rel="shortcut icon" href="assets/images/favicon.ico">
    <style>
        .table .product-name a {
    font-size: 16px;
    font-weight: 500;
}

.table .price {
    font-size: 15px;
    font-weight: bold;
    margin-top: 5px;
}

.table td {
    vertical-align: middle !important;
}
</style>
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
                    <li class='active'>Wishlist</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="body-content outer-top-bd">
        <div class="container">
            <div class="my-wishlist-page inner-bottom-sm">
                <div class="row">
                    <div class="col-md-12 my-wishlist">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th colspan="4">My Wishlist</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $ret = mysqli_query($con, "SELECT products.productName as pname, products.id as pid, 
                                            products.productImage1 as pimage, products.productPrice as pprice, 
                                            wishlist.id as wid FROM wishlist 
                                            JOIN products ON products.id = wishlist.productId 
                                            WHERE wishlist.userId = '".$_SESSION['id']."'");
                                    $num = mysqli_num_rows($ret);
                                    
                                    if($num > 0) {
                                        while ($row = mysqli_fetch_array($ret)) {
                                    ?>
                                    <tr>
                                        <td class="col-md-2">
                                        <img src="admin/productimages/<?php echo htmlentities($row['pid']);?>/<?php echo htmlentities($row['pimage']);?>" 
     alt="<?php echo htmlentities($row['pname']);?>" width="60" height="80">

                                        </td>
                                        <td class="col-md-6">
                                            <div class="product-name">
                                                <a href="product-details.php?pid=<?php echo htmlentities($row['pid']);?>">
                                                    <?php echo htmlentities($row['pname']);?>
                                                </a>
                                            </div>
                                            <?php 
                                            $rt = mysqli_query($con, "SELECT * FROM productreviews WHERE productId='".$row['pid']."'");
                                            $review_count = mysqli_num_rows($rt);
                                            if($review_count > 0) {
                                            ?>
                                            <div class="rating">
                                                <i class="fa fa-star rate"></i>
                                                <i class="fa fa-star rate"></i>
                                                <i class="fa fa-star rate"></i>
                                                <i class="fa fa-star rate"></i>
                                                <i class="fa fa-star non-rate"></i>
                                                <span class="review">(<?php echo $review_count; ?> Reviews)</span>
                                            </div>
                                            <?php } ?>
                                            <div class="price">
                                                Rs. <?php echo htmlentities($row['pprice']);?>.00
                                            </div>
                                        </td>
                                        <td class="col-md-2">
                                            <a href="my-wishlist.php?action=add&id=<?php echo $row['pid']; ?>" 
                                               class="btn-upper btn btn-primary">Add to cart</a>
                                        </td>
                                        <td class="col-md-2 close-btn">
                                        <a href="#" class="delete-wishlist" data-id="<?php echo htmlentities($row['wid']); ?>">
    <i class="fa fa-times text-danger"></i>
</a>

                                        </td>
                                    </tr>
                                    <?php 
                                        } 
                                    } else { 
                                    ?>
                                    <tr>
                                        <td colspan="4" style="font-size: 18px; font-weight:bold; text-align: center;">
                                            Your Wishlist is Empty
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
              
        </div>
    </div>

    <?php include('includes/footer.php');?>

    <!-- JavaScript -->
    <script src="assets/js/jquery-1.11.1.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
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
    <!-- Add SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.querySelectorAll('.delete-wishlist').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const wid = this.getAttribute('data-id');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to undo this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `my-wishlist.php?del=${wid}`;
                }
            });
        });
    });
</script>
<?php if(isset($_SESSION['toast_message'])) { ?>
<script>
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: '<?php echo $_SESSION['toast_message']; ?>',
        showConfirmButton: false,
        timer: 2000
    });
</script>
<?php unset($_SESSION['toast_message']); } ?>

</body>
</html>