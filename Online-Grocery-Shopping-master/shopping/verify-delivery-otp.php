<?php
session_start();
include("includes/config.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = intval($_POST['order_id']);
    $entered_otp = $_POST['otp'];
    $delivery_boy_id = $_SESSION['delivery_boy_id'] ?? 0;
    
    // Verify OTP from session
    if (!isset($_SESSION['delivery_otp']) || !isset($_SESSION['delivery_otp_order']) || 
        $_SESSION['delivery_otp_order'] != $order_id) {
        echo json_encode(['success' => false, 'error' => 'OTP session expired or invalid']);
        exit;
    }
    
    // Check OTP expiration (15 minutes)
    if (time() - $_SESSION['delivery_otp_time'] > 900) {
        echo json_encode(['success' => false, 'error' => 'OTP expired']);
        exit;
    }
    
    // Verify OTP
    if ($_SESSION['delivery_otp'] == $entered_otp) {
        // Update order status and set delivery timestamp
        $update_query = "UPDATE orders 
                        SET orderStatus = 'Delivered', 
                            delivery_confirmed_at = NOW() 
                        WHERE id = ? AND delivery_boy_id = ?";
        
        $stmt = $con->prepare($update_query);
        $stmt->bind_param("ii", $order_id, $delivery_boy_id);
        
        if ($stmt->execute()) {
            // Clear OTP session data
            unset($_SESSION['delivery_otp']);
            unset($_SESSION['delivery_otp_order']);
            unset($_SESSION['delivery_otp_time']);
            
            // Mark OTP as verified in session
            $_SESSION['otp_verified'] = true;
            $_SESSION['verified_order_id'] = $order_id;
            
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to update order status']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid OTP']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
?>