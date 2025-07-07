<?php
session_start();
include('include/config.php');
if (!isset($_SESSION['alogin']) || strlen((string)$_SESSION['alogin']) === 0) {
    header('Location: index.php');
    exit();
}

date_default_timezone_set('Asia/Kolkata');
$currentTime = date('d-m-Y h:i:s A', time());

// Delete review if requested
if(isset($_GET['del'])) {
    $id = intval($_GET['del']);
    mysqli_query($con, "DELETE FROM user_product_reviews WHERE id='$id'");
    $_SESSION['delmsg'] = "Review deleted successfully";
    header('Location: manage-reviews.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin| Manage Product Reviews</title>
    <link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    <link type="text/css" href="css/theme.css" rel="stylesheet">
    <link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
    <link type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600' rel='stylesheet'>
    <style>
        /* Reviews Management */
        #reviews-table td {
            vertical-align: middle;
        }
        .rating-stars {
            color: #FFD700;
            font-size: 16px;
        }
        .review-text {
            max-width: 300px;
            word-wrap: break-word;
        }
    </style>
</head>
<body>
<?php include('include/header.php');?>

    <div class="wrapper">
        <div class="container">
            <div class="row">
<?php include('include/sidebar.php');?>                
            <div class="span9">
                    <div class="content">

    <div class="module">
        <div class="module-head">
            <h3>Manage Product Reviews</h3>
        </div>
        <div class="module-body table">
            <?php if(isset($_SESSION['delmsg'])) { ?>
                <div class="alert alert-<?php echo (strpos($_SESSION['delmsg'], 'deleted') !== false) ? 'error' : 'success'; ?>">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong><?php echo (strpos($_SESSION['delmsg'], 'deleted') !== false) ? 'Success!' : 'Oh snap!'; ?></strong>  
                    <?php echo htmlentities($_SESSION['delmsg']); ?>
                    <?php unset($_SESSION['delmsg']); ?>
                </div>
            <?php } ?>

            <br />
            
            <table cellpadding="0" cellspacing="0" border="0" class="datatable-1 table table-bordered table-striped display" width="100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>Review</th>
                        <th>Ratings</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $query = "SELECT r.*, p.productName 
                             FROM user_product_reviews r
                             JOIN products p ON r.productId = p.id
                             ORDER BY r.reviewDate DESC";
                    $result = mysqli_query($con, $query);
                    $cnt = 1;
                    while($row = mysqli_fetch_array($result)) {
                    ?>
                    <tr>
                        <td><?php echo htmlentities($cnt); ?></td>
                        <td>
                            <a href="product-details.php?pid=<?php echo $row['productId']; ?>">
                                <?php echo htmlentities($row['productName']); ?>
                            </a>
                        </td>
                        <td><?php echo htmlentities($row['user_name']); ?></td>
                        <td><?php echo htmlentities($row['user_email']); ?></td>
                        <td class="review-text"><?php echo nl2br(htmlentities($row['review'])); ?></td>
                        <td class="rating-stars">
                            Quality: <?php echo str_repeat('★', $row['rating_quality']); ?><br>
                            Price: <?php echo str_repeat('★', $row['rating_price']); ?><br>
                            Value: <?php echo str_repeat('★', $row['rating_value']); ?>
                        </td>
                        <td><?php echo date("d M Y", strtotime($row['reviewDate'])); ?></td>
                        <td>
                            <a href="manage-reviews.php?del=<?php echo $row['id']; ?>" 
                               class="btn btn-danger btn-mini" 
                               onclick="return confirm('Are you sure you want to delete this review?')">
                                <i class="icon-trash icon-white"></i> Delete
                            </a>
                        </td>
                    </tr>
                    <?php 
                    $cnt++;
                    } 
                    ?>
                </tbody>
            </table>
        </div>
    </div>                        

                    </div><!--/.content-->
                </div><!--/.span9-->
            </div>
        </div><!--/.container-->
    </div><!--/.wrapper-->

<?php include('include/footer.php');?>

    <script src="scripts/jquery-1.9.1.min.js" type="text/javascript"></script>
    <script src="scripts/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
    <script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="scripts/flot/jquery.flot.js" type="text/javascript"></script>
    <script src="scripts/datatables/jquery.dataTables.js"></script>
    <script>
        $(document).ready(function() {
            $('.datatable-1').dataTable({
                "pageLength": 25,
                "order": [[6, "desc"]], // Sort by date column (6th column) descending
                "aoColumnDefs": [
                    { "bSortable": false, "aTargets": [ 4, 5, 7 ] } // Disable sorting for Review, Ratings, and Action columns
                ]
            });
            $('.dataTables_paginate').addClass("btn-group datatable-pagination");
            $('.dataTables_paginate > a').wrapInner('<span />');
            $('.dataTables_paginate > a:first-child').append('<i class="icon-chevron-left shaded"></i>');
            $('.dataTables_paginate > a:last-child').append('<i class="icon-chevron-right shaded"></i>');
        } );
    </script>
</body>
</html>
