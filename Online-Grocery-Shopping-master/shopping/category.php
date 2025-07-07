<?php
session_start();
error_reporting(0);
include('includes/config.php');
$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
if ($cid > 0) {
    $ret = mysqli_query($con, "SELECT * FROM products WHERE category = '$cid'");
    $num = mysqli_num_rows($ret);
} else {
    $ret = false;
    $num = 0;
}


// Add to cart functionality
if(isset($_GET['action']) && $_GET['action'] == "add") {
    $id = intval($_GET['id']);
    if(isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['quantity']++;
    } else {
        $sql_p = "SELECT * FROM products WHERE id = {$id}";
        $query_p = mysqli_query($con, $sql_p);
        if(mysqli_num_rows($query_p) != 0) {
            $row_p = mysqli_fetch_array($query_p);
            $_SESSION['cart'][$row_p['id']] = array(
                "quantity" => 1, 
                "price" => $row_p['productPrice']
            );
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 1500,
                        timerProgressBar: true
                    });
                    Toast.fire({
                        icon: 'success',
                        title: 'Added to cart'
                    });
                });
            </script>";
            

        } else {
            $message = "Product ID is invalid";
        }
    }
}

// Wishlist functionality
if(isset($_GET['pid']) && $_GET['action'] == "wishlist") {
    if(strlen($_SESSION['login']) == 0) {   
        $response = [
            'error' => 'Please login to add items to wishlist',
            'redirect' => 'login.php'
        ];
    } else {
        $userId = $_SESSION['id'];
        $check = mysqli_query($con, "SELECT * FROM wishlist WHERE userId='$userId' AND productId='".$_GET['pid']."'");
        
        if(mysqli_num_rows($check) == 0) {
            mysqli_query($con, "INSERT INTO wishlist(userId, productId) VALUES('$userId','".$_GET['pid']."')");
            $response = ['success' => true];
        } else {
            $response = ['error' => 'Product already in wishlist'];
        }
    }
    
    if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    } else {
        if(isset($response['success'])) {
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Added to wishlist!',
                        text: 'Product has been successfully added.',
                        confirmButtonText: 'Go to wishlist'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'my-wishlist.php';
                        }
                    });
                });
            </script>";
        } else {
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: '".$response['error']."',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed && '".(isset($response['redirect']) ? $response['redirect'] : '')."') {
                            window.location.href = '".$response['redirect']."';
                        }
                    });
                });
            </script>";
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
    <title>Product Category</title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
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
/* Main Layout Styles */
.category-product .row {
    display: flex;
    flex-wrap: wrap;
    margin: 0 -10px; /* Compensate for column padding */
}

.category-product .col-sm-6.col-md-4 {
    display: flex;
    margin-bottom: 30px;
    padding: 0 10px; /* Spacing between cards */
}

/* Product Card Container */
.category-product .products {
    width: 100%;
    padding: 15px;
    border: 1px solid #eee;
    border-radius: 5px;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    height: 100%;
    background: #fff;
}

.category-product .products:hover {
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    transform: translateY(-3px);
}

/* Product Image Section */
.product-image .image {
    height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 10px;
    overflow: hidden;
}

.product-image img {
    max-height: 100%;
    max-width: 100%;
    width: auto;
    transition: transform 0.3s ease;
}

.product:hover .product-image img {
    transform: scale(1.05);
}

/* Product Info Section */
.product-info {
    flex: 1;
    display: flex;
    flex-direction: column;
    padding: 10px 0;
}

.product-info .name {
    font-size: 14px;
    line-height: 1.4;
    margin: 0 0 5px 0;
    height: 40px;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    color: #333;
}

.product-info .name a {
    color: inherit;
    text-decoration: none;
}

.product-info .name a:hover {
    color: #336699;
}

.rating {
    margin: 5px 0;
    color: #ffb503; /* Star color */
}

/* Price Section */
.product-price {
    margin-top: auto;
    padding: 5px 0;
}

.product-price .price {
    font-size: 16px;
    font-weight: bold;
    color: #336699;
    display: block;
}

.product-price .price-before-discount {
    font-size: 12px;
    color: #999;
    text-decoration: line-through;
    display: block;
}

/* Action Buttons */
.cart {
    margin-top: 10px;
}

.cart .action {
    margin: 0;
}

.list-unstyled {
    display: flex;
    gap: 10px;
    margin: 0;
    padding: 0;
    list-style: none;
}

.add-cart-button {
    flex: 1;
}

.add-to-cart-btn {
    width: 100%;
    text-transform: none !important;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.add-to-wishlist-btn {
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 34px;
    width: 34px;
    font-size: 14px;
    border-radius: 4px !important;
}

/* Category Title */
.category-carousel .item {
    display: flex;
    align-items: center;
    min-height: 100px;
    margin-bottom: 20px;
}

.category-carousel .container-fluid {
    width: 100%;
}

.category-carousel .caption {
    padding: 20px 0;
}

.category-carousel .big-text {
    font-size: 36px;
    margin: 0;
    font-weight: 600;
    text-align: center;
    width: 100%;
    color: #333;
}

/* Responsive Adjustments */
@media (max-width: 767px) {
    .category-product .col-sm-6.col-md-4 {
        flex: 0 0 50%;
        max-width: 50%;
    }
    
    .product-image .image {
        height: 150px;
    }
    
    .category-carousel .big-text {
        font-size: 28px;
    }
}

@media (max-width: 480px) {
    .category-product .col-sm-6.col-md-4 {
        flex: 0 0 100%;
        max-width: 100%;
    }
    
    .product-info .name {
        height: auto;
        -webkit-line-clamp: 3;
    }
}

/* Out of Stock Style */
.action[style*="color:red"] {
    padding: 6px 12px;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 34px;
}
    </style>
</head>
<body class="cnt-home">
    <header class="header-style-1">
        <?php include('includes/top-header.php');?>
        <?php include('includes/main-header.php');?>
        <?php include('includes/menu-bar.php');?>
    </header>

    <div class="body-content outer-top-xs">
        <div class='container'>
            <div class='row outer-bottom-sm'>
                <div class='col-md-3 sidebar'>
                    <!-- ================================== TOP NAVIGATION ================================== -->
                    <div class="side-menu animate-dropdown outer-bottom-xs">       
                        <div class="head"><i class="icon fa fa-align-justify fa-fw"></i>Sub Categories</div>        
                        <nav class="yamm megamenu-horizontal" role="navigation">
                            <ul class="nav">
                                <?php 
                                $sql = mysqli_query($con, "SELECT id, subcategory FROM subcategory WHERE categoryid = '$cid'");
                                while($row = mysqli_fetch_array($sql)) {
                                ?>
                                <li class="dropdown menu-item">
                                    <a href="sub-category.php?scid=<?php echo $row['id'];?>" class="dropdown-toggle"><i class="icon fa fa-desktop fa-fw"></i>
                                    <?php echo $row['subcategory'];?></a>
                                </li>
                                <?php } ?>
                            </ul>
                        </nav>
                    </div>
                    
                    <!-- ================================== SHOP BY SECTION ================================== -->
                    <div class="sidebar-module-container">
                        <h3 class="section-title">shop by</h3>
                        <div class="sidebar-filter">
                            <!-- ================================== CATEGORY WIDGET ================================== -->
                            <div class="sidebar-widget wow fadeInUp outer-bottom-xs">
                                <div class="widget-header m-t-20">
                                    <h4 class="widget-title">Category</h4>
                                </div>
                                <div class="sidebar-widget-body m-t-10">
                                    <?php 
                                    $sql = mysqli_query($con, "SELECT id, categoryName FROM category");
                                    while($row = mysqli_fetch_array($sql)) {
                                    ?>
                                    <div class="accordion">
                                        <div class="accordion-group">
                                            <div class="accordion-heading">
                                                <a href="category.php?cid=<?php echo $row['id'];?>" class="accordion-toggle collapsed">
                                                    <?php echo $row['categoryName'];?>
                                                </a>
                                            </div>  
                                        </div>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <!-- ================================== CATEGORY WIDGET : END ================================== -->
                        </div>
                    </div>
                    <!-- ================================== SHOP BY SECTION : END ================================== -->
                </div>
                
                <div class='col-md-9'>
                    <!-- Category Title - Now smaller -->
                    <div class="category-carousel hidden-xs">
                        <div class="item">    
                            <div class="container-fluid">
                                <div class="vertical-top text-center">
                                    <?php 
                                    $sql = mysqli_query($con, "SELECT categoryName FROM category WHERE id = '$cid'");
                                    while($row = mysqli_fetch_array($sql)) {
                                    ?>
                                    <div class="big-text">
                                        <?php echo htmlentities($row['categoryName']);?>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product Grid -->
                    <div class="search-result-container">
                        <div id="myTabContent" class="tab-content">
                            <div class="tab-pane active" id="grid-container">
                                <div class="category-product inner-top-vs">
                                    <div class="row">
                                        <?php
                                        $ret = mysqli_query($con, "SELECT * FROM products WHERE category = '$cid'");
                                        $num = mysqli_num_rows($ret);
                                        if($num > 0) {
                                            while ($row = mysqli_fetch_array($ret)) {
                                        ?>
                                        <div class="col-sm-6 col-md-4">
                                            <div class="products">                
                                                <div class="product">        
                                                    <div class="product-image">
                                                        <div class="image">
                                                            <a href="product-details.php?pid=<?php echo htmlentities($row['id']);?>">
                                                                <img src="admin/productimages/<?php echo htmlentities($row['id']);?>/<?php echo htmlentities($row['productImage1']);?>" alt="<?php echo htmlentities($row['productName']);?>">
                                                            </a>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="product-info text-left">
                                                        <h3 class="name"><a href="product-details.php?pid=<?php echo htmlentities($row['id']);?>"><?php echo htmlentities($row['productName']);?></a></h3>
                                                        <div class="rating rateit-small"></div>
                                                        <div class="description"></div>

                                                        <div class="product-price">    
                                                            <span class="price">
                                                                Rs. <?php echo htmlentities($row['productPrice']);?>
                                                            </span>
                                                            <?php if($row['productPriceBeforeDiscount'] > 0) { ?>
                                                            <span class="price-before-discount">Rs. <?php echo htmlentities($row['productPriceBeforeDiscount']);?></span>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="cart clearfix animate-effect">
                                                        <div class="action">
                                                            <ul class="list-unstyled">
                                                                <li class="add-cart-button btn-group">
                                                                <?php if($row['productAvailability'] == 'In Stock'){ ?>
<button class="btn btn-primary add-to-cart-btn" type="button" data-product-id="<?php echo $row['id']; ?>">
    <i class="fa fa-shopping-cart"></i> Add to cart
</button>
<?php } else { ?>
<div class="action" style="color:red">Out of Stock</div>
<?php } ?>
<li class="add-cart-button btn-group">
    <button class="btn btn-primary add-to-wishlist-btn" type="button" title="Add to wishlist" data-product-id="<?php echo $row['id']; ?>">
        <i class="fa fa-heart"></i>
    </button>
</li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php 
                                            } 
                                        } else { 
                                        ?>
                                        <div class="col-sm-12">
                                            <h3>No Product Found</h3>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
              
        </div>
    </div>

    <?php include('includes/footer.php');?>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>

</script>


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
    <script>
$(document).ready(function() {
    // Handle add to cart button clicks
    $('.add-to-cart-btn').click(function() {
        var productId = $(this).data('product-id');
        
        $.ajax({
            url: 'add-to-cart.php',
            type: 'GET',
            data: { id: productId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // ✅ Update cart count
                    $('.basket-item-count .count').text(response.total_qty);

                    // ✅ Replace cart dropdown HTML
                    $('.top-cart-row').html(response.cart_html);

                    // ✅ Show SweetAlert toast
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Product added to cart',
                        showConfirmButton: false,
                        timer: 1500,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    });
                }
            },
            error: function() {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: 'Error adding product',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        });
    });
});
// Handle wishlist button clicks
$('.add-to-wishlist-btn').on('click', function(e) {
    e.preventDefault();
    var productId = $(this).data('product-id');
    var button = $(this);
    
    // Show loading state
    button.html('<i class="fa fa-spinner fa-spin"></i>');
    
    $.ajax({
        url: 'category.php',
        type: 'GET',
        data: { 
            action: 'wishlist',
            pid: productId 
        },
        dataType: 'json',
        success: function(response) {
            // Reset button state
            button.html('<i class="fa fa-heart"></i>');
            
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Added to wishlist!',
                    text: 'Product has been successfully added.',
                    confirmButtonText: 'Go to wishlist',
                    showCancelButton: true,
                    cancelButtonText: 'Continue shopping'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'my-wishlist.php';
                    }
                });
            } else if (response.error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.error,
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed && response.redirect) {
                        window.location.href = response.redirect;
                    }
                });
            }
        },
        error: function() {
            // Reset button state
            button.html('<i class="fa fa-heart"></i>');
            
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: 'Error adding to wishlist',
                showConfirmButton: false,
                timer: 1500
            });
        }
    });
});
</script>

</body>
</html>