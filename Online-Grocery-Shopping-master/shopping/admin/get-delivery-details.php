<?php
session_start();
include("include/config.php");

if(strlen($_SESSION['alogin']) == 0) {    
    header('Content-Type: application/json');
    die(json_encode(['success' => false, 'error' => 'Unauthorized']));
}

$order_id = intval($_GET['order_id']);

$query = mysqli_query($con, "SELECT 
    o.id, 
    o.order_number, 
    o.orderStatus,
    u.name as customer_name,
    db.name as delivery_boy_name,
    dl.latitude,
    dl.longitude,
    dl.updated_at as last_update
FROM orders o
JOIN users u ON o.userId = u.id
JOIN delivery_boys db ON o.delivery_boy_id = db.id
LEFT JOIN delivery_locations dl ON o.id = dl.order_id
WHERE o.id = $order_id");

if(mysqli_num_rows($query) > 0) {
    $order = mysqli_fetch_assoc($query);
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'order' => $order]);
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Order not found']);
}
?>