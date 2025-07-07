<?php
session_start();
error_reporting(0);
include('includes/config.php');

// Initialize errmsg if not set
if (!isset($_SESSION['errmsg'])) {
    $_SESSION['errmsg'] = "";
}
// Include PHPMailer
require 'vendor/PHPMailer/PHPMailer.php';
require 'vendor/PHPMailer/SMTP.php';
require 'vendor/PHPMailer/Exception.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Function to send OTP email
function sendOTPEmail($email, $otp) {
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'jadhavmahesh3329@gmail.com';
        $mail->Password = 'gfir mzmm tqzl trlr';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('jadhavmahesh3329@gmail.com', 'Grocery Portal');
        $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Your Password Reset OTP';
        $mail->Body = "Your OTP for password reset is: <b>$otp</b><br>This OTP is valid for 15 minutes.";
        $mail->AltBody = "Your OTP for password reset is: $otp\nThis OTP is valid for 15 minutes.";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Step 1: Verify email and contact
if(isset($_POST['verify'])) {
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    
    $query = mysqli_query($con, "SELECT * FROM users WHERE email='$email' and contactno='$contact'");
    $num = mysqli_fetch_array($query);
    
    if($num > 0) {
        // Generate OTP
        $otp = rand(100000, 999999);
        $_SESSION['reset_otp'] = $otp;
        $_SESSION['reset_otp_expiry'] = time() + 900; // 15 minutes
        $_SESSION['reset_email'] = $email;
        $_SESSION['reset_contact'] = $contact;
        
        // Send OTP
        if(sendOTPEmail($email, $otp)) {
            $_SESSION['otp_sent'] = true;
            $_SESSION['errmsg'] = "OTP sent to your email!";
        } else {
            $_SESSION['errmsg'] = "Failed to send OTP. Please try again.";
        }
    } else {
        $_SESSION['errmsg'] = "Invalid email id or Contact no";
    }
    header("Location: forgot-password.php");
    exit();
}

// Step 2: Verify OTP
if(isset($_POST['verify_otp'])) {
    $entered_otp = $_POST['otp'];
    
    if($entered_otp == $_SESSION['reset_otp'] && time() < $_SESSION['reset_otp_expiry']) {
        $_SESSION['otp_verified'] = true;
        $_SESSION['errmsg'] = "OTP verified successfully!";
    } else {
        $_SESSION['errmsg'] = "Invalid or expired OTP";
    }
    header("Location: forgot-password.php");
    exit();
}

// Step 3: Change password after OTP verification
if(isset($_POST['change'])) {
    if(!isset($_SESSION['otp_verified']) || !$_SESSION['otp_verified']) {
        $_SESSION['errmsg'] = "OTP verification required";
        header("Location: forgot-password.php");
        exit();
    }
    
    $email = $_SESSION['reset_email'];
    $contact = $_SESSION['reset_contact'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    
    mysqli_query($con, "UPDATE users SET password='$password' WHERE email='$email' and contactno='$contact'");
    
    // Clear session variables
    unset($_SESSION['reset_otp']);
    unset($_SESSION['reset_otp_expiry']);
    unset($_SESSION['reset_email']);
    unset($_SESSION['reset_contact']);
    unset($_SESSION['otp_verified']);
    unset($_SESSION['otp_sent']);
    
    $_SESSION['errmsg'] = "Password Changed Successfully";
    header("Location: forgot-password.php");
    exit();
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

	    <title>Shopping Portal | Forgot Password</title>

	    <!-- Bootstrap Core CSS -->
	    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
	    
	    <!-- Customizable CSS -->
	    <link rel="stylesheet" href="assets/css/main.css">
	    <link rel="stylesheet" href="assets/css/green.css">
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
		<!-- Demo Purpose Only. Should be removed in production : END -->

		
		<!-- Icons/Glyphs -->
		<link rel="stylesheet" href="assets/css/font-awesome.min.css">

        <!-- Fonts --> 
		<link href='http://fonts.googleapis.com/css?family=Roboto:300,400,500,700' rel='stylesheet' type='text/css'>
		
		<!-- Favicon -->
		<link rel="shortcut icon" href="assets/images/favicon.ico">
<script type="text/javascript">
function valid()
{
 if(document.register.password.value!= document.register.confirmpassword.value)
{
alert("Password and Confirm Password Field do not match  !!");
document.register.confirmpassword.focus();
return false;
}
return true;
}
</script>
	</head>
    <body class="cnt-home">
	
		
	
		<!-- ============================================== HEADER ============================================== -->
<header class="header-style-1">

	<!-- ============================================== TOP MENU ============================================== -->
<?php include('includes/top-header.php');?>
<!-- ============================================== TOP MENU : END ============================================== -->
<?php include('includes/main-header.php');?>
	<!-- ============================================== NAVBAR ============================================== -->
<?php include('includes/menu-bar.php');?>
<!-- ============================================== NAVBAR : END ============================================== -->

</header>

<!-- ============================================== HEADER : END ============================================== -->
<div class="breadcrumb">
        <div class="container">
            <div class="breadcrumb-inner">
                <ul class="list-inline list-unstyled">
                    <li><a href="home.html">Home</a></li>
                    <li class='active'>Forgot Password</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="body-content outer-top-bd">
        <div class="container">
            <div class="sign-in-page inner-bottom-sm">
                <div class="row">
                    <div class="col-md-6 col-sm-6 sign-in">
                        <h4 class="">Forgot password</h4>
                        <form class="register-form outer-top-xs" method="post">
						<span style="color:red;">
                                <?php 
                                if (!empty($_SESSION['errmsg'])) {
                                    echo htmlentities($_SESSION['errmsg']); 
                                    $_SESSION['errmsg'] = "";
                                }
                                ?>
                            </span>
                            <?php if(!isset($_SESSION['otp_sent'])) { ?>
                                <!-- Step 1: Verify email and contact -->
                                <div class="form-group">
                                    <label class="info-title" for="email">Email Address <span>*</span></label>
                                    <input type="email" name="email" class="form-control unicase-form-control text-input" id="email" required>
                                </div>
                                <div class="form-group">
                                    <label class="info-title" for="contact">Contact no <span>*</span></label>
                                    <input type="text" name="contact" class="form-control unicase-form-control text-input" id="contact" required>
                                </div>
                                <button type="submit" name="verify" class="btn-upper btn btn-primary checkout-page-button">Verify</button>
                            
                            <?php } elseif(isset($_SESSION['otp_sent']) && !isset($_SESSION['otp_verified'])) { ?>
                                <!-- Step 2: Verify OTP -->
                                <div class="form-group">
                                    <label class="info-title" for="otp">Enter OTP <span>*</span></label>
                                    <input type="text" name="otp" class="form-control unicase-form-control text-input" id="otp" required>
                                </div>
                                <button type="submit" name="verify_otp" class="btn-upper btn btn-primary checkout-page-button">Verify OTP</button>
                                <a href="forgot-password.php?resend=1" class="btn btn-default">Resend OTP</a>
                            
                            <?php } else { ?>
                                <!-- Step 3: Change password -->
                                <div class="form-group">
                                    <label class="info-title" for="password">New Password <span>*</span></label>
                                    <input type="password" class="form-control unicase-form-control text-input" id="password" name="password" required>
                                </div>
                                <div class="form-group">
                                    <label class="info-title" for="confirmpassword">Confirm Password <span>*</span></label>
                                    <input type="password" class="form-control unicase-form-control text-input" id="confirmpassword" name="confirmpassword" required>
                                </div>
                                <button type="submit" name="change" class="btn-upper btn btn-primary checkout-page-button">Change Password</button>
                            <?php } ?>
                        </form>
                    </div>
                </div>
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

	<!-- For demo purposes – can be removed on production -->
	
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
	<!-- For demo purposes – can be removed on production : End -->

	

</body>
</html>

