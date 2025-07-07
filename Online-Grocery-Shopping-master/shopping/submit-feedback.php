<?php
session_start();
include('includes/config.php');

if(!isset($_SESSION['id'])) {
    die(json_encode(['success' => false, 'message' => 'Not logged in']));
}

if($_SERVER['REQUEST_METHOD'] != 'POST') {
    die(json_encode(['success' => false, 'message' => 'Invalid request']));
}

$order_id = intval($_POST['order_id']);
$user_id = $_SESSION['id'];
$rating = intval($_POST['rating']);
$comment = mysqli_real_escape_string($con, $_POST['comment']);

// Verify order belongs to user
$order_check = mysqli_query($con, "SELECT id FROM orders WHERE id='$order_id' AND userId='$user_id'");
if(mysqli_num_rows($order_check) == 0) {
    die(json_encode(['success' => false, 'message' => 'Invalid order']));
}

// Check for existing feedback
$feedback_check = mysqli_query($con, "SELECT id FROM order_feedback WHERE order_id='$order_id' AND user_id='$user_id'");
if(mysqli_num_rows($feedback_check) > 0) {
    die(json_encode(['success' => false, 'message' => 'Feedback already submitted']));
}

// Insert feedback
$insert = mysqli_query($con, "INSERT INTO order_feedback 
                            (order_id, user_id, rating, comment) 
                            VALUES 
                            ('$order_id', '$user_id', '$rating', '$comment')");

if($insert) {
    echo json_encode(['success' => true, 'message' => 'Feedback submitted']);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>