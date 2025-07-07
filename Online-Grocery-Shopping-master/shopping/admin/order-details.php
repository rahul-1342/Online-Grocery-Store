<?php
session_start();
include('include/config.php');
if(strlen($_SESSION['alogin'])==0) { 
    header('location:index.php');
}

$orderId = intval($_GET['id']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Order Details</title>
    <!-- Include your CSS/JS files -->
</head>
<body>
    <?php 
    $orderQuery = mysqli_query($con,"SELECT * FROM orders WHERE id='$orderId'");
    $order = mysqli_fetch_assoc($orderQuery);
    
    if($order['is_grouped']) {
        $itemsQuery = mysqli_query($con,"SELECT 
            o.*, p.productName, p.productPrice 
            FROM orders o 
            JOIN products p ON o.productId = p.id 
            WHERE o.parent_order_id='$orderId'");
    } else {
        $itemsQuery = mysqli_query($con,"SELECT 
            o.*, p.productName, p.productPrice 
            FROM orders o 
            JOIN products p ON o.productId = p.id 
            WHERE o.id='$orderId'");
    }
    ?>
    
    <h2>Order #<?php echo $order['order_number']; ?></h2>
    
    <table class="table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Qty</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php while($item = mysqli_fetch_assoc($itemsQuery)): ?>
            <tr>
                <td><?php echo $item['productName']; ?></td>
                <td>₹<?php echo number_format($item['productPrice'], 2); ?></td>
                <td><?php echo $item['quantity']; ?></td>
                <td>₹<?php echo number_format($item['productPrice'] * $item['quantity'], 2); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>