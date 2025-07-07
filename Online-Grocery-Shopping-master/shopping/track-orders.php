<?php
session_start();
error_reporting(0);
include('includes/config.php');
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

	    <title>Track Orders</title>
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
		<link href="assets/css/blue.css" rel="alternate stylesheet" title="Blue color">
		<link href="assets/css/red.css" rel="alternate stylesheet" title="Red color">
		<link href="assets/css/orange.css" rel="alternate stylesheet" title="Orange color">
		<link href="assets/css/dark-green.css" rel="alternate stylesheet" title="Darkgreen color">
		<link rel="stylesheet" href="assets/css/font-awesome.min.css">
		<!-- Add Toastr CSS -->
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
		<!-- Add SweetAlert CSS -->
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
		<link href='http://fonts.googleapis.com/css?family=Roboto:300,400,500,700' rel='stylesheet' type='text/css'>
		<link rel="shortcut icon" href="assets/images/favicon.ico">
	</head>
    <body class="cnt-home">
	
<header class="header-style-1">

	<!-- ============================================== TOP MENU ============================================== -->
<?php include('includes/top-header.php');?>
<!-- ============================================== TOP MENU : END ============================================== -->
<?php include('includes/main-header.php');?>
	<!-- ============================================== NAVBAR ============================================== -->
<?php include('includes/menu-bar.php');?>
<!-- ============================================== NAVBAR : END ============================================== -->

</header>
<div class="breadcrumb">
	<div class="container">
		<div class="breadcrumb-inner">
			<ul class="list-inline list-unstyled">
				<li><a href="home.html">Home</a></li>
				<li class='active'>Track your orders</li>
			</ul>
		</div><!-- /.breadcrumb-inner -->
	</div><!-- /.container -->
</div><!-- /.breadcrumb -->

<div class="body-content outer-top-bd">
	<div class="container">
		<div class="track-order-page inner-bottom-sm">
			<div class="row">
				<div class="col-md-12">
	<h2>Track your Order</h2>
	<span class="title-tag inner-top-vs">Please enter your Order ID in the box below and press Enter. This was given to you on your receipt and in the confirmation email you should have received. </span>
	<form class="register-form outer-top-xs" role="form" method="post" action="order-details.php" id="trackOrderForm">
		<div class="form-group">
		    <label class="info-title" for="exampleOrderId1">Order ID</label>
		    <input type="text" class="form-control unicase-form-control text-input" name="orderid" id="exampleOrderId1" required>
		</div>
	  	<div class="form-group">
		    <label class="info-title" for="exampleBillingEmail1">Registered Email</label>
		    <input type="email" class="form-control unicase-form-control text-input" name="email" id="exampleBillingEmail1" required>
		</div>
	  	<button type="submit" name="submit" class="btn-upper btn btn-primary checkout-page-button">Track</button>
	</form>	
</div>			</div><!-- /.row -->
		</div><!-- /.sigin-in-->

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
	<!-- Add Toastr JS -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
	<!-- Add SweetAlert JS -->
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<script src="assets/js/scripts.js"></script>

	<!-- For demo purposes – can be removed on production -->
	<script src="switchstylesheet/switchstylesheet.js"></script>
	
	<script>
		$(document).ready(function(){ 
			$(".changecolor").switchstylesheet( { seperator:"color"} );
			$('.show-theme-options').click(function(){
				$(this).parent().toggleClass('open');
				return false;
			});

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

			// Form submission handling
			$('#trackOrderForm').on('submit', function(e) {
				e.preventDefault();
				
				// Validate form
				var orderId = $('#exampleOrderId1').val().trim();
				var email = $('#exampleEmail1').val().trim();
				
				if(!orderId || !email) {
					toastr.error('Please fill in all required fields');
					return;
				}
				
				if(!isValidEmail(email)) {
					toastr.error('Please enter a valid email address');
					return;
				}
				
				// Show loading indicator
				Swal.fire({
					title: 'Tracking Order',
					html: 'Please wait while we retrieve your order details...',
					allowOutsideClick: false,
					didOpen: () => {
						Swal.showLoading();
						// Submit the form after validation
						setTimeout(() => {
							e.target.submit();
						}, 1000);
					}
				});
			});
			
			// Function to validate email
			function isValidEmail(email) {
				var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
				return re.test(email);
			}

			// Check for URL parameters to show success/error messages
			var urlParams = new URLSearchParams(window.location.search);
			if(urlParams.has('error')) {
				var errorMsg = urlParams.get('error');
				Swal.fire({
					icon: 'error',
					title: 'Error',
					text: decodeURIComponent(errorMsg),
					confirmButtonColor: '#e74c3c'
				});
			}
			if(urlParams.has('success')) {
				var successMsg = urlParams.get('success');
				toastr.success(decodeURIComponent(successMsg));
			}
		});

		$(window).bind("load", function() {
		   $('.show-theme-options').delay(2000).trigger('click');
		});
	</script>
	<!-- For demo purposes – can be removed on production : End -->
</body>
</html>