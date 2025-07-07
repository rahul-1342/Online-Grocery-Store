<?php
session_start();
include('includes/config.php');

$response = ['success' => false, 'cart_html' => '', 'total_qty' => 0, 'message' => ''];

if(isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Check if product exists
    $query = mysqli_query($con, "SELECT * FROM products WHERE id = $id");
    if(mysqli_num_rows($query) == 0) {
        $response['message'] = 'Product not found';
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }

    $row = mysqli_fetch_assoc($query);

    // Check product availability
    if($row['productAvailability'] != 'In Stock') {
        $response['message'] = 'Product is out of stock';
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }

    // Initialize cart item structure if it doesn't exist
    if(!isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id] = [
            "quantity" => 0,
            "price" => $row['productPrice'],
            "name" => $row['productName'],
            "image" => $row['productImage1']
        ];
    }
    

    // Increment quantity
    $_SESSION['cart'][$id]['quantity']++;

    // Calculate total quantity and price
    $totalQty = 0;
    $totalPrice = 0;
    foreach ($_SESSION['cart'] as $pid => $item)
 {
        // Ensure all required fields exist
        if(!isset($item['quantity'])) $item['quantity'] = 0;
        if(!isset($item['price'])) $item['price'] = 0;
        if(!isset($item['name'])) $item['name'] = 'Unknown Product';
        if(!isset($item['image'])) $item['image'] = 'default.jpg';

        $totalQty += $item['quantity'];
        $totalPrice += $item['price'] * $item['quantity'];
    }
    
    $_SESSION['qnty'] = $totalQty;
    $response['total_qty'] = $totalQty;
    $response['total_price'] = $totalPrice;

    // Generate cart dropdown HTML
    ob_start();
    ?>
    <div class="dropdown dropdown-cart">
        <a href="#" class="dropdown-toggle lnk-cart" data-toggle="dropdown">
            <div class="items-cart-inner">
                <div class="basket">
                    <i class="glyphicon glyphicon-shopping-cart"></i>
                </div>
                <div class="basket-item-count"><span class="count"><?php echo $totalQty; ?></span></div>
                <div class="total-price-basket">
                    <span class="lbl">cart -</span>
                    <span class="total-price">
                        <span class="sign">Rs.</span>
                        <span class="value"><?php echo number_format($totalPrice, 2); ?></span>
                    </span>
                </div>
            </div>
        </a>
        <ul class="dropdown-menu">
            <?php if($totalQty > 0): ?>
                <?php foreach($_SESSION['cart'] as $pid => $item): ?>
    <?php
        $name = $item['name'] ?? 'Unknown Product';
        $image = $item['image'] ?? 'default.jpg';
        $price = $item['price'] ?? 0;
        $quantity = $item['quantity'] ?? 0;
        
    ?>
    <li>
        <div class="cart-item product-summary">
            <div class="row">
                <div class="col-xs-4">
                    <div class="image">
                        <a href="product-details.php?pid=<?php echo $pid; ?>">
                            <?php if(file_exists("admin/productimages/$pid/$image")): ?>
                                <img src="admin/productimages/<?php echo $pid; ?>/<?php echo htmlspecialchars($image); ?>" width="35" height="50" alt="">
                            <?php else: ?>
                                <img src="assets/images/default-product.jpg" width="35" height="50" alt="">
                            <?php endif; ?>
                        </a>
                    </div>
                </div>
                <div class="col-xs-7">
                <h3 class="name"><a href="product-details.php?pid=<?php echo $pid; ?>"><?php echo htmlspecialchars($name); ?></a></h3>
                <div class="price">Rs.<?php echo number_format($price, 2); ?> x <?php echo $quantity; ?></div>
                </div>
                
            </div>
        </div>
    </li>
<?php endforeach; ?>

                <div class="clearfix"></div>
                <hr>
                <div class="clearfix cart-total">
                    <div class="pull-right">
                        <span class="text">Total :</span>
                        <span class="price">Rs.<?php echo number_format($totalPrice, 2); ?></span>
                    </div>
                    <div class="clearfix"></div>
                    <a href="my-cart.php" class="btn btn-upper btn-primary btn-block m-t-20">Go to Cart</a>
                </div>
            <?php else: ?>
                <li><p>Your cart is empty</p></li>
            <?php endif; ?>
        </ul>
    </div>
    <?php
    $response['cart_html'] = ob_get_clean();
    $response['success'] = true;
    $response['message'] = 'Product added to cart';
}

header('Content-Type: application/json');
echo json_encode($response);
?>