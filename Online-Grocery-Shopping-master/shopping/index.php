<?php
session_start();
error_reporting(0);
include('includes/config.php');

// Initialize cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// Handle wishlist additions
if(isset($_GET['action']) && $_GET['action']=="wishlist" && isset($_GET['pid'])){
    $pid = intval($_GET['pid']);
    
    if(strlen($_SESSION['login']) == 0) {   
        $response = [
            'error' => 'Please login to add items to wishlist',
            'redirect' => 'login.php'
        ];
    } else {
        $userId = $_SESSION['id'];
        $check = mysqli_query($con, "SELECT * FROM wishlist WHERE userId='$userId' AND productId='$pid'");
        
        if(mysqli_num_rows($check) == 0) {
            mysqli_query($con, "INSERT INTO wishlist(userId, productId) VALUES('$userId','$pid')");
            $response = ['success' => true];
        } else {
            $response = ['error' => 'Product already in wishlist'];
        }
    }
    
    if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		
		<!-- Meta -->
		<meta charset="utf-8">
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
		<meta name="description" content="">
		<meta name="author" content="">
	    <meta name="keywords" content="MediaCenter, Template, eCommerce">
	    <meta name="robots" content="all">

	    <title>Online Grocery Shoping Portal</title>

	    <!-- Bootstrap Core CSS -->
	    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
	    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
	    <!-- Customizable CSS -->
	    <link rel="stylesheet" href="assets/css/main.css">
	    <link rel="stylesheet" href="assets/css/orange.css">
	    <link rel="stylesheet" href="assets/css/owl.carousel.css">
		<link rel="stylesheet" href="assets/css/owl.transitions.css">
		<!--<link rel="stylesheet" href="assets/css/owl.theme.css">-->
		<link href="assets/css/lightbox.css" rel="stylesheet">
		<link rel="stylesheet" href="assets/css/animate.min.css">
		<link rel="stylesheet" href="assets/css/rateit.css">
		<link rel="stylesheet" href="assets/css/bootstrap-select.min.css">

		<!-- Demo Purpose Only. Should be removed in production -->
		<link rel="stylesheet" href="assets/css/config.css">

		<link href="assets/css/green.css" rel="alternate stylesheet" title="Green color">
		<link href="assets/css/blue.css" rel="alternate stylesheet" title="Blue color">
		<link href="assets/css/red.css" rel="alternate stylesheet" title="Red color">
		<link href="assets/css/orange.css" rel="alternate stylesheet" title="Orange color">
		<link href="assets/css/dark-green.css" rel="alternate stylesheet" title="Darkgreen color">
		<link rel="stylesheet" href="assets/css/font-awesome.min.css">
		<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
		<link href='http://fonts.googleapis.com/css?family=Roboto:300,400,500,700' rel='stylesheet' type='text/css'>
		
		<!-- Favicon -->
		<link rel="shortcut icon" href="assets/images/favicon.ico">
		<style>
			.add-to-wishlist-btn {
    padding-left: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 42px;
    width: 44px;
    font-size: 16px;
}
.list-unstyled
{
	display:flex;
	gap:10px;
}
/* Add this to your existing styles */
.product {
    height: 100%;
    display: flex;
    flex-direction: column;
}

.product-info {
    flex: 1;
    min-height: 150px; /* Adjust this value as needed */
    display: flex;
    flex-direction: column;
}

.product-info .name {
    flex: 1;
    display: -webkit-box;
    -webkit-line-clamp: 2; /* Limit to 2 lines */
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-bottom: 5px;
    min-height: 40px; /* Adjust based on your font size */
}

.product-price {
    margin-top: auto; /* Pushes price to bottom */
}

.cart {
    margin-top: 10px;
}
/* For the smaller product cards in FOODS section */
.product-micro .product-info {
    min-height: 120px; /* Slightly smaller for compact layout */
}

.product-micro .name {
    min-height: 36px;
    font-size: 14px; /* Slightly smaller font */
}
/* Add this to your existing styles */
.item-carousel {
    padding: 0 10px; /* Adds space between cards */
    margin-bottom: 20px; /* Space below each card */
}

.products {
    border-radius: 5px; /* Rounded corners */
    padding: 15px; /* Inner spacing */
    height: 100%; /* Ensures consistent height */
    transition: all 0.3s ease; /* Smooth hover effect */
}

.products:hover {
    box-shadow: 0 5px 15px rgba(0,0,0,0.1); /* Subtle hover effect */
    transform: translateY(-5px); /* Slight lift on hover */
}
/* Product info section */
.product-info {
    padding: 10px 0;
}

/* Product name styling */
.product-info .name {
    font-size: 14px;
    line-height: 1.4;
    margin: 5px 0;
    height: 40px; /* Fixed height for 2 lines */
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

/* Price styling */
.product-price {
    margin: 10px 0;
    display: flex;
    flex-direction: column;
}

.product-price .price {
    font-size: 16px;
    font-weight: bold;
    color: #336699;
}

.product-price .price-before-discount {
    font-size: 12px;
    color: #999;
    text-decoration: line-through;
}

/* Action buttons */
.cart .action {
    margin-top: 10px;
}

/* Rating stars */
.rating {
    margin: 5px 0;
    color: #ffb503; /* Star color */
}

/* Owl Carousel adjustments */
.owl-carousel .owl-stage {
    display: flex;
    padding: 10px 0;
}

.owl-carousel .owl-item {
    padding: 0 5px;
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
<div class="body-content outer-top-xs" id="top-banner-and-menu">
	<div class="container">
		<div class="furniture-container homepage-container">
		<div class="row">
		
			<div class="col-xs-12 col-sm-12 col-md-3 sidebar">
				<!-- ================================== TOP NAVIGATION ================================== -->
	<?php include('includes/side-menu.php');?>
<!-- ================================== TOP NAVIGATION : END ================================== -->
			</div><!-- /.sidemenu-holder -->	
			
			<div class="col-xs-12 col-sm-12 col-md-9 homebanner-holder">
				<!-- ========================================== SECTION – HERO ========================================= -->
			
<div id="hero" class="homepage-slider3">
	<div id="owl-main" class="owl-carousel owl-inner-nav owl-ui-sm">
		<div class="full-width-slider">	
			<div class="item" style="background-image: url(https://techcrunch.com/wp-content/uploads/2015/03/groceries-e1554037962210.jpg?w=1390&crop=1);">
				<!-- /.container-fluid -->
			</div><!-- /.item -->
		</div><!-- /.full-width-slider -->
	    
	    <div class="full-width-slider">
			<div class="item full-width-slider" style="background-image: url(https://techcrunch.com/wp-content/uploads/2015/03/groceries-e1554037962210.jpg?w=1390&crop=1);">
			</div><!-- /.item -->
		</div><!-- /.full-width-slider -->

	</div><!-- /.owl-carousel -->
</div>
			
<!-- ========================================= SECTION – HERO : END ========================================= -->	
				<!-- ============================================== INFO BOXES ============================================== -->
<div class="info-boxes wow fadeInUp">
	<div class="info-boxes-inner">
		<div class="row">
			<div class="col-md-6 col-sm-4 col-lg-4">
				<div class="info-box">
					<div class="row">
						<div class="col-xs-2">
						     <i></i>
						</div>
						<div class="col-xs-10">
							<h4 class="info-box-heading orange">money back</h4>
						</div>
					</div>	
					<h6 class="text">7 Day Money Back Guarantee.</h6>
				</div>
			</div><!-- .col -->

			<div class="hidden-md col-sm-4 col-lg-4">
				<div class="info-box">
					<div class="row">
						<div class="col-xs-2">
							<i class="icon fa fa-truck"></i>
						</div>
						<div class="col-xs-10">
							<h4 class="info-box-heading orange">free shipping</h4>
						</div>
					</div>
					<h6 class="text">free ship-on oder over Rs. 600.00</h6>	
				</div>
			</div><!-- .col -->

			<div class="col-md-6 col-sm-4 col-lg-4">
				<div class="info-box">
					<div class="row">
						<div class="col-xs-2">
							<i class="icon fa fa-gift"></i>
						</div>
						<div class="col-xs-10">
							<h4 class="info-box-heading red">Special Sale</h4>
						</div>
					</div>
					<h6 class="text">All Grocery-sale up to 20% off </h6>	
				</div>
			</div><!-- .col -->
		</div><!-- /.row -->
	</div><!-- /.info-boxes-inner -->
	
</div><!-- /.info-boxes -->
<!-- ============================================== INFO BOXES : END ============================================== -->		
			</div><!-- /.homebanner-holder -->
			
		</div><!-- /.row -->

		<!-- ============================================== SCROLL TABS ============================================== -->
		<div id="product-tabs-slider" class="scroll-tabs inner-bottom-vs  wow fadeInUp">
			<div class="more-info-tab clearfix">
			   <h3 class="new-product-title pull-left">Deals Of the week</h3>
				<ul class="nav nav-tabs nav-tab-line pull-right" id="new-products-1">
					<li class="active"><a href="#all" data-toggle="tab">All</a></li>
					<li><a href="#books" data-toggle="tab"> veggies</a></li>
					<li><a href="#furniture" data-toggle="tab">fruits</a></li>
				</ul><!-- /.nav-tabs -->
			</div>

			<div class="tab-content outer-top-xs">
				<div class="tab-pane in active" id="all">			
					<div class="product-slider">
						<div class="owl-carousel home-owl-carousel custom-carousel owl-theme" data-item="4">
<?php
$ret=mysqli_query($con,"select * from products");
while ($row=mysqli_fetch_array($ret)) 
{
	# code...


?>

						    	
		<div class="item item-carousel">
			<div class="products">
				
	<div class="product">		
		<div class="product-image">
			<div class="image">
				<a href="product-details.php?pid=<?php echo htmlentities($row['id']);?>">
				<img  src="admin/productimages/<?php echo htmlentities($row['id']);?>/<?php echo htmlentities($row['productImage1']);?>" data-echo="admin/productimages/<?php echo htmlentities($row['id']);?>/<?php echo htmlentities($row['productImage1']);?>"  width="180" height="300" alt=""></a>
			</div><!-- /.image -->			

			                        		   
		</div><!-- /.product-image -->
			
		
		<div class="product-info text-left">
			<h3 class="name"><a href="product-details.php?pid=<?php echo htmlentities($row['id']);?>"><?php echo htmlentities($row['productName']);?></a></h3>
			<div class="rating rateit-small"></div>
			<div class="description"></div>

			<div class="product-price">	
				<span class="price">
					Rs.<?php echo htmlentities($row['productPrice']);?>			</span>
										     <span class="price-before-discount">Rs.<?php echo htmlentities($row['productPriceBeforeDiscount']);?>	</span>
									
			</div><!-- /.product-price -->
			
		</div><!-- /.product-info -->
  <div class="cart clearfix animate-effect">
    <div class="action">
        <ul class="list-unstyled">
            <li class="add-cart-button btn-group">
                <?php if($row['productAvailability'] == 'In Stock'){ ?>
                <button class="btn btn-primary add-to-cart-btn " type="button" style="text-transform: none;" data-product-id="<?php echo $row['id']; ?>">
                    <i class="fa fa-shopping-cart"></i>  &nbsp;Add to cart
                </button>
                <?php } else { ?>
                <div class="action" style="color:red">Out of Stock</div>
                <?php } ?>
            </li>
            <li class="add-cart-button btn-group">
    <button class="btn btn-primary add-to-wishlist-btn" type="button" title="Add to wishlist" data-product-id="<?php echo $row['id']; ?>">
        <i class="fa fa-heart"></i>
    </button>
</li>

        </ul>
    </div>
</div>
			</div><!-- /.product -->
      
			</div><!-- /.products -->
		</div><!-- /.item -->
	<?php } ?>

			</div><!-- /.home-owl-carousel -->
					</div><!-- /.product-slider -->
				</div>




	<div class="tab-pane" id="books">
					<div class="product-slider">
						<div class="owl-carousel home-owl-carousel custom-carousel owl-theme">
		<?php
$ret=mysqli_query($con,"select * from products where category=3");
while ($row=mysqli_fetch_array($ret)) 
{
	# code...


?>

						    	
		<div class="item item-carousel">
			<div class="products">
				
	<div class="product">		
		<div class="product-image">
			<div class="image">
				<a href="product-details.php?pid=<?php echo htmlentities($row['id']);?>">
				<img  src="admin/productimages/<?php echo htmlentities($row['id']);?>/<?php echo htmlentities($row['productImage1']);?>" data-echo="admin/productimages/<?php echo htmlentities($row['id']);?>/<?php echo htmlentities($row['productImage1']);?>"  width="200" height="300" alt=""></a>
			</div><!-- /.image -->			

			                        		   
		</div><!-- /.product-image -->
			
		
		<div class="product-info text-left">
			<h3 class="name"><a href="product-details.php?pid=<?php echo htmlentities($row['id']);?>"><?php echo htmlentities($row['productName']);?></a></h3>
			<div class="rating rateit-small"></div>
			<div class="description"></div>
			<div class="product-price">	
				<span class="price">
					Rs. <?php echo htmlentities($row['productPrice']);?>			</span>
										     <span class="price-before-discount">Rs.<?php echo htmlentities($row['productPriceBeforeDiscount']);?></span>
									
			</div><!-- /.product-price -->
		</div><!-- /.product-info -->
  <div class="cart clearfix animate-effect">
    <div class="action">
        <ul class="list-unstyled">
            <li class="add-cart-button btn-group">
                <?php if($row['productAvailability'] == 'In Stock'){ ?>
                <button class="btn btn-primary add-to-cart-btn" type="button" style="text-transform: none;" data-product-id="<?php echo $row['id']; ?>">
                    <i class="fa fa-shopping-cart"></i> &nbsp;Add to cart
                </button>
                <?php } else { ?>
                <div class="action" style="color:red">Out of Stock</div>
                <?php } ?>
            </li>
            <li class="add-cart-button btn-group">
    <button class="btn btn-primary add-to-wishlist-btn" type="button" title="Add to wishlist" data-product-id="<?php echo $row['id']; ?>">
        <i class="fa fa-heart"></i>
    </button>
</li>

        </ul>
    </div>
</div>
			</div><!-- /.product -->
			</div><!-- /.products -->
		</div><!-- /.item -->
	<?php } ?>
								</div><!-- /.home-owl-carousel -->
					</div><!-- /.product-slider -->
				</div>
		<div class="tab-pane" id="furniture">
					<div class="product-slider">
						<div class="owl-carousel home-owl-carousel custom-carousel owl-theme">
		<?php
$ret=mysqli_query($con,"select * from products where category=5");
while ($row=mysqli_fetch_array($ret)) 
{
?>				    	
		<div class="item item-carousel">
			<div class="products">		
	<div class="product">		
		<div class="product-image">
			<div class="image">
				<a href="product-details.php?pid=<?php echo htmlentities($row['id']);?>">
				<img  src="admin/productimages/<?php echo htmlentities($row['id']);?>/<?php echo htmlentities($row['productImage1']);?>" data-echo="admin/productimages/<?php echo htmlentities($row['id']);?>/<?php echo htmlentities($row['productImage1']);?>"  width="180" height="300" alt=""></a>
			</div>		                        		   
		</div>
		<div class="product-info text-left">
			<h3 class="name"><a href="product-details.php?pid=<?php echo htmlentities($row['id']);?>"><?php echo htmlentities($row['productName']);?></a></h3>
			<div class="rating rateit-small"></div>
			<div class="description"></div>

			<div class="product-price">	
				<span class="price">
					Rs.<?php echo htmlentities($row['productPrice']);?>			</span>
										     <span class="price-before-discount">Rs.<?php echo htmlentities($row['productPriceBeforeDiscount']);?></span>					
			</div>
		</div>
<div class="cart clearfix animate-effect">
    <div class="action">
        <ul class="list-unstyled">
            <li class="add-cart-button btn-group">
                <?php if($row['productAvailability'] == 'In Stock'){ ?>
                <button class="btn btn-primary add-to-cart-btn" type="button" style="text-transform: none;" data-product-id="<?php echo $row['id']; ?>">
                    <i class="fa fa-shopping-cart"></i> &nbsp;Add to cart
                </button>
                <?php } else { ?>
                <div class="action" style="color:red">Out of Stock</div>
                <?php } ?>
            </li>
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
	<?php } ?>
								</div>
					</div>
				</div>
			</div>
		</div>
         <!-- ============================================== TABS ============================================== -->
			<div class="sections prod-slider-small outer-top-small">
				<div class="row">
					<div class="col-md-6">
	                   <section class="section">
	                   	<h3 class="section-title"></h3>
	                   	<div class="owl-carousel homepage-owl-carousel custom-carousel outer-top-xs owl-theme" data-item="2">
<?php
$ret=mysqli_query($con,"select * from products where category=4 and subCategory=4");
while ($row=mysqli_fetch_array($ret)) 
{
?>
		<div class="item item-carousel">
		<div class="products">		
	<div class="product">		
		<div class="product-image">
			<div class="image">
				<a href="product-details.php?pid=<?php echo htmlentities($row['id']);?>"><img  src="admin/productimages/<?php echo htmlentities($row['id']);?>/<?php echo htmlentities($row['productImage1']);?>" data-echo="admin/productimages/<?php echo htmlentities($row['id']);?>/<?php echo htmlentities($row['productImage1']);?>"  width="180" height="300"></a>
			</div><!-- /.image -->			                        		   
		</div><!-- /.product-image -->
		<div class="product-info text-left">
			<h3 class="name"><a href="product-details.php?pid=<?php echo htmlentities($row['id']);?>"><?php echo htmlentities($row['productName']);?></a></h3>
			<div class="rating rateit-small"></div>
			<div class="description"></div>
			<div class="product-price">	
				<span class="price">
					Rs. <?php echo htmlentities($row['productPrice']);?>			</span>
										     <span class="price-before-discount">Rs.<?php echo htmlentities($row['productPriceBeforeDiscount']);?></span>					
			</div>
		</div>
  <div class="cart clearfix animate-effect">
    <div class="action">
        <ul class="list-unstyled">
            <li class="add-cart-button btn-group">
                <?php if($row['productAvailability'] == 'In Stock'){ ?>
                <button class="btn btn-primary add-to-cart-btn" type="button" style="text-transform: none;" data-product-id="<?php echo $row['id']; ?>">
                    <i class="fa fa-shopping-cart"></i> &nbsp;Add to cart
                </button>
                <?php } else { ?>
                <div class="action" style="color:red">Out of Stock</div>
                <?php } ?>
            </li>
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
<?php }?>
			                   	</div>
	                   </section>
					</div>
					<div class="col-md-6">
						<section class="section">
							<h3 class="section-title"></h3>
		                   	<div class="owl-carousel homepage-owl-carousel custom-carousel outer-top-xs owl-theme" data-item="2">
	<?php
$ret=mysqli_query($con,"select * from products where category=4 and subCategory=6");
while ($row=mysqli_fetch_array($ret)) 
{
?>
		<div class="item item-carousel">
			<div class="products">		
	<div class="product">		
		<div class="product-image">
			<div class="image">
				<a href="product-details.php?pid=<?php echo htmlentities($row['id']);?>"><img  src="admin/productimages/<?php echo htmlentities($row['id']);?>/<?php echo htmlentities($row['productImage1']);?>" data-echo="admin/productimages/<?php echo htmlentities($row['id']);?>/<?php echo htmlentities($row['productImage1']);?>"  width="300" height="300"></a>
			</div><!-- /.image -->			                        		   
		</div><!-- /.product-image -->
		<div class="product-info text-left">
			<h3 class="name"><a href="product-details.php?pid=<?php echo htmlentities($row['id']);?>"><?php echo htmlentities($row['productName']);?></a></h3>
			<div class="rating rateit-small"></div>
			<div class="description"></div>
			<div class="product-price">	
				<span class="price">
					Rs .<?php echo htmlentities($row['productPrice']);?>			</span>
										     <span class="price-before-discount">Rs.<?php echo htmlentities($row['productPriceBeforeDiscount']);?></span>						
			</div>	
		</div>
  <div class="cart clearfix animate-effect">
    <div class="action">
        <ul class="list-unstyled">
            <li class="add-cart-button btn-group">
                <?php if($row['productAvailability'] == 'In Stock'){ ?>
                <button class="btn btn-primary add-to-cart-btn" type="button" style="text-transform: none;" data-product-id="<?php echo $row['id']; ?>">
                    <i class="fa fa-shopping-cart"></i> &nbsp;Add to cart
                </button>
                <?php } else { ?>
                <div class="action" style="color:red">Out of Stock</div>
                <?php } ?>
            </li>
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
<?php }?>
				                   	</div>
	                   </section>

					</div>
				</div>
			</div>
		<!-- ============================================== TABS : END ============================================== -->
	<section class="section featured-product inner-xs wow fadeInUp">
		<h3 class="section-title">FOODS</h3>
		<div class="owl-carousel best-seller custom-carousel owl-theme outer-top-xs">
			<?php
$ret=mysqli_query($con,"select * from products where category=6");
while ($row=mysqli_fetch_array($ret)) 
{
	# code...
?>
				<div class="item">
					<div class="products">

												<div class="product">
							<div class="product-micro">
								<div class="row product-micro-row">
									<div class="col col-xs-6">
										<div class="product-image">
											<div class="image">
												<a href="admin/productimages/<?php echo htmlentities($row['id']);?>/<?php echo htmlentities($row['productImage1']);?>" data-lightbox="image-1" data-title="<?php echo htmlentities($row['productName']);?>">
													<img data-echo="admin/productimages/<?php echo htmlentities($row['id']);?>/<?php echo htmlentities($row['productImage1']);?>" width="170" height="174" alt="">
													<div class="zoom-overlay"></div>
												</a>					
											</div><!-- /.image -->
										</div><!-- /.product-image -->
									</div><!-- /.col -->
									<div class="col col-xs-6">
										<div class="product-info">
											<h3 class="name"><a href="product-details.php?pid=<?php echo htmlentities($row['id']);?>"><?php echo htmlentities($row['productName']);?></a></h3>
											<div class="rating rateit-small"></div>
											<div class="product-price">	
												<span class="price">
													Rs. <?php echo htmlentities($row['productPrice']);?>
												</span>
											</div><!-- /.product-price -->
									  <div class="cart clearfix animate-effect">
    <div class="action">
        <ul class="list-unstyled">
            <li class="add-cart-button btn-group">
                <?php if($row['productAvailability'] == 'In Stock'){ ?>
                <button class="btn btn-primary add-to-cart-btn" type="button" style="text-transform: none;" data-product-id="<?php echo $row['id']; ?>">
                    <i class="fa fa-shopping-cart"></i> &nbsp;Add to cart
                </button>
                <?php } else { ?>
                <div class="action" style="color:red">Out of Stock</div>
                <?php } ?>
            </li>
            <li class="add-cart-button btn-group">
    <button class="btn btn-primary add-to-wishlist-btn" type="button" title="Add to wishlist" data-product-id="<?php echo $row['id']; ?>">
        <i class="fa fa-heart"></i>
    </button>
</li>

        </ul>
    </div>
</div>
										</div>
									</div><!-- /.col -->
								</div><!-- /.product-micro-row -->
							</div><!-- /.product-micro -->
						</div>
					</div>
				</div><?php } ?>
							</div>
		</section>
		<center>
		
		

		
</div>
</div>
<?php include('includes/footer.php');?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
	<!-- For demo purposes – can be removed on production -->
	<script src="switchstylesheet/switchstylesheet.js"></script>
	<script>
		$(window).bind("load", function() {
		   $('.show-theme-options').delay(2000).trigger('click');
		});
	</script>
	<!-- For demo purposes – can be removed on production : End -->
<script>
$(document).ready(function() {
    // Handle add to cart button clicks
    $('.add-to-cart-btn').click(function(e) {
        e.preventDefault();
        var productId = $(this).data('product-id');
        var button = $(this);
        
        // Show loading state
        button.html('<i class="fa fa-spinner fa-spin"></i> Adding...');
        
        $.ajax({
            url: 'add-to-cart.php',
            type: 'GET',
            data: { id: productId },
            dataType: 'json',
            success: function(response) {
                // Reset button state
                button.html('<i class="fa fa-shopping-cart"></i> Add to cart');
                
                if (response.success) {
                    // Update cart count
                    $('.basket-item-count .count').text(response.total_qty);
                    
                    // Update cart dropdown
                    $('.top-cart-row').html(response.cart_html);
                    
                    // Show success toast
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 1500,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    });
                    Toast.fire({
                        icon: 'success',
                        title: response.message
                    });
                } else {
                    // Show error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            },
            error: function() {
                // Reset button state
                button.html('<i class="fa fa-shopping-cart"></i> Add to cart');
                
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: 'Error adding to cart',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        });
    });

    // Handle wishlist button clicks
    $('.add-to-wishlist-btn').on('click', function(e) {
    e.preventDefault();
    var productId = $(this).data('product-id');
    var button = $(this);

    // Show loading state
    button.html('<i class="fa fa-spinner fa-spin"></i> Adding...');

    $.ajax({
        url: 'index.php',
        type: 'GET',
        data: { action: 'wishlist', pid: productId },
        dataType: 'json',
        success: function(response) {
            button.html('<i class="fa fa-heart"></i> ');

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

});
</script>
	

</body>
</html>
