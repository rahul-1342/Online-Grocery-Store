<?php
session_start();
include('includes/config.php');

if(strlen($_SESSION['login'])==0) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if (!empty($_SESSION['pending_order']['products'])) {
    $userId = $_SESSION['id'];
    $paymentMethod = 'Razorpay';

    mysqli_begin_transaction($con);

    try {
        $orderNumber = 'ORD' . str_replace('.', '', uniqid('', true));
        $totalAmount = $_SESSION['pending_order']['total'];

        $stmt = $con->prepare("INSERT INTO orders 
            (order_number, userId, paymentMethod, orderStatus, final_amount, is_grouped) 
            VALUES (?, ?, ?, 'pending', ?, 1)");
        $stmt->bind_param("sisi", $orderNumber, $userId, $paymentMethod, $totalAmount);
        $stmt->execute();
        $parentOrderId = $con->insert_id;

        foreach ($_SESSION['pending_order']['products'] as $productId => $item) {
            $productId = intval($productId);
            $qty = intval($item['quantity']);
            $price = floatval($item['price']);
            $shipping = floatval($item['shipping'] ?? 0.0);

            $stmt = $con->prepare("INSERT INTO order_items 
                (orderId, productId, quantity, price, shippingCharge) 
                VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iiidd", 
                $parentOrderId, 
                $productId, 
                $qty, 
                $price, 
                $shipping
            );
            $stmt->execute();
        }

        mysqli_commit($con);

        // Clear cart and pending order
        unset($_SESSION['cart']);
        unset($_SESSION['pending_order']);

        echo json_encode(['success' => true, 'order_number' => $orderNumber]);

    } catch (Exception $e) {
        mysqli_rollback($con);
        echo json_encode(['error' => 'Order creation failed']);
    }
} else {
    echo json_encode(['error' => 'Empty order']);
}
?>
