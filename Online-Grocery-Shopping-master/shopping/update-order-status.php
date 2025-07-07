<?php
session_start();
include("includes/config.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = intval($_POST['order_id']);
    
    // Verify OTP was previously verified
    if (!isset($_SESSION['otp_verified']) || $_SESSION['verified_order_id'] != $order_id) {
        echo json_encode(['success' => false, 'error' => 'OTP verification required before marking as delivered']);
        exit;
    }
    
    // Update order status
    $stmt = $con->prepare("UPDATE orders SET orderStatus = 'Delivered' WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    
    if ($stmt->execute()) {
        // Clear verification flags
        unset($_SESSION['otp_verified']);
        unset($_SESSION['verified_order_id']);
        
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Database update failed']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
?>