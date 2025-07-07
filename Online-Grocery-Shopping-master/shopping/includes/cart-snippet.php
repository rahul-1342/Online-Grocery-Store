<?php
if (!empty($_SESSION['cart'])) {
    $totalqunty = 0;
    foreach ($_SESSION['cart'] as $item) {
        $totalqunty += $item['quantity'];
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
                <span class="count"><?php echo $totalqunty; ?></span>
            </div>
        </div>
    </a>
    <ul class="dropdown-menu">
        <?php
        $sql = "SELECT * FROM products WHERE id IN(" . implode(",", array_keys($_SESSION['cart'])) . ")";
        $query = mysqli_query($con, $sql);
        $totalprice = 0;

        while ($row = mysqli_fetch_array($query)) {
            $quantity = $_SESSION['cart'][$row['id']]['quantity'];
            $subtotal = $quantity * $row['productPrice'] + $row['shippingCharge'];
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
                        <h3 class="name">
                            <a href="product-details.php?pid=<?php echo $row['id']; ?>"><?php echo $row['productName']; ?></a>
                        </h3>
                        <div class="price">Rs.<?php echo $row['productPrice'] + $row['shippingCharge']; ?> * <?php echo $quantity; ?></div>
                    </div>
                </div>
            </div>
        </li>
        <?php } ?>
        <div class="clearfix"></div>
        <hr>
        <div class="clearfix cart-total">
            <div class="pull-right">
                <span class="text">Total :</span>
                <span class="price">Rs.<?php echo number_format($totalprice); ?>.00</span>
            </div>
            <div class="clearfix"></div>
            <a href="my-cart.php" class="btn btn-upper btn-primary btn-block m-t-20">My Cart</a>
        </div>
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
