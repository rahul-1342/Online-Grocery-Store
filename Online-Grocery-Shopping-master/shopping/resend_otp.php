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

// Check if user is in OTP verification stage
if (!isset($_SESSION['temp_user']) || !isset($_SESSION['otp_sent'])) {
    header("Location: login.php");
    exit();
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

// Generate new OTP
$otp = rand(100000, 999999);
$_SESSION['otp'] = (string)$otp;
$_SESSION['otp_expiry'] = time() + 900; // 15 minutes expiry
$_SESSION['session_debug'] = "Resend OTP Set: $otp\nExpiry: " . date('Y-m-d H:i:s', $_SESSION['otp_expiry']) . "\nSession ID: " . session_id();

$email = $_SESSION['temp_user']['email'];
$name = $_SESSION['temp_user']['name'];

if (sendOTPEmail($email, $otp, $name)) {
    $_SESSION['registration_success'] = "OTP resent successfully";
} else {
    $_SESSION['registration_error'] = "Failed to resend OTP";
}
header("Location: login.php");
exit();
?>