<?php 
// Initialize cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// Handle quantity updates
if (isset($_GET['action']) && isset($_POST['quantity'])) {
    if (!empty($_SESSION['cart'])) {
        foreach ($_POST['quantity'] as $key => $val) {
            $val = intval($val); // Ensure quantity is an integer
            if ($val <= 0) {
                unset($_SESSION['cart'][$key]);
            } else {
                $_SESSION['cart'][$key]['quantity'] = $val;
            }
        }

        // Recalculate cart quantity total after update
        $totalqunty = 0;
        foreach ($_SESSION['cart'] as $item) {
            if (isset($item['quantity'])) {
                $totalqunty += $item['quantity'];
            }
        }
        $_SESSION['qnty'] = $totalqunty;
    }
}
?>

<div class="main-header">
    <div class="container">
        <div class="row" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; padding: 10px 0;">
            <!-- Logo -->
            <div class="logo-holder" style="flex: 1;">
                <div class="logo">
                    <a href="index.php">
                        <h3 style="margin: 0;">ARIHANT TRADERS</h3>
                    </a>
                </div>        
            </div>

            <!-- Search Box -->
            <div class="top-search-holder" style="flex: 2;">
                <div class="search-area" style="width: 100%;">
                    <form name="search" method="post" action="search-result.php">
                        <div class="control-group" style="display: flex; gap: 5px;">
                            <input class="search-field form-control" placeholder="Search here..." name="product" required="required" style="flex: 1;" />
                            <button class="search-button btn btn-primary" type="submit" name="search">
                                <i class="glyphicon glyphicon-search"></i>
                            </button>    
                        </div>
                    </form>
                </div>
            </div>

            <!-- Cart Dropdown -->
            <div class="top-cart-row" style="flex: 1;">
                <?php
                $totalqunty = 0;
                if (!empty($_SESSION['cart'])) {
                    foreach ($_SESSION['cart'] as $item) {
                        if (isset($item['quantity'])) {
                            $totalqunty += $item['quantity'];
                        }
                    }
                    $_SESSION['qnty'] = $totalqunty;
                ?>
                <div class="dropdown dropdown-cart">
                    <a href="#" class="dropdown-toggle lnk-cart" data-toggle="dropdown">
                        <div class="items-cart-inner">
                            <div class="basket">
                                <i class="glyphicon glyphicon-shopping-cart"></i>
                            </div>
                            <div class="basket-item-count">
                                <span class="count"><?php echo isset($_SESSION['qnty']) ? $_SESSION['qnty'] : 0; ?></span>
                            </div>
                        </div>
                    </a>
                    <ul class="dropdown-menu">
                        <?php
                        if (!empty($_SESSION['cart'])) {
                            $sql = "SELECT * FROM products WHERE id IN(";
                            $ids = array();
                            foreach ($_SESSION['cart'] as $id => $value) {
                                $ids[] = intval($id);
                            }
                            $sql .= implode(",", $ids) . ") ORDER BY id ASC";
                            $query = mysqli_query($con, $sql);
                            $totalprice = 0;

                            if ($query && mysqli_num_rows($query) > 0) {
                                while ($row = mysqli_fetch_array($query)) {
                                    if (isset($_SESSION['cart'][$row['id']]['quantity'])) {
                                        $quantity = $_SESSION['cart'][$row['id']]['quantity'];
                                        $subtotal = $quantity * ($row['productPrice'] + $row['shippingCharge']);
                                        $totalprice += $subtotal;
                        ?>
                        <li>
                            <div class="cart-item product-summary">
                                <div class="row">
                                    <div class="col-xs-4">
                                        <div class="image">
                                            <a href="product-details.php?pid=<?php echo $row['id']; ?>">
                                                <img src="admin/productimages/<?php echo $row['id']; ?>/<?php echo $row['productImage1']; ?>" width="35" height="50" alt="">
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-xs-7">
                                        <h3 class="name"><a href="product-details.php?pid=<?php echo $row['id']; ?>"><?php echo $row['productName']; ?></a></h3>
                                        <div class="price">Rs.<?php echo ($row['productPrice'] + $row['shippingCharge']); ?>*<?php echo $quantity; ?></div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <?php 
                                    }
                                }
                            }
                        ?>
                        <div class="clearfix"></div>
                        <hr>
                        <div class="clearfix cart-total">
                            <div class="pull-right">
                                <span class="text">Total :</span><span class='price'>Rs.<?php echo number_format($totalprice, 2); ?></span>
                            </div>
                            <div class="clearfix"></div>
                            <a href="my-cart.php" class="btn btn-upper btn-primary btn-block m-t-20">My Cart</a>    
                        </div>
                        <?php } ?>
                    </ul>
                </div>
                <?php } else { ?>
                <div class="dropdown dropdown-cart">
                    <a href="#" class="dropdown-toggle lnk-cart" data-toggle="dropdown">
                        <div class="items-cart-inner">
                            <div class="basket">
                                <i class="glyphicon glyphicon-shopping-cart"></i>
                            </div>
                            <div class="basket-item-count"><span class="count">0</span></div>
                        </div>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <div class="cart-item product-summary">
                                <div class="row">
                                    <div class="col-xs-12">
                                        Your Shopping Cart is Empty.
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="clearfix cart-total">
                                <div class="clearfix"></div>
                                <a href="index.php" class="btn btn-upper btn-primary btn-block m-t-20">Continue Shopping</a>    
                            </div>
                        </li>
                    </ul>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
