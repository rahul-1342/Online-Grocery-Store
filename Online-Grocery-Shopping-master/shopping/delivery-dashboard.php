<?php
session_start();
include("includes/config.php");

if (!isset($_SESSION['delivery_boy_id'])) {
    header("Location: delivery-login.php");
    exit;
}

$boy_id = $_SESSION['delivery_boy_id'];
$boy_name = $_SESSION['delivery_boy_name'] ?? 'Delivery Partner';

// Debug current assignments
error_log("Loading dashboard for delivery boy ID: $boy_id");

// Update status if requested
if (isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $status = mysqli_real_escape_string($con, $_POST['status']);
    
    // Check if order is assigned to this delivery boy
    $order_check = mysqli_query($con, "SELECT orderStatus FROM orders 
                     WHERE id='$order_id' AND delivery_boy_id='$boy_id'");
    
    if(mysqli_num_rows($order_check) == 0) {
        $_SESSION['msg'] = "Order not assigned to you";
        header("Location: delivery-dashboard.php");
        exit;
    }
    
    $order_data = mysqli_fetch_assoc($order_check);
    
    if ($order_data['orderStatus'] == 'Delivered') {
        $_SESSION['msg'] = "Cannot modify status - order already delivered";
        header("Location: delivery-dashboard.php");
        exit;
    }
    
    // For "Delivered" status, redirect to OTP verification
    if ($status == 'Delivered') {
        $_SESSION['delivery_to_verify'] = $order_id;
        header("Location: start-delivery.php?order_id=$order_id");
        exit;
    }
    
    // Update status
    $update_result = mysqli_query($con, "UPDATE orders SET orderStatus='$status' 
        WHERE id='$order_id' AND delivery_boy_id='$boy_id'");
    
    if($update_result) {
        // Add to track history
        mysqli_query($con, "INSERT INTO ordertrackhistory(orderId, status, remark, postingDate) 
                          VALUES('$order_id', '$status', 'Status updated by delivery boy', NOW())");
        
        $_SESSION['msg'] = "Order status updated to $status successfully";
    } else {
        $_SESSION['msg'] = "Error updating status: ".mysqli_error($con);
    }
    
    header("Location: delivery-dashboard.php");
    exit;
}

// Get assigned orders (including newly assigned ones)
$active_orders = mysqli_query($con, "
    SELECT o.*, u.name AS username, u.contactno, 
           CONCAT(u.shippingAddress, ', ', u.shipping_building, ', ', 
                  u.shipping_house) AS fullAddress
    FROM orders o 
    JOIN users u ON o.userId = u.id 
    WHERE o.delivery_boy_id = $boy_id 
    AND o.orderStatus IN ('Assigned', 'Shipped', 'Out for Delivery')
    ORDER BY FIELD(o.orderStatus, 'Out for Delivery', 'Shipped', 'Assigned'),
             o.orderDate DESC
");

// Then use $active_orders everywhere instead of $res

// Get delivered orders (last 30 days)
$delivered_orders = mysqli_query($con, "
    SELECT o.*, u.name AS username, u.contactno, 
           CONCAT(u.shippingAddress, ', ', u.shipping_building, ', ', 
                  u.shipping_house, ' - ', u.shipping_landmark) AS fullAddress,
           o.delivery_confirmed_at
    FROM orders o 
    JOIN users u ON o.userId = u.id 
    WHERE o.delivery_boy_id = $boy_id 
    AND o.orderStatus = 'Delivered'
    AND o.delivery_confirmed_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    ORDER BY o.delivery_confirmed_at DESC
    LIMIT 50
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            position: relative;
        }
        
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiPjxkZWZzPjxwYXR0ZXJuIGlkPSJwYXR0ZXJuIiB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHBhdHRlcm5Vbml0cz0idXNlclNwYWNlT25Vc2UiIHBhdHRlcm5UcmFuc2Zvcm09InJvdGF0ZSg0NSkiPjxyZWN0IHdpZHRoPSIyMCIgaGVpZ2h0PSIyMCIgZmlsbD0icmdiYSgyNTUsMjU1LDI1NSwwLjEpIi8+PC9wYXR0ZXJuPjwvZGVmcz48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSJ1cmwoI3BhdHRlcm4pIi8+PC9zdmc+');
            opacity: 0.6;
            z-index: -1;
        }
        
        .dashboard-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin: 30px auto;
            padding: 30px;
            position: relative;
            overflow: hidden;
            max-width: 1200px;
        }
        
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        
        .welcome-message h2 {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .welcome-message p {
            color: #7f8c8d;
            margin: 0;
        }
        
        .btn-logout {
            background: var(--accent-color);
            border: none;
            border-radius: 8px;
            padding: 8px 20px;
            color: white;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-logout:hover {
            background: #c0392b;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border-radius: 8px;
        }
        
        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
        }
        
        .table {
            margin-bottom: 0;
        }
        
        .table thead th {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 15px;
        }
        
        .table tbody tr {
            transition: all 0.2s;
        }
        
        .table tbody tr:hover {
            background-color: #f8f9fa;
            transform: translateY(-1px);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        .table tbody td {
            padding: 12px 15px;
            vertical-align: middle;
            border-color: #eee;
        }
        
        .status-select {
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            padding: 6px 12px;
            width: 100%;
            transition: all 0.3s;
        }
        
        .status-select:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }
        
        .btn-start-delivery {
            background: var(--secondary-color);
            border: none;
            border-radius: 8px;
            padding: 6px 12px;
            color: white;
            font-weight: 500;
            transition: all 0.3s;
            width: 100%;
            text-align: center;
            display: block;
        }
        
        .btn-start-delivery:hover {
            background: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .no-orders {
            background: white;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
        }
        
        .delivery-decoration {
            position: absolute;
            opacity: 0.1;
            z-index: 0;
        }
        
        .delivery-decoration.top-left {
            top: -30px;
            left: -30px;
            font-size: 100px;
            color: var(--secondary-color);
        }
        
        .delivery-decoration.bottom-right {
            bottom: -30px;
            right: -30px;
            font-size: 100px;
            color: var(--primary-color);
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .status-assigned {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-shipped {
            background: #cce5ff;
            color: #004085;
        }
        
        .status-out {
            background: #d4edda;
            color: #155724;
        }
        
        .status-delivered {
            background: #d1ecf1;
            color: #0c5460;
        }
        .delivered-orders-table tbody tr {
        opacity: 0.8;
        transition: opacity 0.2s;
    }
    
    .delivered-orders-table tbody tr:hover {
        opacity: 1;
    }
    
    .delivered-orders-table td {
        color: #666;
    }
    
    .status-delivered {
        background: #d1e7dd;
        color: #0f5132;
    }
        
        @media (max-width: 768px) {
            .dashboard-container {
                margin: 15px;
                padding: 20px;
            }
            
            .dashboard-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .btn-logout {
                margin-top: 15px;
                width: 100%;
            }
            
            .table thead {
                display: none;
            }
            
            .table, .table tbody, .table tr, .table td {
                display: block;
                width: 100%;
            }
            
            .table tr {
                margin-bottom: 20px;
                position: relative;
            }
            
            .table td {
                text-align: right;
                padding-left: 50%;
                position: relative;
                border-bottom: 1px solid #eee;
            }
            
            .table td::before {
                content: attr(data-label);
                position: absolute;
                left: 15px;
                width: calc(50% - 15px);
                padding-right: 15px;
                text-align: left;
                font-weight: bold;
                color: var(--primary-color);
            }
            
            .delivery-decoration {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Decorative elements -->
        <i class="fas fa-motorcycle delivery-decoration top-left"></i>
        <i class="fas fa-map-marked-alt delivery-decoration bottom-right"></i>
        
        <div class="dashboard-header">
            <div class="welcome-message">
                <h2>Welcome, <?= htmlspecialchars($boy_name) ?></h2>
                <p>Manage your delivery orders</p>
            </div>
            <a href="del-logout.php" class="btn btn-logout">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
        
        <?php if(isset($_SESSION['msg'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <h3 class="mb-4" style="color: var(--primary-color);">Your Delivery Orders</h3>
        
        <?php if(mysqli_num_rows($active_orders) > 0): ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Contact</th>
                            <th>Delivery Address</th>
                            <th>Order Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($active_orders)): 
                            $statusClass = strtolower(str_replace(' ', '-', $row['orderStatus']));
                        ?>
                        <tr>
                            <td data-label="Order ID"><?= $row['id'] ?></td>
                            <td data-label="Customer"><?= htmlspecialchars($row['username']) ?></td>
                            <td data-label="Contact"><?= htmlspecialchars($row['contactno']) ?></td>
                            <td data-label="Address"><?= htmlspecialchars($row['fullAddress']) ?></td>
                            <td data-label="Order Date"><?= date('d M Y h:i A', strtotime($row['orderDate'])) ?></td>
                            <td data-label="Status">
                                <form method="post" class="status-form">
                                    <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                                    <input type="hidden" name="update_status" value="1">
                                    <select name="status" class="status-select">
                                        <option value="Assigned" <?= $row['orderStatus']=='Assigned'?'selected':''?>>Assigned</option>
                                        <option value="Shipped" <?= $row['orderStatus']=='Shipped'?'selected':''?>>Shipped</option>
                                        <option value="Out for Delivery" <?= $row['orderStatus']=='Out for Delivery'?'selected':''?>>Out for Delivery</option>
                                        <option value="Delivered" <?= $row['orderStatus']=='Delivered'?'selected':''?>>Delivered</option>
                                    </select>
                                </form>
                                <div class="status-badge status-<?= $statusClass ?> mt-1">
                                    <?= $row['orderStatus'] ?>
                                </div>
                            </td>
                            <td data-label="Action">
                                <a href="start-delivery.php?order_id=<?= $row['id'] ?>" 
                                   class="btn btn-start-delivery">
                                    <i class="fas fa-truck"></i> Start Delivery
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="no-orders">
                <i class="fas fa-box-open" style="font-size: 3rem; color: #adb5bd; margin-bottom: 15px;"></i>
                <h4 style="color: var(--primary-color);">No Orders Assigned</h4>
                <p class="text-muted">You currently don't have any orders assigned to you.</p>
            </div>
        <?php endif; ?>
        <!-- Delivered Orders Section -->
<div class="mt-5">
    <h3 class="mb-4" style="color: var(--primary-color);">
        <i class="fas fa-check-circle"></i> Recently Delivered Orders
    </h3>
    
    <?php if(mysqli_num_rows($delivered_orders) > 0): ?>
        <div class="table-responsive">
            <table class="table delivered-orders-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Delivery Address</th>
                        <th>Order Date</th>
                        <th>Delivered On</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($delivered_orders)): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= htmlspecialchars($row['fullAddress']) ?></td>
                        <td><?= date('d M Y', strtotime($row['orderDate'])) ?></td>
                        <td><?= date('d M Y h:i A', strtotime($row['delivery_confirmed_at'])) ?></td>
                        <td>
                            <span class="status-badge status-delivered">
                                Delivered
                            </span>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="no-orders">
            <i class="fas fa-box-open" style="font-size: 3rem; color: #adb5bd; margin-bottom: 15px;"></i>
            <p class="text-muted">No delivered orders found in the last 30 days</p>
        </div>
    <?php endif; ?>
</div>
    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.querySelectorAll('.status-form').forEach(form => {
        const select = form.querySelector('select[name="status"]');
        
        select.addEventListener('change', function() {
            const status = this.value;
            const orderId = form.querySelector('input[name="order_id"]').value;
            
            if (status === 'Delivered') {
                if (!confirm('Mark as delivered? This will require OTP verification and cannot be undone.')) {
                    this.value = this.dataset.previous;
                    return;
                }
                // The form will submit normally and redirect to OTP verification
                form.submit();
            } else {
                if (!confirm(`Change status to ${status}?`)) {
                    this.value = this.dataset.previous;
                    return;
                }
                
                // Show loading indicator
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                this.disabled = true;
                
                fetch('update-order-status.php', {
                    method: 'POST',
                    body: new FormData(form)
                })
                .then(response => {
                    if (!response.ok) throw new Error('Network error');
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Update UI
                        const badge = form.nextElementSibling;
                        badge.className = `status-badge status-${status.toLowerCase().replace(' ', '-')}`;
                        badge.textContent = status;
                        this.dataset.previous = status;
                        
                        // Show success message
                        showAlert(`Status updated to ${status}`, 'success');
                        
                        // If status is delivered, reload the page to remove from list
                        if (status === 'Delivered') {
                            setTimeout(() => location.reload(), 1500);
                        }
                    } else {
                        throw new Error(data.error || 'Update failed');
                    }
                })
                .catch(error => {
                    this.value = this.dataset.previous;
                    showAlert(error.message, 'danger');
                })
                .finally(() => {
                    this.innerHTML = originalText;
                    this.disabled = false;
                });
            }
        });
        
        // Store initial value
        select.dataset.previous = select.value;
    });
    
    function showAlert(message, type) {
        // Remove existing alerts
        document.querySelectorAll('.alert-dismissible').forEach(alert => {
            alert.remove();
        });
        
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show`;
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.querySelector('.dashboard-container').prepend(alert);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    }
</script>
</body>
</html>