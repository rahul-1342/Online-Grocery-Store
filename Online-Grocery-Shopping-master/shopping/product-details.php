<?php 
session_start();
error_reporting(0);
include('includes/config.php');

// Initialize variables
$pid = isset($_GET['pid']) ? intval($_GET['pid']) : 0;

// Add to cart functionality - only if action parameter exists
if(isset($_GET['action']) && $_GET['action'] == "add"){
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if($id > 0){
        if(isset($_SESSION['cart'][$id])){
            $_SESSION['cart'][$id]['quantity']++;
        } else {
            $sql_p = "SELECT * FROM products WHERE id = $id";
            $query_p = mysqli_query($con, $sql_p);
            if(mysqli_num_rows($query_p) > 0){
                $row_p = mysqli_fetch_array($query_p);
                $_SESSION['cart'][$row_p['id']] = array(
                    "quantity" => 1, 
                    "price" => $row_p['productPrice']
                );
                echo "<script>alert('Product has been added to the cart')</script>";
                echo "<script>document.location ='my-cart.php';</script>";
                exit;
            } else {
                $message = "Product ID is invalid";
            }
        }
    }
}

// Wishlist functionality - only if action parameter exists
if(isset($_GET['action']) && $_GET['action'] == "wishlist" && $pid > 0){
    if(empty($_SESSION['login'])){
        header('location:login.php');
        exit;
    } else {
        mysqli_query($con, "INSERT INTO wishlist(userId, productId) VALUES('".$_SESSION['id']."', '$pid')");
        echo "<script>alert('Product added to wishlist');</script>";
        header('location:my-wishlist.php');
        exit;
    }
}

// Review submission
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review']) && isset($_SESSION['login'])){
    $quality = intval($_POST['quality']);
    $price   = intval($_POST['price']);
    $value   = intval($_POST['value']);
    $review  = mysqli_real_escape_string($con, $_POST['review']);
    $uid     = $_SESSION['id'];
    
    // Get user details
    $user_query = mysqli_query($con, "SELECT name, email FROM users WHERE id='$uid'");
    $user_data = mysqli_fetch_assoc($user_query);
    
    if($quality > 0 && $price > 0 && $value > 0 && !empty($review)){
        $insert = "INSERT INTO user_product_reviews(
                    userId, productId, 
                    rating_quality, rating_price, rating_value, 
                    review, user_name, user_email
                   ) VALUES(
                    '$uid', '$pid', 
                    '$quality', '$price', '$value', 
                    '$review', '".$user_data['name']."', '".$user_data['email']."'
                   )";
        mysqli_query($con, $insert);
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Product Details</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/green.css">
    <link rel="stylesheet" href="assets/css/owl.carousel.css">
    <link rel="stylesheet" href="assets/css/owl.transitions.css">
    <link href="assets/css/lightbox.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/animate.min.css">
    <link rel="stylesheet" href="assets/css/rateit.css">
    <link rel="stylesheet" href="assets/css/bootstrap-select.min.css">
    <link rel="stylesheet" href="assets/css/config.css">
    <link href="assets/css/green.css" rel="alternate stylesheet" title="Green color">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <link href='http://fonts.googleapis.com/css?family=Roboto:300,400,500,700' rel='stylesheet' type='text/css'>
    <link rel="shortcut icon" href="assets/images/favicon.ico">

<style>
.star-rating {
    direction: rtl;
    display: inline-block;
    unicode-bidi: bidi-override;
}

.star-rating input {
    display: none;
}

.star-rating label {
    color: #ccc;
    cursor: pointer;
    font-size: 24px;
    padding: 0 2px;
}

.star-rating label:hover,
.star-rating label:hover ~ label,
.star-rating input:checked ~ label {
    color: #ffc107;
}

.review-item {
    margin-bottom: 20px;
    padding: 15px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.review-title {
    font-weight: bold;
    margin-bottom: 10px;
    margin-bottom: 8px;
    line-height: 1.4;
}

.review-text {
    margin-bottom: 10px;
}

.review-ratings div {
    margin: 5px 0;
    color: #ffc107;
    font-size: 18px;
}
.review-title b {
    font-size: 16px;
}
.review-title i {
    font-size: 12px;
    color: #666;
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
            <?php
            $ret=mysqli_query($con,"select category.categoryName as catname,subCategory.subcategory as subcatname,products.productName as pname from products join category on category.id=products.category join subcategory on subcategory.id=products.subCategory where products.id='$pid'");
            while ($rw=mysqli_fetch_array($ret)) {
            ?>
            <ul class="list-inline list-unstyled">
                <li><a href="index.php">Home</a></li>
                <li><?php echo htmlentities($rw['catname']);?></a></li>
                <li><?php echo htmlentities($rw['subcatname']);?></li>
                <li class='active'><?php echo htmlentities($rw['pname']);?></li>
            </ul>
            <?php }?>
        </div>
    </div>
</div>

<div class="body-content outer-top-xs">
    <div class='container'>
        <div class='row single-product outer-bottom-sm '>
            <div class='col-md-3 sidebar'>
                <div class="sidebar-module-container">
                    <div class="sidebar-widget outer-bottom-xs wow fadeInUp">
                        <h3 class="section-title">Category</h3>
                        <div class="sidebar-widget-body m-t-10">
                            <div class="accordion">
                                <?php 
                                $sql=mysqli_query($con,"select id,categoryName from category");
                                while($row=mysqli_fetch_array($sql)) {
                                ?>
                                <div class="accordion-group">
                                    <div class="accordion-heading">
                                        <a href="category.php?cid=<?php echo $row['id'];?>" class="accordion-toggle collapsed">
                                           <?php echo $row['categoryName'];?>
                                        </a>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>

                    <div class="sidebar-widget hot-deals wow fadeInUp">
                        <h3 class="section-title">hot deals</h3>
                        <div class="owl-carousel sidebar-carousel custom-carousel owl-theme outer-top-xs">
                            <?php
                            $ret=mysqli_query($con,"select * from products order by rand() limit 4 ");
                            while ($rws=mysqli_fetch_array($ret)) {
                            ?>
                            <div class="item">
                                <div class="products">
                                    <div class="hot-deal-wrapper">
                                        <div class="image">
                                            <img src="admin/productimages/<?php echo htmlentities($rws['id']);?>/<?php echo htmlentities($rws['productImage1']);?>" width="200" height="334" alt="">
                                        </div>
                                    </div>
                                    <div class="product-info text-left m-t-20">
                                        <h3 class="name"><a href="product-details.php?pid=<?php echo htmlentities($rws['id']);?>"><?php echo htmlentities($rws['productName']);?></a></h3>
                                        <div class="rating rateit-small"></div>
                                        <div class="product-price">    
                                            <span class="price">Rs. <?php echo htmlentities($rws['productPrice']);?>.00</span>
                                            <span class="price-before-discount">Rs.<?php echo htmlentities($rws['productPriceBeforeDiscount']);?></span>                    
                                        </div>
                                    </div>
                                    <div class="cart clearfix animate-effect">
                                        <div class="action">
                                            <div class="add-cart-button btn-group">
                                                <?php if($rws['productAvailability']=='In Stock'){?>
                                                <button class="btn btn-primary icon" data-toggle="dropdown" type="button">
                                                    <i class="fa fa-shopping-cart"></i>                                                    
                                                </button>
                                                <a href="category.php?page=product&action=add&id=<?php echo $rws['id']; ?>">
                                                    <button class="btn btn-primary" type="button">Add to cart</button>
                                                </a>
                                                <?php } else {?>
                                                <div class="action" style="color:red">Out of Stock</div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>    
                            </div>        
                            <?php } ?>        
                        </div>
                    </div>
                </div>
            </div>

            <?php 
            $ret=mysqli_query($con,"select * from products where id='$pid'");
            if($row=mysqli_fetch_array($ret)) {
            ?>
            <div class='col-md-9'>
                <div class="row wow fadeInUp">
                    <div class="col-xs-12 col-sm-6 col-md-5 gallery-holder">
                        <div class="product-item-holder size-big single-product-gallery small-gallery">
                            <div id="owl-single-product">
                                <div class="single-product-gallery-item" id="slide1">
                                    <a data-lightbox="image-1" data-title="<?php echo htmlentities($row['productName']);?>" href="admin/productimages/<?php echo htmlentities($row['id']);?>/<?php echo htmlentities($row['productImage1']);?>">
                                        <img class="img-responsive" alt="" src="assets/images/blank.gif" data-echo="admin/productimages/<?php echo htmlentities($row['id']);?>/<?php echo htmlentities($row['productImage1']);?>" width="370" height="350" />
                                    </a>
                                </div>
                                <div class="single-product-gallery-item" id="slide2">
                                    <a data-lightbox="image-1" data-title="Gallery" href="admin/productimages/<?php echo htmlentities($row['id']);?>/<?php echo htmlentities($row['productImage2']);?>">
                                        <img class="img-responsive" alt="" src="assets/images/blank.gif" data-echo="admin/productimages/<?php echo htmlentities($row['id']);?>/<?php echo htmlentities($row['productImage2']);?>" />
                                    </a>
                                </div>
                                <div class="single-product-gallery-item" id="slide3">
                                    <a data-lightbox="image-1" data-title="Gallery" href="admin/productimages/<?php echo htmlentities($row['id']);?>/<?php echo htmlentities($row['productImage3']);?>">
                                        <img class="img-responsive" alt="" src="assets/images/blank.gif" data-echo="admin/productimages/<?php echo htmlentities($row['id']);?>/<?php echo htmlentities($row['productImage3']);?>" />
                                    </a>
                                </div>
                            </div>

                            <div class="single-product-gallery-thumbs gallery-thumbs">
                                <div id="owl-single-product-thumbnails">
                                    <div class="item">
                                        <a class="horizontal-thumb active" data-target="#owl-single-product" data-slide="1" href="#slide1">
                                            <img class="img-responsive" alt="" src="assets/images/blank.gif" data-echo="admin/productimages/<?php echo htmlentities($row['id']);?>/<?php echo htmlentities($row['productImage1']);?>" />
                                        </a>
                                    </div>
                                    <div class="item">
                                        <a class="horizontal-thumb" data-target="#owl-single-product" data-slide="2" href="#slide2">
                                            <img class="img-responsive" width="85" alt="" src="assets/images/blank.gif" data-echo="admin/productimages/<?php echo htmlentities($row['id']);?>/<?php echo htmlentities($row['productImage2']);?>"/>
                                        </a>
                                    </div>
                                    <div class="item">
                                        <a class="horizontal-thumb" data-target="#owl-single-product" data-slide="3" href="#slide3">
                                            <img class="img-responsive" width="85" alt="" src="assets/images/blank.gif" data-echo="admin/productimages/<?php echo htmlentities($row['id']);?>/<?php echo htmlentities($row['productImage3']);?>" height="200" />
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class='col-sm-6 col-md-7 product-info-block'>
                        <div class="product-info">
                            <h1 class="name"><?php echo htmlentities($row['productName']);?></h1>
                            <?php 
                            $rt=mysqli_query($con,"select * from productreviews where productId='$pid'");
                            $num=mysqli_num_rows($rt);
                            if($num > 0) {
                            ?>        
                            <div class="rating-reviews m-t-20">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="rating rateit-small"></div>
                                    </div>
                                    <div class="col-sm-8">
                                        <div class="reviews">
                                            <a href="#" class="lnk">(<?php echo htmlentities($num);?> Reviews)</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>

                            <div class="stock-container info-container m-t-10">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="stock-box">
                                            <span class="label">Availability :</span>
                                        </div>    
                                    </div>
                                    <div class="col-sm-9">
                                        <div class="stock-box">
                                            <span class="value"><?php echo htmlentities($row['productAvailability']);?></span>
                                        </div>    
                                    </div>
                                </div>
                            </div>

                            <div class="stock-container info-container m-t-10">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="stock-box">
                                            <span class="label">Product Brand :</span>
                                        </div>    
                                    </div>
                                    <div class="col-sm-9">
                                        <div class="stock-box">
                                            <span class="value"><?php echo htmlentities($row['productCompany']);?></span>
                                        </div>    
                                    </div>
                                </div>
                            </div>

                            <div class="stock-container info-container m-t-10">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="stock-box">
                                            <span class="label">Shipping Charge :</span>
                                        </div>    
                                    </div>
                                    <div class="col-sm-9">
                                        <div class="stock-box">
                                            <span class="value">
                                                <?php 
                                                if($row['shippingCharge']==0) {
                                                    echo "Free";
                                                } else {
                                                    echo htmlentities($row['shippingCharge']);
                                                }
                                                ?>
                                            </span>
                                        </div>    
                                    </div>
                                </div>
                            </div>

                            <div class="price-container info-container m-t-20">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="price-box">
                                            <span class="price">Rs. <?php echo htmlentities($row['productPrice']);?></span>
                                            <span class="price-strike">Rs.<?php echo htmlentities($row['productPriceBeforeDiscount']);?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="favorite-button m-t-10">
                                            <a class="btn btn-primary" data-toggle="tooltip" data-placement="right" title="Wishlist" href="product-details.php?pid=<?php echo htmlentities($row['id'])?>&&action=wishlist">
                                                <i class="fa fa-heart"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="quantity-container info-container">
                                <div class="row">
                                    <div class="col-sm-2">
                                        <span class="label">Qty :</span>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="cart-quantity">
                                            <div class="quant-input">
                                                <div class="arrows">
                                                  <div class="arrow plus gradient"><span class="ir"><i class="icon fa fa-sort-asc"></i></span></div>
                                                  <div class="arrow minus gradient"><span class="ir"><i class="icon fa fa-sort-desc"></i></span></div>
                                                </div>
                                                <input type="text" value="1">
                                          </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <?php if($row['productAvailability']=='In Stock'){?>
                                        <a href="product-details.php?page=product&action=add&id=<?php echo $row['id']; ?>" class="btn btn-primary"><i class="fa fa-shopping-cart inner-right-vs"></i> ADD TO CART</a>
                                        <?php } else {?>
                                        <div class="action" style="color:red">Out of Stock</div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>

                            <div class="product-social-link m-t-20 text-right">
                                <span class="social-label">Share :</span>
                                <div class="social-icons">
                                    <ul class="list-inline">
                                        <li><a class="fa fa-facebook" href="http://facebook.com/transvelo"></a></li>
                                        <li><a class="fa fa-twitter" href="#"></a></li>
                                        <li><a class="fa fa-linkedin" href="#"></a></li>
                                        <li><a class="fa fa-rss" href="#"></a></li>
                                        <li><a class="fa fa-pinterest" href="#"></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="product-tabs inner-bottom-xs wow fadeInUp">
                    <div class="row">
                        <div class="col-sm-3">
                            <ul id="product-tabs" class="nav nav-tabs nav-tab-cell">
                                <li class="active"><a data-toggle="tab" href="#description">DESCRIPTION</a></li>
                                <li><a data-toggle="tab" href="#review">REVIEW</a></li>
                            </ul>
                        </div>
                        <div class="col-sm-9">
                            <div class="tab-content">
                                <div id="description" class="tab-pane in active">
                                    <div class="product-tab">
                                        <p class="text"><?php echo $row['productDescription'];?></p>
                                    </div>    
                                </div>

                                <div id="review" class="tab-pane">
                                    <div class="product-tab">
                                    <div class="product-reviews">
    <h4 class="title">Customer Reviews</h4>
    <?php 
    $qry = mysqli_query($con, "SELECT * FROM user_product_reviews WHERE productId='$pid' ORDER BY reviewDate DESC");
    while($rvw = mysqli_fetch_array($qry)) {
    ?>
    <div class="review-item" style="border:1px solid #000; padding:1rem; margin-bottom:1rem;">
        <div class="review-title">
            <b><?= htmlentities($rvw['user_name']) ?></b> 
            (<?= htmlentities($rvw['user_email']) ?>)<br>
            <i class="fa fa-calendar"></i> <?= $rvw['reviewDate'] ?>
        </div>
        <div class="review-text"><?= nl2br(htmlentities($rvw['review'])) ?></div>
        <div class="review-ratings">
            <div>Quality: <?= str_repeat('★', $rvw['rating_quality']) . str_repeat('☆', 5 - $rvw['rating_quality']) ?></div>
            <div>Price: <?= str_repeat('★', $rvw['rating_price']) . str_repeat('☆', 5 - $rvw['rating_price']) ?></div>
            <div>Value: <?= str_repeat('★', $rvw['rating_value']) . str_repeat('☆', 5 - $rvw['rating_value']) ?></div>
        </div>
    </div>
    <?php } ?>
</div>


                                        <?php if(isset($_SESSION['login'])): ?>
                                            <form method="post" class="cnt-form">
    <div class="product-add-review">
        <h4 class="title">Leave a Review</h4>
        <div class="review-table table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr><th>Criteria</th><th>Rating</th></tr>
                </thead>
                <tbody>
                    <?php foreach(['quality', 'price', 'value'] as $aspect): ?>
                    <tr>
                        <td class="cell-label"><?= ucfirst($aspect) ?></td>
                        <td>
                            <div class="star-rating">
                                <?php for($i=5; $i>=1; $i--): ?>
                                <input type="radio" id="<?= $aspect ?>-star<?= $i ?>" name="<?= $aspect ?>" value="<?= $i ?>" required>
                                <label for="<?= $aspect ?>-star<?= $i ?>">★</label>
                                <?php endfor; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="form-group">
            <label>Your Review <span class="astk">*</span></label>
            <textarea name="review" class="form-control" rows="4" required></textarea>
        </div>

        <div class="action text-right">
            <button type="submit" name="submit_review" class="btn btn-primary">Submit Review</button>
        </div>
    </div>
</form>
<?php else: ?>
    <p class="text-warning">Please <a href="login.php">login</a> to leave a review.</p>
<?php endif; ?>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php 
                $cid=$row['category'];
                $subcid=$row['subCategory']; 
                } 
                ?>

                <section class="section featured-product wow fadeInUp">
                    <h3 class="section-title">Related Products</h3>
                    <div class="owl-carousel home-owl-carousel upsell-product custom-carousel owl-theme outer-top-xs">
                        <?php 
                        $qry=mysqli_query($con,"select * from products where subCategory='$subcid' and category='$cid'");
                        while($rw=mysqli_fetch_array($qry)) {
                        ?>    
                        <div class="item item-carousel">
                            <div class="products">
                                <div class="product">        
                                    <div class="product-image">
                                        <div class="image">
                                            <a href="product-details.php?pid=<?php echo htmlentities($rw['id']);?>"><img src="assets/images/blank.gif" data-echo="admin/productimages/<?php echo htmlentities($rw['id']);?>/<?php echo htmlentities($rw['productImage1']);?>" width="150" height="240" alt=""></a>
                                        </div>
                                    </div>
                                    <div class="product-info text-left">
                                        <h3 class="name"><a href="product-details.php?pid=<?php echo htmlentities($rw['id']);?>"><?php echo htmlentities($rw['productName']);?></a></h3>
                                        <div class="rating rateit-small"></div>
                                        <div class="description"></div>
                                        <div class="product-price">    
                                            <span class="price">Rs.<?php echo htmlentities($rw['productPrice']);?></span>
                                            <span class="price-before-discount">Rs.<?php echo htmlentities($rw['productPriceBeforeDiscount']);?></span>
                                        </div>
                                    </div>
                                    <div class="cart clearfix animate-effect">
                                        <div class="action">
                                            <ul class="list-unstyled">
                                                <li class="add-cart-button btn-group">
                                                    <button class="btn btn-primary icon" data-toggle="dropdown" type="button">
                                                        <i class="fa fa-shopping-cart"></i>                                                    
                                                    </button>
                                                    <a href="product-details.php?page=product&action=add&id=<?php echo $rw['id']; ?>" class="lnk btn btn-primary">Add to cart</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </section>
            </div>
            <div class="clearfix"></div>
        </div>
          
    </div>
</div>
<?php include('includes/footer.php');?>

<script src="assets/js/jquery-1.11.1.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/bootstrap-hover-dropdown.min.js"></script>
<script src="assets/js/owl.carousel.min.js"></script>
<script src="assets/js/echo.min.js"></script>
<script src="assets/js/jquery.easing-1.3.min.js"></script>
<script src="assets/js/bootstrap-slider.min.js"></script>
<script src="assets/js/jquery.rateit.min.js"></script>
<script type="text/javascript" src="assets/js/lightbox.min.js"></script>
<script src="assets/js/bootstrap-select.min.js"></script>
<script src="assets/js/wow.min.js"></script>
<script src="assets/js/scripts.js"></script>
<script src="switchstylesheet/switchstylesheet.js"></script>
<script>
    $(document).ready(function(){ 
        $(".changecolor").switchstylesheet( { seperator:"color"} );
        $('.show-theme-options').click(function(){
            $(this).parent().toggleClass('open');
            return false;
        });
    });

    $(window).bind("load", function() {
       $('.show-theme-options').delay(2000).trigger('click');
    });
</script>
<script>
$(document).ready(function() {
    // Star rating interaction
    $('.star-rating').each(function() {
        var $container = $(this);
        
        // Highlight stars on hover
        $container.find('label').hover(
            function() {
                $(this).addClass('hover');
                $(this).prevAll('label').addClass('hover');
            },
            function() {
                $container.find('label').removeClass('hover');
            }
        );
        
        // Handle selection
        $container.find('input').change(function() {
            var $input = $(this);
            $container.find('label').removeClass('selected');
            $input.nextAll('label').addClass('selected');
        });
        
        // Initialize selected state
        $container.find('input:checked').each(function() {
            $(this).nextAll('label').addClass('selected');
        });
    });
});
</script>
</body>
</html>