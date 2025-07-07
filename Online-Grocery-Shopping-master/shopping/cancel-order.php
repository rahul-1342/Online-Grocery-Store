<?php
session_start();
include('includes/config.php');

if(!isset($_SESSION['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

// Get order ID from POST
$orderId = isset($_POST['order_number']) ? intval($_POST['order_number']) : 0;
$userId = $_SESSION['id'];

if($orderId <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid order ID']);
    exit;
}

// Begin transaction for atomic operations
mysqli_begin_transaction($con);

try {
    // Check if order exists and get its details
    $stmt = $con->prepare("SELECT id, parent_order_id, is_grouped, orderStatus 
                          FROM orders 
                          WHERE (id = ? OR parent_order_id = ?) 
                          AND userId = ?");
    $stmt->bind_param("iii", $orderId, $orderId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows === 0) {
        throw new Exception('Order not found');
    }

    $canCancel = true;
    $isGrouped = false;
    $orderStatus = null;
    
    // Check all order items' statuses
    while($row = $result->fetch_assoc()) {
        if(!in_array($row['orderStatus'], ['pending', 'processing'])) {
            $canCancel = false;
        }
        if($row['is_grouped'] == 1) {
            $isGrouped = true;
        }
        $orderStatus = $row['orderStatus'];
    }

    if(!$canCancel) {
        throw new Exception('This order cannot be cancelled at its current status');
    }

    // Cancel the order(s)
    if($isGrouped) {
        // Cancel entire order group (parent + all children)
        $updateStmt = $con->prepare("UPDATE orders 
                                    SET orderStatus = 'cancelled' 
                                    WHERE (id = ? OR parent_order_id = ?) 
                                    AND userId = ?");
        $updateStmt->bind_param("iii", $orderId, $orderId, $userId);
    } else {
        // Check if this is a child order
        $isChild = $con->query("SELECT 1 FROM orders WHERE id = $orderId AND parent_order_id IS NOT NULL")->num_rows > 0;
        
        if($isChild) {
            // Cancel single item in grouped order
            $updateStmt = $con->prepare("UPDATE orders 
                                        SET orderStatus = 'cancelled' 
                                        WHERE id = ? 
                                        AND userId = ?");
            $updateStmt->bind_param("ii", $orderId, $userId);
        } else {
            // Cancel regular single order
            $updateStmt = $con->prepare("UPDATE orders 
                                        SET orderStatus = 'cancelled' 
                                        WHERE id = ? 
                                        AND userId = ?");
            $updateStmt->bind_param("ii", $orderId, $userId);
        }
    }

    if(!$updateStmt->execute()) {
        throw new Exception('Failed to cancel the order');
    }

    // Update product stocks if needed
    if($isGrouped) {
        $items = $con->query("SELECT productId, quantity FROM orders 
                             WHERE (id = $orderId OR parent_order_id = $orderId) 
                             AND userId = $userId");
    } else {
        $items = $con->query("SELECT productId, quantity FROM orders 
                             WHERE id = $orderId AND userId = $userId");
    }

    while($item = $items->fetch_assoc()) {
        $con->query("UPDATE products SET stock = stock + {$item['quantity']} 
                     WHERE id = {$item['productId']}");
    }

    // Commit transaction if all went well
    mysqli_commit($con);
    
    echo json_encode([
        'status' => 'success', 
        'message' => $isGrouped ? 'Your order and all its items have been cancelled' : 'Your order has been cancelled',
        'is_grouped' => $isGrouped
    ]);

} catch (Exception $e) {
    // Rollback on error
    mysqli_rollback($con);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>