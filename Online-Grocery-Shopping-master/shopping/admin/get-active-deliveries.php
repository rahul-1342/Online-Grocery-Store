<?php
session_start();
include("include/config.php");

if(strlen($_SESSION['alogin']) == 0) {    
    header('Content-Type: application/json');
    die(json_encode(['success' => false, 'error' => 'Unauthorized']));
}

$query = mysqli_query($con, "SELECT 
    o.id as order_id, 
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
WHERE o.orderStatus IN ('Out for Delivery', 'Shipped')
ORDER BY dl.updated_at DESC");

$deliveries = [];
while($row = mysqli_fetch_assoc($query)) {
    $deliveries[] = $row;
}

header('Content-Type: application/json');
echo json_encode(['success' => true, 'deliveries' => $deliveries]);
?>