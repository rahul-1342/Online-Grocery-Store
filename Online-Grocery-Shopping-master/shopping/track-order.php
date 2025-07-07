<?php
session_start();
include_once 'includes/config.php';
$oid = intval($_GET['oid'] ?? 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Order Tracking Details</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Fonts and Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/green.css">

    <style>
    body {
        font-family: 'Poppins', sans-serif;
        background: #f7f7f7;
    }

    .tracking-container {
        margin: 40px auto;
        max-width: 900px;
        padding: 40px;
        background: #ffffff;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease-in-out;
    }

    .tracking-header {
        color: #4caf50;
        margin-bottom: 35px;
        border-bottom: 2px dashed #d0d0d0;
        padding-bottom: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .tracking-header h2 {
        margin: 0;
        font-weight: 600;
        font-size: 24px;
    }

    .btn-print {
        background: linear-gradient(to right, #4caf50, #2e7d32);
        color: white;
        border: none;
        padding: 10px 22px;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s ease-in-out;
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    }

    .btn-print:hover {
        background: linear-gradient(to right, #388e3c, #1b5e20);
        transform: scale(1.03);
    }

    .order-details th {
        background: #f1f8e9;
        font-weight: 600;
        color: #2e7d32;
    }

    .table.order-details td, .table.order-details th {
        vertical-align: middle;
    }

    .product-row:hover {
        background-color: #f9f9f9;
    }

    .badge-success {
        background-color: #66bb6a;
        padding: 6px 12px;
        font-size: 13px;
        border-radius: 50px;
    }

    .row.mb-5  {
        background-color: #f9fafc;
        padding: 25px;
        border-radius: 12px;
        border: 1px solid #e3e3e3;
        display:flex;
    }

    .grand-total {
        font-size: 1.2rem;
        color: #2e7d32;
        font-weight: bold;
    }

    .print-footer {
        text-align: center;
        margin-top: 40px;
        padding-top: 30px;
        border-top: 1px solid #e0e0e0;
        color: #555;
    }

    .print-footer img {
        max-width: 140px;
        margin-top: 20px;
    }

    /* Status History Styles */
    .status-history {
        margin-top: 40px;
        border-top: 1px solid #eee;
        padding-top: 20px;
    }
    
    .status-item {
        position: relative;
        padding-left: 30px;
        margin-bottom: 20px;
        border-left: 2px solid #4caf50;
    }
    
    .status-date {
        font-weight: 600;
        color: #555;
    }
    
    .status-badge {
        position: absolute;
        left: -12px;
        top: 0;
        background: #4caf50;
        color: white;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        text-align: center;
        line-height: 24px;
        font-size: 12px;
    }
    
    .status-remark {
        background: #f5f5f5;
        padding: 10px 15px;
        border-radius: 5px;
        margin-top: 5px;
        font-style: italic;
    }

    /* Print Styles */
    @media print {
        body { 
            background: white !important;
            font-size: 12pt;
            padding: 0;
            margin: 0;
        }
        .tracking-container {
            width: 100%;
            margin: 0;
            padding: 20px;
            box-shadow: none;
            border: none;
        }
        .no-print, .btn-print {
            display: none !important;
        }
        table {
            page-break-inside: auto;
            width: 100% !important;
            border-collapse: collapse !important;
        }
        tr {
            page-break-inside: avoid;
        }
        th, td {
            border: 1px solid #ddd !important;
            padding: 8px;
        }
    }
    </style>
</head>
<body class="cnt-home">
    <header class="header-style-1">
        <?php include('includes/top-header.php'); ?>
        <?php include('includes/main-header.php'); ?>
        <?php include('includes/menu-bar.php'); ?>
    </header>

    <div class="breadcrumb">
        <div class="container">
            <div class="breadcrumb-inner">
                <ul class="list-inline list-unstyled">
                    <li><a href="index.php">Home</a></li>
                    <li class="active">Order Tracking</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="body-content outer-top-xs">
        <div class="container">
            <div class="tracking-container">
                <div class="tracking-header">
                    <h2><i class="fa fa-truck"></i> Order Tracking Details</h2>
                    <button onclick="printInvoice()" class="btn-print">
                        <i class="fa fa-print"></i> Print Invoice
                    </button>
                </div>

                <?php if ($oid <= 0): ?>
    <div class="alert alert-danger">Invalid Order ID</div>
<?php else: 
    $stmt = $con->prepare("SELECT o.orderDate, o.paymentMethod, o.orderStatus, o.final_amount, 
        o.shipping_charge, o.tax_amount, o.discount_amount, o.productId, o.quantity,
        o.parent_order_id, o.is_grouped, o.order_number,
        u.name as customerName, u.email, u.contactno, u.shippingAddress, u.shipping_building, 
        u.shipping_house, u.shipping_landmark
        FROM orders o 
        JOIN users u ON o.userId = u.id 
        WHERE o.id = ? OR o.parent_order_id = ?");
    $stmt->bind_param("ii", $oid, $oid);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()): ?>
                        <div class="row mb-5">
                            <div class="col-md-6 pr-md-5 mb-4 mb-md-0">
                                <h4 class="mb-3"><i class="fa fa-user"></i> Customer Information</h4>
                                <div style="line-height: 1.8; font-size: 15px;">
                                    <strong><?php echo htmlspecialchars($row['customerName']); ?></strong><br>
                                    <?php echo htmlspecialchars($row['email']); ?><br>
                                    <span>Phone:</span> <?php echo htmlspecialchars($row['contactno']); ?>
                                </div><br/>

                                <h4 class="mt-4 mb-3"><i class="fa fa-map-marker"></i> Shipping Address</h4>
                                <div style="line-height: 1.8; font-size: 15px;">
                                    <?php echo nl2br(htmlspecialchars($row['shippingAddress'])); ?><br>
                                    <?php echo htmlspecialchars($row['shipping_building']); ?>, 
                                    <?php echo htmlspecialchars($row['shipping_house']); ?> 
                                    <?php echo htmlspecialchars($row['shipping_building']); ?>
                                </div>
                            </div>
                            <?php 
// Check if this is a grouped order - MOVE THIS BEFORE THE ORDER INFORMATION SECTION
$isGrouped = isset($row['is_grouped']) && $row['is_grouped'] == 1;
$parentOrderId = $isGrouped ? $oid : ($row['parent_order_id'] ?? 0);
?>
<div class="col-md-6 pl-md-5">
    <h4 class="mb-3"><i class="fa fa-file-text-o"></i> Order Information</h4>
    <div style="line-height: 1.8; font-size: 15px;">
        <strong>Order ID:</strong> #<?php echo $oid; ?><br>
        <?php if (!empty($row['order_number'])): ?>
            <strong>Order Number:</strong> <?php echo htmlspecialchars($row['order_number']); ?><br>
        <?php endif; ?>
        <strong>Date:</strong> <?php echo date('d M Y, h:i A', strtotime($row['orderDate'])); ?><br>
        <strong>Status:</strong> 
        <span class="badge badge-success"><?php echo htmlspecialchars($row['orderStatus']); ?></span><br>
        <strong>Payment Method:</strong> <?php echo htmlspecialchars($row['paymentMethod']); ?>
        <?php if ($isGrouped): ?>
            <br><span class="badge badge-info">Grouped Order</span>
        <?php endif; ?>
    </div>
</div>
                        </div>

                        <h4><i class="fa fa-cube"></i> Order Items</h4>
<table class="table table-bordered order-details">
    <thead>
        <tr>
            <th>Product</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Get all products in this order
        $productQuery = $con->prepare("SELECT 
            p.productName, 
            p.productPrice, 
            oi.quantity, 
            p.id as product_id,
            p.productImage1 as product_image
        FROM order_items oi
        JOIN products p ON oi.productId = p.id
        WHERE oi.orderId = ?");
        $productQuery->bind_param("i", $oid);
        $productQuery->execute();
        $productResult = $productQuery->get_result();

        $subtotal = 0;
        while ($productRow = $productResult->fetch_assoc()):
            $itemTotal = $productRow['quantity'] * $productRow['productPrice'];
            $subtotal += $itemTotal;
        ?>
        <tr class="product-row">
            <td>
            <?php if ($productRow['product_image']): ?>
                    <br>
                    <img src="admin/productimages/<?php echo $productRow['product_id']; ?>/<?php echo $productRow['product_image']; ?>" 
                         width="60" class="mt-2">
                <?php endif; ?>&nbsp;&nbsp;&nbsp;
                <?php echo htmlspecialchars($productRow['productName']); ?><br>
                
            </td>
            <td><?php echo htmlspecialchars($productRow['quantity']); ?></td>
            <td>₹<?php echo number_format($productRow['productPrice'], 2); ?></td>
            <td>₹<?php echo number_format($itemTotal, 2); ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
                        <div class="row justify-content-end">
                            <div class="col-md-5">
                                <table class="table">
<tr>
    <th>Subtotal:</th>
    <td>₹<?php echo number_format($subtotal, 2); ?></td>
</tr>
                                    <tr>
                                        <th>Shipping:</th>
                                        <td>₹<?php echo number_format($row['shipping_charge'], 2); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Tax:</th>
                                        <td>₹<?php echo number_format($row['tax_amount'], 2); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Discount:</th>
                                        <td>-₹<?php echo number_format($row['discount_amount'], 2); ?></td>
                                    </tr>
                                    <tr class="grand-total">
                                        <th>Grand Total:</th>
                                        <td>₹<?php echo number_format($row['final_amount'], 2); ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Status History Section -->
                        <div class="status-history">
                            <h4><i class="fa fa-history"></i> Order Status History</h4>
                            
                            <?php
                            // Fetch status history for this order
                            $historyQuery = $con->prepare("SELECT status, remark, postingDate 
                                                         FROM ordertrackhistory 
                                                         WHERE orderId = ? 
                                                         ORDER BY postingDate DESC");
                            $historyQuery->bind_param("i", $oid);
                            $historyQuery->execute();
                            $historyResult = $historyQuery->get_result();
                            
                            if ($historyResult->num_rows > 0): ?>
                                <?php while ($history = $historyResult->fetch_assoc()): ?>
                                    <div class="status-item">
                                        <div class="status-badge">
                                            <i class="fa fa-check"></i>
                                        </div>
                                        <div class="status-date">
                                            <?php echo date('d M Y, h:i A', strtotime($history['postingDate'])); ?>
                                        </div>
                                        <div class="status-name">
                                            <strong><?php echo htmlspecialchars($history['status']); ?></strong>
                                        </div>
                                        <?php if (!empty($history['remark'])): ?>
                                            <div class="status-remark">
                                                <?php echo htmlspecialchars($history['remark']); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <p>No status history available for this order.</p>
                            <?php endif; ?>
                        </div>
                        
                    <?php endif; ?>
                <?php endif; ?>

                <div class="print-footer">
                    <p>Thank you for your order!</p>
                    <p>If you have any questions, please contact our customer service.</p>
                </div>
            </div>
        </div>
    </div>

    <?php include('includes/footer.php'); ?>

    <!-- Scripts -->
    <script src="assets/js/jquery-1.11.1.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/bootstrap-hover-dropdown.min.js"></script>
    <script src="assets/js/scripts.js"></script>
    
    <!-- Print Function -->
    <script>
    function printInvoice() {
        // Store original content and scroll position
        const originalContent = document.body.innerHTML;
        const scrollPosition = window.pageYOffset;
        
        // Get the content we want to print
        const printContent = document.querySelector('.tracking-container').outerHTML;
        
        // Create print-specific styles
        const printStyles = 
            `<style>
                @page { size: auto; margin: 5mm; }
                body { 
                    font-family: 'Poppins', sans-serif; 
                    background: white !important; 
                    font-size: 12pt;
                    padding: 10px !important;
                }
                .tracking-container { 
                    width: 100% !important; 
                    margin: 0 !important; 
                    padding: 0 !important;
                    box-shadow: none !important;
                    border: none !important;
                }
                table { 
                    page-break-inside: auto !important; 
                    width: 100% !important;
                }
                tr { page-break-inside: avoid !important; }
                .no-print { display: none !important; }
                th, td {
                    border: 1px solid #ddd !important;
                    padding: 8px !important;
                }
                .status-item {
                    page-break-inside: avoid;
                }
            </style>`;
        
        // Replace body content with just what we want to print
        document.body.innerHTML = printStyles + printContent;
        
        // Print the page
        window.print();
        
        // Restore original content and scroll position
        document.body.innerHTML = originalContent;
        window.scrollTo(0, scrollPosition);
    }
    </script>
</body>
</html>