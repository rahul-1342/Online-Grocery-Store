<div class="span3">
    <div class="sidebar">
        <ul class="widget widget-menu unstyled">
            <li>
                <a class="collapsed" data-toggle="collapse" href="#togglePages">
                    <i class="menu-icon icon-cog"></i>
                    <i class="icon-chevron-down pull-right"></i><i class="icon-chevron-up pull-right"></i>
                    Order Management
                </a>
                <ul id="togglePages" class="collapse unstyled">
                    <li>
                        <a href="todays-orders.php">
                            <i class="icon-tasks"></i>
                            Today's Orders
                            <?php
                            $f1 = "00:00:00";
                            $from = date('Y-m-d') . " " . $f1;
                            $t1 = "23:59:59";
                            $to = date('Y-m-d') . " " . $t1;
                            $result = mysqli_query($con, "SELECT * FROM orders WHERE orderDate BETWEEN '$from' AND '$to'");
                            if ($result) {
                                $num_rows1 = mysqli_num_rows($result);
                                if ($num_rows1 > 0) {
                                    echo '<b class="label orange pull-right">' . htmlentities($num_rows1) . '</b>';
                                }
                            }
                            ?>
                        </a>
                    </li>
                    <li>
                        <a href="pending-orders.php">
                            <i class="icon-tasks"></i>
                            Pending Orders
                            <?php
                            $status = 'Delivered';
                            $ret = mysqli_query($con, "SELECT * FROM orders WHERE orderStatus != '$status' OR orderStatus IS NULL");
                            if ($ret) {
                                $num = mysqli_num_rows($ret);
                                if ($num > 0) {
                                    echo '<b class="label orange pull-right">' . htmlentities($num) . '</b>';
                                }
                            }
                            ?>
                        </a>
                    </li>
                    <li>
                        <a href="delivered-orders.php">
                            <i class="icon-inbox"></i>
                            Delivered Orders
                            <?php
                            $status = 'Delivered';
                            $rt = mysqli_query($con, "SELECT * FROM orders WHERE orderStatus = '$status'");
                            if ($rt) {
                                $num1 = mysqli_num_rows($rt);
                                if ($num1 > 0) {
                                    echo '<b class="label green pull-right">' . htmlentities($num1) . '</b>';
                                }
                            }
                            ?>
                        </a>
                    </li>
                    <!-- Add this new option for Track Delivery -->
                    <li>
                        <a href="track-deliveries.php">
                            <i class="icon-map-marker"></i>
                            Track Deliveries
                            <?php
                            $active_deliveries = mysqli_query($con, "SELECT COUNT(*) as total FROM orders 
                                WHERE orderStatus IN ('Out for Delivery', 'Shipped')");
                            if ($active_deliveries) {
                                $delivery_data = mysqli_fetch_assoc($active_deliveries);
                                if ($delivery_data['total'] > 0) {
                                    echo '<b class="label blue pull-right">' . htmlentities($delivery_data['total']) . '</b>';
                                }
                            }
                            ?>
                        </a>
                    </li>
                </ul>
            </li>

            <li>
                <a href="manage-users.php">
                    <i class="menu-icon icon-group"></i>
                    Manage users
                </a>
            </li>
        </ul>

        <ul class="widget widget-menu unstyled">
            <li><a href="category.php"><i class="menu-icon icon-tasks"></i> Create Category </a></li>
            <li><a href="subcategory.php"><i class="menu-icon icon-tasks"></i>Sub Category </a></li>
            <li><a href="insert-product.php"><i class="menu-icon icon-paste"></i>Insert Product </a></li>
            <li><a href="manage-products.php"><i class="menu-icon icon-table"></i>Manage Products </a></li>
        </ul>

        <ul class="widget widget-menu unstyled">
            <li>
                <a href="manage-reviews.php">
                    <i class="menu-icon icon-comments"></i>
                    Manage Reviews
                    <?php
                    $review_count = mysqli_query($con, "SELECT COUNT(*) as total FROM user_product_reviews");
                    if ($review_count) {
                        $review_data = mysqli_fetch_assoc($review_count);
                        if ($review_data['total'] > 0) {
                            echo '<b class="label blue pull-right">' . htmlentities($review_data['total']) . '</b>';
                        }
                    }
                    ?>
                </a>
            </li>

            <li>
                <a href="contact-queries.php">
                    <i class="menu-icon icon-envelope"></i>
                    Contact Queries
                    <?php
                    $query = "SELECT COUNT(*) as total FROM contact_queries WHERE status='unread'";
                    $result = mysqli_query($con, $query);
                    if ($result) {
                        $row = mysqli_fetch_assoc($result);
                        if ($row['total'] > 0) {
                            echo '<b class="label red pull-right">' . htmlentities($row['total']) . '</b>';
                        }
                    }
                    ?>
                </a>
            </li>

            <li><a href="user-logs.php"><i class="menu-icon icon-tasks"></i>User Login Log </a></li>
            <li>
                <a href="logout.php">
                    <i class="menu-icon icon-signout"></i>
                    Logout
                </a>
            </li>
        </ul>
    </div><!--/.sidebar-->
</div><!--/.span3-->
