<?php
session_start();
include("includes/config.php");
require 'vendor/PHPMailer/PHPMailer.php';
require 'vendor/PHPMailer/SMTP.php';
require 'vendor/PHPMailer/Exception.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $order_id = intval($data['order_id']);
    
    // Get customer details
    $query = mysqli_query($con, "SELECT u.email, u.name FROM orders o 
        JOIN users u ON o.userId = u.id 
        WHERE o.id = $order_id");
    $customer = mysqli_fetch_assoc($query);
    
    if (!$customer) {
        echo json_encode(['success' => false, 'error' => 'Order not found']);
        exit;
    }
    
    // Generate new OTP
    $otp = rand(100000, 999999);
    $_SESSION['delivery_otp'] = $otp;
    $_SESSION['delivery_otp_order'] = $order_id;
    $_SESSION['delivery_otp_time'] = time();
    
    // Send OTP via email
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'jadhavmahesh3329@gmail.com';
        $mail->Password = 'gfir mzmm tqzl trlr';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('jadhavmahesh3329@gmail.com', 'Grocery Delivery');
        $mail->addAddress($customer['email'], $customer['name']);

        $mail->isHTML(true);
        $mail->Subject = 'Your New Delivery OTP';
        $mail->Body = "Hello {$customer['name']},<br><br>Your new OTP for order #$order_id is: <b>$otp</b><br>Valid for 15 minutes.<br><br>Thank you.";
        $mail->AltBody = "Hello {$customer['name']},\n\nYour new OTP for order #$order_id is: $otp\nValid for 15 minutes.\n\nThank you.";

        $mail->send();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Failed to send OTP: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
?>