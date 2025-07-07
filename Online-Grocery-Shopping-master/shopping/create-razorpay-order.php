<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Create detailed log file
$logFile = 'razorpay_debug.log';
file_put_contents($logFile, "\n\n" . date('Y-m-d H:i:s') . " - New order creation started\n", FILE_APPEND);

require('razorpay-php/Razorpay.php');
use Razorpay\Api\Api;

include('includes/config.php');

// Log session and POST data
file_put_contents($logFile, "SESSION DATA:\n" . print_r($_SESSION, true) . "\n", FILE_APPEND);
file_put_contents($logFile, "POST DATA:\n" . print_r($_POST, true) . "\n", FILE_APPEND);

// Validate session
if (empty($_SESSION['id'])) {
    $error = "User not logged in";
    file_put_contents($logFile, "ERROR: $error\n", FILE_APPEND);
    http_response_code(401);
    die(json_encode(['error' => $error]));
}

if (empty($_SESSION['cart'])) {
    $error = "Cart is empty";
    file_put_contents($logFile, "ERROR: $error\n", FILE_APPEND);
    http_response_code(400);
    die(json_encode(['error' => $error]));
}

// Validate input
$amount = isset($_POST['amount']) ? intval($_POST['amount']) : 0;
$currency = isset($_POST['currency']) ? $_POST['currency'] : 'INR';

if ($amount <= 0) {
    $error = "Invalid amount: $amount";
    file_put_contents($logFile, "ERROR: $error\n", FILE_APPEND);
    http_response_code(400);
    die(json_encode(['error' => $error]));
}

try {
    // Initialize Razorpay client
    $api = new Api("rzp_live_GMPTAi3TWJL16X", "T7nERw33eG9wPl1tMvKGYJM6");
    file_put_contents($logFile, "Razorpay client initialized\n", FILE_APPEND);

    // Create order parameters
    $orderData = [
        'amount' => $amount,
        'currency' => $currency,
        'payment_capture' => 1,
        'notes' => [
            'merchant_order_id' => 'TEMP_' . uniqid(),
            'user_id' => $_SESSION['id']
        ]
    ];
    
    file_put_contents($logFile, "Creating order with data:\n" . print_r($orderData, true) . "\n", FILE_APPEND);

    // Create Razorpay order
    $order = $api->order->create($orderData);
    $orderArray = $order->toArray();
    
    file_put_contents($logFile, "Order created successfully:\n" . print_r($orderArray, true) . "\n", FILE_APPEND);

    // Prepare session data
    $pendingOrder = [
        'razorpay_order_id' => $orderArray['id'],
        'total' => $amount / 100, // Convert back to rupees
        'products' => []
    ];

    // Store product details
    foreach ($_SESSION['cart'] as $productId => $details) {
        $productId = intval($productId);
        $qty = intval($details['quantity']);
        
        $result = mysqli_query($con, "SELECT productPrice, shippingCharge FROM products WHERE id = '$productId'");
        if ($row = mysqli_fetch_assoc($result)) {
            $pendingOrder['products'][$productId] = [
                'quantity' => $qty,
                'price' => $row['productPrice'],
                'shipping' => $row['shippingCharge']
            ];
        }
    }

    $_SESSION['pending_order'] = $pendingOrder;
    file_put_contents($logFile, "Session updated with pending order:\n" . print_r($pendingOrder, true) . "\n", FILE_APPEND);

    // Return success response
    $response = [
        'id' => $orderArray['id'],
        'amount' => $orderArray['amount'],
        'currency' => $orderArray['currency'],
        'status' => 'created'
    ];
    
    file_put_contents($logFile, "Returning response:\n" . print_r($response, true) . "\n", FILE_APPEND);
    echo json_encode($response);

} catch (Exception $e) {
    $error = "Order creation failed: " . $e->getMessage();
    file_put_contents($logFile, "EXCEPTION: $error\n", FILE_APPEND);
    http_response_code(500);
    echo json_encode(['error' => $error]);
}