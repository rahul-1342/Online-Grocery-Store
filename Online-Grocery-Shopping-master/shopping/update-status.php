<?php
session_start();
include("includes/config.php");

if (!isset($_SESSION['delivery_boy_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
    exit;
}

$order_id = intval($_POST['order_id']);
$status = mysqli_real_escape_string($con, $_POST['status']);
$delivery_boy_id = $_SESSION['delivery_boy_id'];

$result = mysqli_query($con, "UPDATE orders SET orderStatus = '$status' 
    WHERE id = $order_id AND delivery_boy_id = $delivery_boy_id");

if ($result) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => mysqli_error($con)]);
}
?>