<?php
session_start();
error_reporting(0);
include('includes/config.php');

// Load Razorpay's PHP SDK
require('razorpay-php/Razorpay.php');
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

// Razorpay API Keys
$api_key = "rzp_live_GMPTAi3TWJL16X";            // Replace with your test key
$api_secret = "T7nERw33eG9wPl1tMvKGYJM6";       // Replace with your test secret

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $api = new Api($api_key, $api_secret);

    $attributes = [
        'razorpay_order_id' => $_POST['razorpay_order_id'],
        'razorpay_payment_id' => $_POST['razorpay_payment_id'],
        'razorpay_signature' => $_POST['razorpay_signature']
    ];

    try {
        // Signature verification
        $api->utility->verifyPaymentSignature($attributes);

        // If successful, update orders table
        $user_id = $_SESSION['id'];
        mysqli_query($con, "UPDATE orders SET paymentMethod='Razorpay' WHERE userId='$user_id' AND paymentMethod IS NULL");

        // Clear cart
        unset($_SESSION['cart']);

        echo json_encode(['status' => 'success']);
        exit;
    } catch (SignatureVerificationError $e) {
        echo json_encode([
            'status' => 'failure',
            'error' => 'Payment signature verification failed'
        ]);
        exit;
    }
}

echo json_encode(['status' => 'invalid_request']);
exit;
