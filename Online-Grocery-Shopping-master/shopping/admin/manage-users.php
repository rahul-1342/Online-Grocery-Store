<?php
// 🚫 No output before this!
ob_start();
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('include/config.php');

if (empty($_SESSION['alogin'])) {
    header('location:index.php');
    exit();
}

if (isset($_GET['del'])) {
    mysqli_query($con, "DELETE FROM products WHERE id = '" . $_GET['id'] . "'");
    $_SESSION['delmsg'] = "Product deleted !!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Admin | Manage Users</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    <link href="css/theme.css" rel="stylesheet">
    <link href="images/icons/css/font-awesome.css" rel="stylesheet">
    <link href="scripts/datatables/jquery.dataTables.min.css" rel="stylesheet">

    <style>
        .scrollable-table-container {
            overflow-x: auto;
            width: 100%;
        }
        table.dataTable {
            min-width: 1200px; /* Force table wider */
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
                            <h3>Manage Users</h3>
                        </div>
                        <div class="module-body table">
                            <?php if (!empty($_SESSION['delmsg'])) { ?>
                                <div class="alert alert-error">
                                    <button type="button" class="close" data-dismiss="alert">×</button>
                                    <strong>Oh snap!</strong> <?php echo htmlentities($_SESSION['delmsg']); ?>
                                    <?php $_SESSION['delmsg'] = ""; ?>
                                </div>
                            <?php } ?>

                            <div class="scrollable-table-container mt-3">
                                <table id="userTable" class="datatable-1 table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Contact No</th>
                                            <th>Shipping Address</th>
                                            <th>Reg. Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = mysqli_query($con, "SELECT * FROM users");
                                        $cnt = 1;
                                        while ($row = mysqli_fetch_array($query)) {
                                        ?>
                                        <tr>
                                            <td><?php echo htmlentities($cnt); ?></td>
                                            <td><?php echo htmlentities($row['name']); ?></td>
                                            <td><?php echo htmlentities($row['email']); ?></td>
                                            <td><?php echo htmlentities($row['contactno']); ?></td>
                                            <td><?php echo htmlentities($row['shippingAddress'] . ", " . $row['shipping_building'] . ", " . $row['shipping_house']); ?></td>
                                            <td><?php echo htmlentities($row['regDate']); ?></td>
                                        </tr>
                                        <?php $cnt++; } ?>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>

                </div>
            </div><!-- /.span9 -->
        </div>
    </div><!-- /.container -->
</div><!-- /.wrapper -->

<?php include('include/footer.php'); ?>

<script src="scripts/jquery-1.9.1.min.js"></script>
<script src="scripts/jquery-ui-1.10.1.custom.min.js"></script>
<script src="bootstrap/js/bootstrap.min.js"></script>
<script src="scripts/datatables/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function () {
        $('#userTable').DataTable({
            scrollX: true,
            responsive: false
        });
    });
</script>
</body>
</html>
