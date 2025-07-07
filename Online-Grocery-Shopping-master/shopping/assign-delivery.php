<?php
session_start();
include("includes/config.php");

if(!isset($_SESSION['alogin'])) {
    http_response_code(403);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$order_id = intval($data['order_id']);
$boy_id = intval($data['delivery_boy_id']);

mysqli_query($con, "UPDATE orders SET delivery_boy_id = $boy_id WHERE id = $order_id");
echo json_encode(['success' => true]);
?>