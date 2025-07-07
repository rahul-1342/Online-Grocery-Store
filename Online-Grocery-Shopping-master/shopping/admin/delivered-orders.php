<?php
session_start();
include('include/config.php');
if(strlen($_SESSION['alogin'])==0) {    
    header('location:index.php');
} else {
    date_default_timezone_set('Asia/Kolkata');
    $currentTime = date('d-m-Y h:i:s A', time());
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Delivered Orders</title>
    
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="bootstrap/css/bootstrap-responsive.min.css">
    <link rel="stylesheet" href="css/theme.css">
    <link rel="stylesheet" href="images/icons/css/font-awesome.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

    <style>
     /* Table container */
.table-container {
    width: 100%;
    margin-bottom: 20px;
    overflow: hidden;
}

/* Table styling */
#ordersTable {
    width: 100% !important;
    margin-bottom: 0 !important;
    border-collapse: separate !important;
    border-spacing: 0;
}

/* Search box styling */
.search-container {
    margin-bottom: 15px;
}

/* DataTables wrapper */
.dataTables_wrapper {
    position: relative;
    clear: both;
    width: 100% !important;
    min-height: 400px; /* Ensure space for table */
}

/* Scroll body styling */
.dataTables_scrollBody {
    overflow-y: auto !important;
    max-height: 500px !important;
    position: relative;
}

/* ================== */
/* PAGINATION CONTROLS */
/* ================== */

/* Pagination container */
.dataTables_wrapper .dataTables_paginate {
    float: none;
    text-align: center;
    margin: 25px 0 15px 0;
    clear: both;
    position: relative;
    z-index: 100;
    width: 100%;
    user-select: none;
}

/* Pagination buttons */
.dataTables_wrapper .dataTables_paginate .paginate_button {
    display: inline-block !important;
    padding: 8px 14px !important;
    margin: 0 4px !important;
    border: 1px solid #d1d5db !important;
    background: #ffffff !important;
    color: #3b82f6 !important;
    cursor: pointer !important;
    pointer-events: auto !important;
    border-radius: 6px !important;
    transition: all 0.2s ease !important;
    min-width: 36px;
    text-align: center;
    vertical-align: middle;
    line-height: 1.5;
    font-size: 14px;
    font-weight: 500;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

/* Current page button */
.dataTables_wrapper .dataTables_paginate .paginate_button.current,
.dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
    background: #3b82f6 !important;
    color: white !important;
    border-color: #2563eb !important;
    font-weight: 500 !important;
    box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);
}

/* Hover state */
.dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled) {
    background: #f3f4f6 !important;
    text-decoration: none !important;
    color: #2563eb !important;
    border-color: #bfdbfe !important;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Disabled buttons */
.dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
.dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
    opacity: 0.6 !important;
    cursor: not-allowed !important;
    pointer-events: none !important;
    background: #f9fafb !important;
    color: #9ca3af !important;
    border-color: #e5e7eb !important;
    transform: none !important;
    box-shadow: none !important;
}

/* Active state when clicked */
.dataTables_wrapper .dataTables_paginate .paginate_button:active:not(.disabled) {
    transform: translateY(1px) !important;
    box-shadow: inset 0 1px 3px rgba(0,0,0,0.1) !important;
    background: #e0e7ff !important;
}

/* Button focus state */
.dataTables_wrapper .dataTables_paginate .paginate_button:focus {
    outline: none !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3) !important;
    border-color: #3b82f6 !important;
}

/* Ellipsis button */
.dataTables_wrapper .dataTables_paginate .ellipsis {
    padding: 8px 12px;
    margin: 0 4px;
    color: #6b7280;
}

/* Ensure no overlapping elements */
.dataTables_scroll, 
.dataTables_wrapper, 
.dataTables_scrollHead, 
.dataTables_scrollBody {
    position: static !important;
    overflow: visible !important;
}

/* Fix for potential overlay issues */
.dataTables_wrapper:after {
    content: "";
    display: table;
    clear: both;
}
    </style>
</head>
<body>

<?php include('include/header.php'); ?>

<div class="wrapper">
    <div class="container">
        <div class="row">
            <?php include('include/sidebar.php'); ?>                
            <div class="span9">
                <div class="content">

                    <div class="module">
                        <div class="module-head">
                            <h3>Delivered Orders</h3>
                        </div>
                        <div class="module-body">
                            <?php if(isset($_GET['del'])) { ?>
                                <div class="alert alert-error">
                                    <button type="button" class="close" data-dismiss="alert">×</button>
                                    <strong>Oh snap!</strong> <?php echo htmlentities($_SESSION['delmsg']); ?>
                                    <?php echo htmlentities($_SESSION['delmsg'] = ""); ?>
                                </div>
                            <?php } ?>	

                            <!-- Table Container -->
                            <div class="table-container">
                                <table id="ordersTable" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Email / Contact No</th>
                                            <th>Shipping Address</th>
                                            <th>Product</th>
                                            <th>Qty</th>
                                            <th>Amount</th>
                                            <th>Order Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $st = 'Delivered';
// Update the query to:
$query = mysqli_query($con, "SELECT 
    o.id, o.order_number, o.orderDate, o.final_amount,
    u.name AS username,
    u.email AS useremail,
    u.contactno AS usercontact,
    u.shippingAddress, 
    u.shipping_building, 
    u.shipping_house, 
    u.shipping_landmark,
    p.productName,
    oi.quantity,
    p.productPrice,
    oi.shippingCharge
FROM orders o
JOIN users u ON o.userId = u.id
JOIN order_items oi ON o.id = oi.orderId
JOIN products p ON oi.productId = p.id
WHERE o.orderStatus = 'Delivered'
ORDER BY o.orderDate DESC");

                                        $cnt = 1;
                                        while($row = mysqli_fetch_array($query)) { 
                                            $amount = $row['quantity'] * $row['productPrice'] + $row['shippingCharge'];
                                        ?>										
                                        <tr>
                                            <td><?php echo htmlentities($cnt); ?></td>
                                            <td><?php echo htmlentities($row['username']); ?></td>
                                            <td><?php echo htmlentities($row['useremail']); ?> / <?php echo htmlentities($row['usercontact']); ?></td>
                                            <td>
<?php 
    $addressParts = [
        $row['shippingAddress'] ?? '',
        $row['shipping_building'] ?? '',
        $row['shipping_house'] ?? '',
        $row['shipping_landmark'] ?? ''
    ];
    echo htmlentities(implode(', ', array_filter($addressParts))); 
?>
</td>
                                            <td><?php echo htmlentities($row['productName']); ?></td>
                                            <td><?php echo htmlentities($row['quantity']); ?></td>
                                            <td>₹<?php echo number_format($amount, 2); ?></td>
                                            <td><?php echo htmlentities($row['orderDate']); ?></td>
                                            <td>
                                                <a href="updateorder.php?oid=<?php echo htmlentities($row['id']); ?>" title="Update order" target="_blank">
                                                    <i class="icon-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php $cnt++; } ?>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>						

                </div><!--/.content-->
            </div><!--/.span9-->
        </div>
    </div><!--/.container-->
</div><!--/.wrapper-->

<?php include('include/footer.php'); ?>
<script>
$(document).ready(function() {
    // First completely destroy any existing DataTable instance
    if ($.fn.DataTable.isDataTable('#ordersTable')) {
        $('#ordersTable').DataTable().destroy(true);
        $('#ordersTable').removeAttr('style').removeClass('dataTable');
        $('.dataTables_wrapper').remove();
    }
    
    // Initialize DataTables with proper configuration
    var table = $('#ordersTable').DataTable({
        "paging": true,
        "pageLength": 10, // Show 10 records per page
        "lengthMenu": [[5, 10, 15, 20, -1], [5, 10, 15, 20, "All"]],
        "pagingType": "full_numbers",
        "dom": '<"top"lf>rt<"bottom"ip>',
        "language": {
            "paginate": {
                "first": "First",
                "previous": "Prev",
                "next": "Next",
                "last": "Last"
            },
            "lengthMenu": "Show _MENU_ entries"
        },
        "initComplete": function() {
            console.log('DataTables initialized with', this.api().data().length, 'records');
            console.log('Current page info:', this.api().page.info());
            
            // Force enable all buttons
            $('.paginate_button').css('pointer-events', 'auto');
        }
    });

    // Custom search functionality
    $('#searchBox').on('keyup', function() {
        table.search(this.value).draw();
    });
    
    // Manual click handler as backup
    $(document).on('click', '.paginate_button:not(.disabled)', function(e) {
        e.preventDefault();
        var page = $(this).data('dt-idx');
        if (!isNaN(page)) {
            table.page(page-1).draw(false);
        }
    });
});
</script>
</body>
<?php } ?>