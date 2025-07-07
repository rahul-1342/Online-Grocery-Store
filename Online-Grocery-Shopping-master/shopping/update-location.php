<?php
session_start();
include("includes/config.php");

// Validate session
if (!isset($_SESSION['delivery_boy_id'])) {
    header('Content-Type: application/json');
    http_response_code(401);
    die(json_encode(['success' => false, 'error' => 'Unauthorized']));
}

// Validate inputs
$required = ['lat', 'lon', 'order_id'];
foreach ($required as $field) {
    if (!isset($_POST[$field])) {
        header('Content-Type: application/json');
        http_response_code(400);
        die(json_encode(['success' => false, 'error' => "Missing $field"]));
    }
}

$lat = floatval($_POST['lat']);
$lon = floatval($_POST['lon']);
$accuracy = isset($_POST['accuracy']) ? floatval($_POST['accuracy']) : null;
$order_id = intval($_POST['order_id']);
$delivery_boy_id = intval($_SESSION['delivery_boy_id']);

// Validate coordinate ranges
if ($lat < -90 || $lat > 90 || $lon < -180 || $lon > 180) {
    header('Content-Type: application/json');
    http_response_code(400);
    die(json_encode(['success' => false, 'error' => 'Invalid coordinates']));
}

// Reject 0,0 coordinates (likely error)
if ($lat == 0 && $lon == 0) {
    header('Content-Type: application/json');
    http_response_code(400);
    die(json_encode(['success' => false, 'error' => 'Invalid zero coordinates']));
}

// Reject low accuracy locations (> 1km)
if ($accuracy && $accuracy > 1000) {
    header('Content-Type: application/json');
    http_response_code(400);
    die(json_encode(['success' => false, 'error' => 'Location accuracy too low']));
}

// Prepare statement with error handling
$stmt = $con->prepare("
    INSERT INTO delivery_locations 
    (order_id, delivery_boy_id, latitude, longitude, accuracy) 
    VALUES (?, ?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE 
        latitude = VALUES(latitude),
        longitude = VALUES(longitude),
        accuracy = VALUES(accuracy),
        updated_at = CURRENT_TIMESTAMP
");

if (!$stmt) {
    header('Content-Type: application/json');
    http_response_code(500);
    die(json_encode(['success' => false, 'error' => $con->error]));
}

$stmt->bind_param("iiddd", $order_id, $delivery_boy_id, $lat, $lon, $accuracy);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}

$stmt->close();
?>