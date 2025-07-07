<?php
include("includes/config.php");

$order_id = intval($_GET['order_id']);

$res = mysqli_query($con, "
    SELECT latitude, longitude 
    FROM delivery_locations 
    WHERE order_id = $order_id 
    ORDER BY updated_at DESC 
    LIMIT 1
");

if ($row = mysqli_fetch_assoc($res)) {
    echo json_encode([
        'success' => true,
        'lat' => $row['latitude'],
        'lon' => $row['longitude']
    ]);
} else {
    echo json_encode(['success' => false]);
}
