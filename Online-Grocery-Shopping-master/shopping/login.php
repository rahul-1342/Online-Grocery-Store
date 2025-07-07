<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Kolkata');
include('includes/config.php');

// Include PHPMailer
require 'vendor/PHPMailer/PHPMailer.php';
require 'vendor/PHPMailer/SMTP.php';
require 'vendor/PHPMailer/Exception.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Initialize login attempts if not set
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['last_login_attempt'] = 0;
}

// Function to send OTP email
function sendOTPEmail($email, $otp, $name) {
    $mail = new PHPMailer(true);
    try {
        // Initialize smtp_debug
        if (!isset($_SESSION['smtp_debug'])) {
            $_SESSION['smtp_debug'] = '';
        }

        // Enable verbose debug output
        $mail->SMTPDebug = 2;
        $mail->Debugoutput = function($str, $level) {
            $_SESSION['smtp_debug'] .= "$str\n";
        };

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
        $mail->addAddress($email, $name);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Your OTP for Registration';
        $mail->Body = "Dear $name,<br><br>Your OTP for email verification is: <b>$otp</b><br>This OTP is valid for 15 minutes.<br><br>Regards,<br>Grocery Portal";
        $mail->AltBody = "Dear $name,\n\nYour OTP for email verification is: $otp\nThis OTP is valid for 15 minutes.\n\nRegards,\nGrocery Portal";

        $mail->send();
        return true;
    } catch (Exception $e) {
        $_SESSION['smtp_error'] = "Mailer Error: {$mail->ErrorInfo}";
        return false;
    }
}

// Code for OTP Verification
if (isset($_POST['verify_otp'])) {
    // CSRF validation
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed");
    }

    $entered_otp = trim(mysqli_real_escape_string($con, $_POST['otp']));
    $errors = array();

    // Debug: Log OTP details
    $_SESSION['otp_debug'] = "Entered OTP: '$entered_otp'\nSession OTP: '" . ($_SESSION['otp'] ?? 'Not set') . "'\nCurrent Time: " . time() . " (" . date('Y-m-d H:i:s') . ")\nExpiry Time: " . ($_SESSION['otp_expiry'] ?? 'Not set') . " (" . (isset($_SESSION['otp_expiry']) ? date('Y-m-d H:i:s', $_SESSION['otp_expiry']) : 'Not set') . ")";

    if (empty($entered_otp)) {
        $errors[] = "OTP is required";
    } elseif ($entered_otp !== (string)$_SESSION['otp'] || time() > $_SESSION['otp_expiry']) {
        $errors[] = "Invalid or expired OTP";
    }

    if (empty($errors)) {
        // Retrieve temporary user data from session
        $name = $_SESSION['temp_user']['name'];
        $email = $_SESSION['temp_user']['email'];
        $contactno = $_SESSION['temp_user']['contactno'];
        $hashed_password = $_SESSION['temp_user']['password'];

        // Insert user into database
        $query = mysqli_query($con, "INSERT INTO users(name,email,contactno,password) VALUES('$name','$email','$contactno','$hashed_password')");

        if ($query) {
            $_SESSION['registration_success'] = "You are successfully registered";
            // Clear temporary data
            unset($_SESSION['temp_user']);
            unset($_SESSION['otp']);
            unset($_SESSION['otp_expiry']);
            unset($_SESSION['otp_sent']);
        } else {
            $_SESSION['registration_error'] = "Something went wrong. Please try again.";
        }
        header("Location: login.php");
        exit();
    } else {
        $_SESSION['registration_error'] = implode("<br>", $errors);
        header("Location: login.php");
        exit();
    }
}

// Code for User Registration
if (isset($_POST['submit'])) {
    // CSRF validation
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed");
    }

    $name = mysqli_real_escape_string($con, $_POST['fullname']);
    $email = mysqli_real_escape_string($con, $_POST['emailid']);
    $contactno = mysqli_real_escape_string($con, $_POST['contactno']);
    $password = $_POST['password'];
    $confirmpassword = $_POST['confirmpassword'];

    // Validation
    $errors = array();

    // Name validation
    if (empty($name)) {
        $errors[] = "Full name is required";
    } elseif (!preg_match("/^[a-zA-Z ]*$/", $name)) {
        $errors[] = "Only letters and white space allowed in name";
    }

    // Email validation
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    } else {
        // Check if email already exists
        $check_email = mysqli_query($con, "SELECT email FROM users WHERE email='$email'");
        if (mysqli_num_rows($check_email) > 0) {
            $errors[] = "Email already exists";
        }
    }

// Contact number validation
if (empty($contactno)) {
    $errors[] = "Contact number is required";
} elseif (!preg_match("/^[0-9]{10}$/", $contactno)) {
    $errors[] = "Contact number must be 10 digits";
} else {
    // Check if contact number already exists
    $check_contact = mysqli_query($con, "SELECT contactno FROM users WHERE contactno='$contactno'");
    if (mysqli_num_rows($check_contact) > 0) {
        $errors[] = "Contact number already registered";
    }
}
    // Password validation
    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters";
    } elseif (!preg_match("/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/", $password)) {
        $errors[] = "Password must contain at least one number, one uppercase and one lowercase letter";
    } elseif ($password != $confirmpassword) {
        $errors[] = "Passwords do not match";
    }

    // If no errors, proceed with OTP generation
    if (empty($errors)) {
        // Generate OTP
        $otp = rand(100000, 999999);
        $_SESSION['otp'] = (string)$otp;
        $_SESSION['otp_expiry'] = time() + 900; // 15 minutes
        $_SESSION['session_debug'] = "Registration OTP Set: $otp\nExpiry: " . date('Y-m-d H:i:s', $_SESSION['otp_expiry']) . "\nSession ID: " . session_id();

        // Store user data temporarily
        $_SESSION['temp_user'] = [
            'name' => $name,
            'email' => $email,
            'contactno' => $contactno,
            'password' => password_hash($password, PASSWORD_BCRYPT)
        ];

        // Send OTP to email
        if (sendOTPEmail($email, $otp, $name)) {
            $_SESSION['otp_sent'] = true;
        } else {
            $errors[] = "Failed to send OTP. Please try again.";
        }
    }

    // Display errors if any
    if (!empty($errors)) {
        $_SESSION['registration_error'] = implode("<br>", $errors);
        header("Location: login.php");
        exit();
    }
}

// Code for User login
if (isset($_POST['login'])) {
    // CSRF validation
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed");
    }

    // Rate limiting check
    if ($_SESSION['login_attempts'] > 5 && (time() - $_SESSION['last_login_attempt']) < 300) {
        $_SESSION['errmsg'] = "Too many login attempts. Please try again in 5 minutes.";
        header("Location: login.php");
        exit();
    }

    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = $_POST['password'];

    // Validation
    $errors = array();

    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    if (empty($password)) {
        $errors[] = "Password is required";
    }

    if (empty($errors)) {
        $query = mysqli_query($con, "SELECT * FROM users WHERE email='$email'");
        $num = mysqli_fetch_array($query);

        if ($num > 0 && password_verify($password, $num['password'])) {
            // Reset login attempts on successful login
            $_SESSION['login_attempts'] = 0;

            $_SESSION['login'] = $email;
            $_SESSION['id'] = $num['id'];
            $_SESSION['username'] = $num['name'];
            $uip = $_SERVER['REMOTE_ADDR'];
            $status = 1;

            mysqli_query($con, "INSERT INTO userlog(userEmail,userip,status) VALUES('".$_SESSION['login']."','$uip','$status')");

            $_SESSION['login_success'] = "Login successful! Redirecting...";
            header("Location: login.php");
            exit();
        } else {
            $errors[] = "Invalid email or password";
            $_SESSION['login_attempts']++;
            $_SESSION['last_login_attempt'] = time();
        }
    }

    // If errors exist
    if (!empty($errors)) {
        $_SESSION['errmsg'] = implode("<br>", $errors);
        $extra = "login.php";
        $uip = $_SERVER['REMOTE_ADDR'];
        $status = 0;
        mysqli_query($con, "INSERT INTO userlog(userEmail,userip,status) VALUES('$email','$uip','$status')");

        $host = $_SERVER['HTTP_HOST'];
        $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        header("location:http://$host$uri/$extra");
        exit();
    }
}

// Debug: Log server time
$_SESSION['time_debug'] = "Server Time: " . date('Y-m-d H:i:s') . "\nTimezone: " . date_default_timezone_get();
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
    <title>Shopping Portal | Signin | Signup</title>
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
    <link href='http://fonts.googleapis.com/css?family=Roboto:300,400,500,700' rel='stylesheet' type='text/css'>
    <link rel="shortcut icon" href="assets/images/favicon.ico">
    <script type="text/javascript">
        function valid() {
            if (document.register.password.value != document.register.confirmpassword.value) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Password and Confirm Password Field do not match!',
                });
                document.register.confirmpassword.focus();
                return false;
            }
            return true;
        }
    </script>
    <script>
        function userAvailability() {
            $("#loaderIcon").show();
            jQuery.ajax({
                url: "check_availability.php",
                data: 'email=' + $("#email").val(),
                type: "POST",
                success: function(data) {
                    $("#user-availability-status1").html(data);
                    $("#loaderIcon").hide();
                },
                error: function() {}
            });
        }
        function checkContactAvailability() {
    $("#loaderIcon").show();
    jQuery.ajax({
        url: "check_availability.php",
        data: 'contactno=' + $("#contactno").val(),
        type: "POST",
        success: function(data) {
            $("#contact-availability-status").html(data);
            $("#loaderIcon").hide();
        },
        error: function() {}
    });
}
    </script>
</head>
<body class="cnt-home">
    <header class="header-style-1">
        <?php include('includes/top-header.php'); ?>
        <?php include('includes/main-header.php'); ?>
        <?php include('includes/menu-bar.php'); ?>
    </header>
    <div class="breadcrumb">
        <div class="container">
            <div class="breadcrumb-inner">
                <ul class="list-inline list-unstyled">
                    <li><a href="home.html">Home</a></li>
                    <li class='active'>Authentication</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="body-content outer-top-bd">
        <div class="container">
            <div class="sign-in-page inner-bottom-sm">
                <div class="row">
                    <div class="col-md-6 col-sm-6 sign-in">
                        <h4 class="">Sign In</h4>
                        <p class="">Hello, Welcome to your account.</p>
                        <form class="register-form outer-top-xs" method="post">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <div class="form-group">
                                <label class="info-title" for="exampleInputEmail1">Email Address <span>*</span></label>
                                <input type="email" name="email" class="form-control unicase-form-control text-input" id="exampleInputEmail1" required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
                            </div>
                            <div class="form-group">
                                <label class="info-title" for="exampleInputPassword1">Password <span>*</span></label>
                                <input type="password" name="password" class="form-control unicase-form-control text-input" id="exampleInputPassword1" required minlength="8">
                            </div>
                            <div class="radio outer-xs">
                                <a href="forgot-password.php" class="forgot-password pull-right">Forgot your Password?</a>
                            </div>
                            <button type="submit" class="btn-upper btn btn-primary checkout-page-button" name="login">Login</button>
                        </form>
                    </div>
                    <div class="col-md-6 col-sm-6 create-new-account">
    <?php if (isset($_SESSION['otp_sent']) && $_SESSION['otp_sent']) { ?>
        <h4 class="checkout-subtitle">Verify OTP</h4>
        <p class="text title-tag-line">Enter the OTP sent to your email.</p>
        <form class="register-form outer-top-xs" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <div class="form-group">
                <label class="info-title" for="otp">OTP <span>*</span></label>
                <input type="text" class="form-control unicase-form-control text-input" id="otp" name="otp" required pattern="[0-9]{6}" title="OTP must be a 6-digit number" oninput="this.value = this.value.trim()">
            </div>
            <div class="form-group">
                <a href="resend_otp.php" class="pull-right">Resend OTP</a>
                <button type="button" class="btn btn-default btn-sm" onclick="changeEmail()" style="margin-right: 10px;">Change Email</button>
            </div>
            <button type="submit" name="verify_otp" class="btn-upper btn btn-primary checkout-page-button">Verify OTP</button>
        </form>
        <div id="otp-timer" style="font-size: 12px; color: red;"></div>
        <script>
            function startTimer(duration, display) {
                let timer = duration, minutes, seconds;
                let interval = setInterval(function () {
                    minutes = parseInt(timer / 60, 10);
                    seconds = parseInt(timer % 60, 10);
                    minutes = minutes < 10 ? "0" + minutes : minutes;
                    seconds = seconds < 10 ? "0" + seconds : seconds;
                    display.textContent = "OTP expires in: " + minutes + ":" + seconds;
                    if (--timer < 0) {
                        clearInterval(interval);
                        display.textContent = "OTP has expired";
                        document.querySelector('button[name="verify_otp"]').disabled = true;
                    }
                }, 1000);
            }
            
            function changeEmail() {
                // Clear OTP related session variables
                fetch('clear_otp_session.php')
                    .then(response => response.text())
                    .then(() => {
                        window.location.reload();
                    });
            }
            
            window.onload = function () {
                let fifteenMinutes = 60 * 15;
                let display = document.querySelector('#otp-timer');
                startTimer(fifteenMinutes, display);
            };
        </script>
    <?php } else { ?>
        <h4 class="checkout-subtitle">Create a New Account</h4>
        <p class="text title-tag-line">Create your own Shopping account.</p>
        <form class="register-form outer-top-xs" role="form" method="post" name="register" onSubmit="return valid();">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <div class="form-group">
                <label class="info-title" for="fullname">Full Name <span>*</span></label>
                <input type="text" class="form-control unicase-form-control text-input" id="fullname" name="fullname" required pattern="[a-zA-Z ]+" title="Only letters and spaces allowed">
            </div>
            <div class="form-group">
                <label class="info-title" for="exampleInputEmail2">Email Address <span>*</span></label>
                <input type="email" class="form-control unicase-form-control text-input" id="email" onBlur="userAvailability()" name="emailid" required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
                <span id="user-availability-status1" style="font-size:12px;"></span>
            </div>
<div class="form-group">
    <label class="info-title" for="contactno">Contact No. <span>*</span></label>
    <input type="text" class="form-control unicase-form-control text-input" id="contactno" name="contactno" maxlength="10" required pattern="[0-9]{10}" title="10 digit phone number" onBlur="checkContactAvailability()">
    <span id="contact-availability-status" style="font-size:12px;"></span>
</div>
            <div class="form-group">
                <label class="info-title" for="password">Password <span>*</span></label>
                <input type="password" class="form-control unicase-form-control text-input" id="password" name="password" required minlength="8" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number, one uppercase and lowercase letter, and at least 8 or more characters">
            </div>
            <div class="form-group">
                <label class="info-title" for="confirmpassword">Confirm Password <span>*</span></label>
                <input type="password" class="form-control unicase-form-control text-input" id="confirmpassword" name="confirmpassword" required>
            </div>
            <button type="submit" name="submit" class="btn-upper btn btn-primary checkout-page-button" id="submit">Sign Up</button>
        </form>
        <span class="checkout-subtitle outer-top-xs">Sign Up Today And You'll Be Able To :</span>
        <div class="checkbox">
            <label class="checkbox">Speed your way through the checkout.</label>
            <label class="checkbox">Track your orders easily.</label>
            <label class="checkbox">Keep a record of all your purchases.</label>
        </div>
    <?php } ?>
</div>
        <div id="loaderIcon" style="display:none;">
    <img src="assets/images/loader.gif" />
</div>        </div>
            </div>
        </div>
    </div>
    <?php include('includes/footer.php'); ?>
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
        $(document).ready(function() {
            $(".changecolor").switchstylesheet({ seperator: "color" });
            $('.show-theme-options').click(function() {
                $(this).parent().toggleClass('open');
                return false;
            });
        });
        $(window).bind("load", function() {
            $('.show-theme-options').delay(2000).trigger('click');
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php if (isset($_SESSION['smtp_debug']) && $_SESSION['smtp_debug'] != ""): ?>
    <pre><?php echo htmlspecialchars($_SESSION['smtp_debug']); ?></pre>
    <?php unset($_SESSION['smtp_debug']); endif; ?>
    <?php if (isset($_SESSION['smtp_error']) && $_SESSION['smtp_error'] != ""): ?>
    <pre><?php echo htmlspecialchars($_SESSION['smtp_error']); ?></pre>
    <?php unset($_SESSION['smtp_error']); endif; ?>
    <?php if (isset($_SESSION['otp_debug']) && $_SESSION['otp_debug'] != ""): ?>
    <pre><?php echo htmlspecialchars($_SESSION['otp_debug']); ?></pre>
    <?php unset($_SESSION['otp_debug']); endif; ?>
    <?php if (isset($_SESSION['session_debug']) && $_SESSION['session_debug'] != ""): ?>
    <pre><?php echo htmlspecialchars($_SESSION['session_debug']); ?></pre>
    <?php unset($_SESSION['session_debug']); endif; ?>
    <?php if (isset($_SESSION['time_debug']) && $_SESSION['time_debug'] != ""): ?>
    <pre><?php echo htmlspecialchars($_SESSION['time_debug']); ?></pre>
    <?php unset($_SESSION['time_debug']); endif; ?>
    <?php if (isset($_SESSION['login_success']) && $_SESSION['login_success'] != ""): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '<?php echo $_SESSION['login_success']; ?>',
            confirmButtonText: 'OK',
            confirmButtonColor: '#3085d6'
        }).then(() => {
            window.location.href = 'my-cart.php';
        });
    </script>
    <?php unset($_SESSION['login_success']); endif; ?>
    <?php if (isset($_SESSION['errmsg']) && $_SESSION['errmsg'] != ""): ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            html: '<?php echo $_SESSION['errmsg']; ?>',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'OK'
        });
    </script>
    <?php unset($_SESSION['errmsg']); endif; ?>
    <?php if (isset($_SESSION['registration_success']) && $_SESSION['registration_success'] != ""): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '<?php echo $_SESSION['registration_success']; ?>',
            confirmButtonText: 'OK',
            confirmButtonColor: '#3085d6'
        }).then(() => {
            window.location.href = 'login.php';
        });
    </script>
    <?php unset($_SESSION['registration_success']); endif; ?>
    <?php if (isset($_SESSION['registration_error']) && $_SESSION['registration_error'] != ""): ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Registration Failed',
            html: '<?php echo $_SESSION['registration_error']; ?>',
            confirmButtonText: 'OK',
            confirmButtonColor: '#3085d6'
        });
    </script>
    <?php unset($_SESSION['registration_error']); endif; ?>
    <script>
        document.getElementById('otp')?.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '').slice(0, 6);
        });
        document.getElementById('otp')?.addEventListener('paste', function(e) {
            e.preventDefault();
            const text = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, 6);
            this.value = text;
        });
    </script>
</body>
</html>