<?php
session_start();
include('include/config.php');
if(strlen($_SESSION['alogin']) == 0) {    
    header('location:index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Track Deliveries</title>
    <link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    <link type="text/css" href="css/theme.css" rel="stylesheet">
    <link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
    <style>
        #map { height: 70vh; width: 100%; border: 1px solid #ddd; border-radius: 4px; }
        .delivery-list { max-height: 70vh; overflow-y: auto; }
        .delivery-item { cursor: pointer; transition: all 0.3s; }
        .delivery-item:hover { background-color: #f5f5f5; }
        .active-delivery { background-color: #e7f4ff; }
    </style>
</head>
<body>
<?php include('include/header.php');?>
<?php include('include/sidebar.php');?>

<div class="wrapper">
    <div class="container">
        <div class="row">
            <div class="span9">
                <div class="content">
                    <div class="module">
                        <div class="module-head">
                            <h3>Track Active Deliveries</h3>
                        </div>
                        <div class="module-body">
                            <div class="row-fluid">
                                <div class="span4">
                                    <div class="delivery-list">
                                        <h4>Active Deliveries</h4>
                                        <?php
                                        $query = mysqli_query($con, "SELECT 
                                            o.id, o.order_number, 
                                            u.name as customer_name,
                                            db.name as delivery_boy_name,
                                            o.orderStatus
                                        FROM orders o
                                        JOIN users u ON o.userId = u.id
                                        JOIN delivery_boys db ON o.delivery_boy_id = db.id
                                        WHERE o.orderStatus IN ('Out for Delivery', 'Shipped')
                                        ORDER BY o.orderDate DESC");
                                        
                                        if(mysqli_num_rows($query) > 0) {
                                            while($row = mysqli_fetch_assoc($query)) {
                                                echo '<div class="delivery-item" onclick="showDelivery('.$row['id'].')">
                                                    <h5>Order #'.$row['order_number'].'</h5>
                                                    <p>Customer: '.htmlspecialchars($row['customer_name']).'</p>
                                                    <p>Delivery Boy: '.htmlspecialchars($row['delivery_boy_name']).'</p>
                                                    <span class="label label-info">'.$row['orderStatus'].'</span>
                                                </div>';
                                            }
                                        } else {
                                            echo '<div class="alert alert-info">No active deliveries found</div>';
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="span8">
                                    <div id="map"></div>
                                    <div id="delivery-info" class="mt-3"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('include/footer.php');?>

<script src="scripts/jquery-1.9.1.min.js"></script>
<script src="scripts/jquery-ui-1.10.1.custom.min.js"></script>
<script src="bootstrap/js/bootstrap.min.js"></script>

<script>
    let map, marker;
    let deliveryMarkers = {};
    
    function initMap() {
        // Default center (you can set this to your business location)
        map = new google.maps.Map(document.getElementById("map"), {
            zoom: 12,
            center: { lat: 20.5937, lng: 78.9629 }, // Default to India coordinates
            mapTypeId: 'roadmap'
        });
        
        // Load all active deliveries initially
        loadActiveDeliveries();
        
        // Refresh every 30 seconds
        setInterval(loadActiveDeliveries, 30000);
    }
    
    function loadActiveDeliveries() {
        fetch('get-active-deliveries.php')
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    updateDeliveryMarkers(data.deliveries);
                }
            });
    }
    
    function updateDeliveryMarkers(deliveries) {
        // Clear existing markers except the one being tracked
        for (const id in deliveryMarkers) {
            if (deliveryMarkers[id].tracking !== true) {
                deliveryMarkers[id].setMap(null);
                delete deliveryMarkers[id];
            }
        }
        
        // Add new markers
        deliveries.forEach(delivery => {
            if (!deliveryMarkers[delivery.order_id]) {
                const position = { 
                    lat: parseFloat(delivery.latitude), 
                    lng: parseFloat(delivery.longitude) 
                };
                
                const marker = new google.maps.Marker({
                    position: position,
                    map: map,
                    title: `Order #${delivery.order_number}`,
                    icon: {
                        url: `https://maps.google.com/mapfiles/ms/icons/${delivery.orderStatus === 'Out for Delivery' ? 'blue' : 'green'}-dot.png`
                    }
                });
                
                // Add info window
                const infoWindow = new google.maps.InfoWindow({
                    content: `
                        <h5>Order #${delivery.order_number}</h5>
                        <p>Status: ${delivery.orderStatus}</p>
                        <p>Customer: ${delivery.customer_name}</p>
                        <p>Delivery Boy: ${delivery.delivery_boy_name}</p>
                        <p>Last Update: ${delivery.last_update}</p>
                        <button onclick="trackDelivery(${delivery.order_id})" class="btn btn-small btn-primary">Track</button>
                    `
                });
                
                marker.addListener('click', () => {
                    infoWindow.open(map, marker);
                });
                
                deliveryMarkers[delivery.order_id] = marker;
            }
        });
    }
    
    function showDelivery(orderId) {
        // Highlight in list
        document.querySelectorAll('.delivery-item').forEach(item => {
            item.classList.remove('active-delivery');
            if (item.getAttribute('onclick').includes(orderId)) {
                item.classList.add('active-delivery');
            }
        });
        
        // Center map on this delivery
        if (deliveryMarkers[orderId]) {
            map.setCenter(deliveryMarkers[orderId].getPosition());
            map.setZoom(16);
        }
        
        // Load detailed info
        fetch(`get-delivery-details.php?order_id=${orderId}`)
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    document.getElementById('delivery-info').innerHTML = `
                        <div class="alert alert-info">
                            <h4>Order #${data.order.order_number}</h4>
                            <p><strong>Customer:</strong> ${data.order.customer_name}</p>
                            <p><strong>Delivery Boy:</strong> ${data.order.delivery_boy_name}</p>
                            <p><strong>Status:</strong> <span class="label label-info">${data.order.orderStatus}</span></p>
                            <p><strong>Last Location Update:</strong> ${data.order.last_update}</p>
                            <button onclick="trackDelivery(${orderId})" class="btn btn-primary">Track This Delivery</button>
                        </div>
                    `;
                }
            });
    }
    
    function trackDelivery(orderId) {
        // Clear any existing tracking markers
        for (const id in deliveryMarkers) {
            if (deliveryMarkers[id].tracking) {
                deliveryMarkers[id].setMap(null);
                delete deliveryMarkers[id];
            }
        }
        
        // Create a new tracking marker
        if (deliveryMarkers[orderId]) {
            const position = deliveryMarkers[orderId].getPosition();
            
            marker = new google.maps.Marker({
                position: position,
                map: map,
                title: `Tracking Order #${orderId}`,
                icon: {
                    url: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png'
                },
                tracking: true
            });
            
            // Center and zoom
            map.setCenter(position);
            map.setZoom(16);
            
            // Start real-time tracking
            setInterval(() => {
                fetch(`get-location.php?order_id=${orderId}`)
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            const newPos = new google.maps.LatLng(data.lat, data.lon);
                            marker.setPosition(newPos);
                        }
                    });
            }, 5000);
        }
    }
</script>

<!-- Replace with your Google Maps API key -->
<script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAotC3fg_IyzRYHOklsbPJAs8irInjCD7g&callback=initMap">
</script>
</body>
</html>