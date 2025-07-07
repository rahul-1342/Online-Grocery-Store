<?php
session_start();
include("includes/config.php");
require 'vendor/PHPMailer/PHPMailer.php';
require 'vendor/PHPMailer/SMTP.php';
require 'vendor/PHPMailer/Exception.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['delivery_boy_id'])) {
    header("Location: delivery-login.php");
    exit;
}

$order_id = intval($_GET['order_id']);
$delivery_boy_id = $_SESSION['delivery_boy_id'];

// Verify the order is assigned to this delivery boy and get customer details
$order_check = mysqli_query($con, "SELECT o.id, o.userId, u.email, u.name, 
                                  u.shipping_lat, u.shipping_lng,
                                  CONCAT(u.shippingAddress, ', ', u.shipping_building, ', ', 
                                         u.shipping_house, ' - ', u.shipping_landmark) AS fullAddress
                           FROM orders o
                           JOIN users u ON o.userId = u.id
                           WHERE o.id = $order_id AND o.delivery_boy_id = $delivery_boy_id");
    
if (mysqli_num_rows($order_check) == 0) {
    header("Location: delivery-dashboard.php");
    exit;
}

$order_data = mysqli_fetch_assoc($order_check);
$customer_id = $order_data['userId'];
$customer_email = $order_data['email'];
$customer_name = $order_data['name'];
$customer_lat = $order_data['shipping_lat'];
$customer_lng = $order_data['shipping_lng'];
$customer_address = $order_data['fullAddress'];

// Generate OTP if not already generated
if (!isset($_SESSION['delivery_otp'])) {
    $otp = rand(100000, 999999);
    $_SESSION['delivery_otp'] = $otp;
    $_SESSION['delivery_otp_order'] = $order_id;
    $_SESSION['delivery_otp_time'] = time();
    
    // Send OTP via email
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'jadhavmahesh3329@gmail.com';
        $mail->Password = 'gfir mzmm tqzl trlr';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('jadhavmahesh3329@gmail.com', 'Grocery Delivery');
        $mail->addAddress($customer_email, $customer_name);

        $mail->isHTML(true);
        $mail->Subject = 'Your Delivery OTP';
        $mail->Body = "Hello $customer_name,<br><br>Your OTP for order #$order_id is: <b>$otp</b><br>Valid for 15 minutes.<br><br>Thank you.";
        $mail->AltBody = "Hello $customer_name,\n\nYour OTP for order #$order_id is: $otp\nValid for 15 minutes.\n\nThank you.";

        $mail->send();
        $otp_message = "OTP for order #$order_id has been sent to customer's email.";
    } catch (Exception $e) {
        $otp_message = "OTP for order #$order_id is: $otp (Failed to send email: {$mail->ErrorInfo})";
    }
}

// Update order status to "Out for Delivery"
mysqli_query($con, "UPDATE orders SET orderStatus = 'Out for Delivery' 
    WHERE id = $order_id AND delivery_boy_id = $delivery_boy_id");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Delivery Tracking - Order #<?= $order_id ?></title>
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
    
    .tracking-container {
      background: white;
      border-radius: 16px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      margin: 30px auto;
      padding: 30px;
      position: relative;
      overflow: hidden;
      max-width: 1200px;
    }
    
    .tracking-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 30px;
      padding-bottom: 20px;
      border-bottom: 1px solid #eee;
    }
    
    .tracking-header h2 {
      color: var(--primary-color);
      font-weight: 700;
      margin-bottom: 0;
    }
    
    #map { 
      height: 60vh; 
      width: 100%; 
      border-radius: 8px;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
      margin-bottom: 20px;
    }
    
    .customer-info-card {
      background: white;
      border-radius: 8px;
      padding: 20px;
      margin-bottom: 20px;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
    }
    
    .customer-info-card h5 {
      color: var(--primary-color);
      margin-bottom: 15px;
    }
    
    .btn-delivered {
      background: var(--accent-color);
      border: none;
      border-radius: 8px;
      padding: 10px 20px;
      color: white;
      font-weight: 600;
      transition: all 0.3s;
    }
    
    .btn-delivered:hover {
      background: #c0392b;
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    .btn-secondary {
      background: var(--primary-color);
      border: none;
      border-radius: 8px;
      padding: 10px 20px;
      color: white;
      font-weight: 600;
      transition: all 0.3s;
    }
    
    .btn-secondary:hover {
      background: #1a252f;
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    .btn-primary {
      background: var(--secondary-color);
      border: none;
      border-radius: 8px;
      padding: 10px 20px;
      color: white;
      font-weight: 600;
      transition: all 0.3s;
    }
    
    .btn-primary:hover {
      background: #2980b9;
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    .alert {
      border-radius: 8px;
    }
    
    .spinner-border { 
      vertical-align: middle; 
    }
    
    #mobile-tips {
      border-radius: 8px;
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
    
    .action-buttons {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
    }
    
    .otp-modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.7);
      z-index: 1000;
      align-items: center;
      justify-content: center;
    }
    
    .otp-container {
      background: white;
      padding: 30px;
      border-radius: 10px;
      max-width: 500px;
      width: 90%;
      box-shadow: 0 5px 20px rgba(0,0,0,0.2);
    }
    
    .otp-input {
      letter-spacing: 10px;
      font-size: 24px;
      text-align: center;
      padding: 10px;
      width: 100%;
      margin: 15px 0;
    }
    
    .otp-timer {
      color: #e74c3c;
      font-weight: bold;
    }
    
    @media (max-width: 768px) {
      .tracking-container {
        margin: 15px;
        padding: 20px;
      }
      
      .tracking-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
      }
      
      #map {
        height: 50vh;
      }
      
      .action-buttons {
        flex-direction: column;
      }
      
      .action-buttons .btn {
        width: 100%;
      }
      
      .delivery-decoration {
        display: none;
      }
    }
  </style>
</head>
<body>
  <div class="tracking-container">
    <!-- Decorative elements -->
    <i class="fas fa-motorcycle delivery-decoration top-left"></i>
    <i class="fas fa-map-marked-alt delivery-decoration bottom-right"></i>
    
    <div class="tracking-header">
      <h2>
        <i class="fas fa-truck"></i> Delivery Tracking - Order #<?= $order_id ?>
      </h2>
      <a href="delivery-dashboard.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Dashboard
      </a>
    </div>

    <div id="map"></div>
    
    <div class="customer-info-card">
      <h5><i class="fas fa-user"></i> Customer Information</h5>
      <p><strong>Name:</strong> <?= htmlspecialchars($customer_name) ?></p>
      <p><strong>Address:</strong> <?= htmlspecialchars($customer_address) ?></p>
      <?php if(!empty($customer_lat) && !empty($customer_lng)): ?>
        <button onclick="showDirections()" class="btn btn-primary">
          <i class="fas fa-route"></i> Show Directions
        </button>
      <?php else: ?>
        <p class="text-muted">No location coordinates available for directions</p>
      <?php endif; ?>
    </div>
    
    <div class="status-card">
      <div id="status" class="alert alert-info">
        <div class="spinner-border spinner-border-sm" role="status"></div>
        Getting initial location...
      </div>
      <div id="debug" class="mt-2 small text-muted"></div>
    </div>
    
    <div id="troubleshooting" class="mb-3"></div>
    
    <div id="mobile-tips" class="alert alert-warning" style="display:none;">
      <h5><i class="fas fa-mobile-alt"></i> Mobile Device Tips:</h5>
      <ul class="mb-0">
        <li>Ensure GPS/Location is enabled in your device settings</li>
        <li>Go outdoors for better GPS signal</li>
        <li>Keep your device uncovered for better signal</li>
      </ul>
    </div>

    <div class="action-buttons">
      <button onclick="verifyDelivery()" class="btn btn-delivered">
        <i class="fas fa-check-circle"></i> Mark as Delivered
      </button>
      <button onclick="stopTracking()" class="btn btn-danger">
        <i class="fas fa-stop"></i> Stop Tracking
      </button>
      <button onclick="retryLocation()" class="btn btn-primary">
        <i class="fas fa-sync-alt"></i> Retry Location
      </button>
    </div>
  </div>

  <!-- OTP Verification Modal -->
  <div id="otpModal" class="otp-modal">
    <div class="otp-container">
      <h3><i class="fas fa-shield-alt"></i> OTP Verification</h3>
      <p>Please enter the 6-digit OTP received by the customer to mark this order as delivered.</p>
      
      <div class="alert alert-info">
        <?php if(isset($otp_message)) echo $otp_message; ?>
      </div>
      
      <div class="mb-3">
        <input type="text" id="otpInput" class="form-control otp-input" 
               maxlength="6" placeholder="Enter OTP" oninput="validateOTPInput(this)">
        <div id="otpError" class="text-danger mt-2"></div>
      </div>
      
      <div class="d-flex justify-content-between align-items-center">
        <div id="otpTimer" class="otp-timer"></div>
        <button onclick="resendOTP()" class="btn btn-link">Resend OTP</button>
      </div>
      
      <div class="d-flex gap-2 mt-3">
        <button onclick="submitOTP()" class="btn btn-success flex-grow-1">
          <i class="fas fa-check"></i> Verify & Complete Delivery
        </button>
        <button onclick="closeOTPModal()" class="btn btn-secondary">
          <i class="fas fa-times"></i> Cancel
        </button>
      </div>
    </div>
  </div>

  <script>
    let map, marker, accuracyCircle, directionsService, directionsRenderer;
    let watchId = null;
    const orderId = <?= $order_id ?>;
    const deliveryBoyId = <?= $delivery_boy_id ?>;
    const customerLat = <?= !empty($customer_lat) ? $customer_lat : 0 ?>;
    const customerLng = <?= !empty($customer_lng) ? $customer_lng : 0 ?>;
    const customerAddress = "<?= addslashes($customer_address) ?>";

    // Initialize map with last known good position
    function initMap() {
        const defaultLocation = { lat: 20.5937, lng: 78.9629 }; // Default to India coordinates

        try {
            map = new google.maps.Map(document.getElementById("map"), {
                zoom: 16,
                center: defaultLocation,
                mapTypeId: 'roadmap'
            });

            // Initialize directions service
            directionsService = new google.maps.DirectionsService();
            directionsRenderer = new google.maps.DirectionsRenderer();
            directionsRenderer.setMap(map);

            marker = new google.maps.Marker({
                position: defaultLocation,
                map: map,
                title: "Your Location",
                icon: {
                    url: "https://maps.google.com/mapfiles/ms/icons/blue-dot.png"
                }
            });

            accuracyCircle = new google.maps.Circle({
                strokeColor: "#4285F4",
                strokeOpacity: 0.8,
                strokeWeight: 1,
                fillColor: "#4285F4",
                fillOpacity: 0.2,
                map: map,
                radius: 50, // Default accuracy radius in meters
                center: defaultLocation
            });

            // Add customer marker if coordinates exist
            if (customerLat && customerLng) {
                new google.maps.Marker({
                    position: { lat: customerLat, lng: customerLng },
                    map: map,
                    title: "Customer Location",
                    icon: {
                        url: "https://maps.google.com/mapfiles/ms/icons/red-dot.png"
                    }
                });
            }

            // Detect mobile devices
            if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                document.getElementById('mobile-tips').style.display = 'block';
            }

            startTracking();
        } catch (error) {
            console.error("Map initialization failed:", error);
            document.getElementById('status').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> Failed to load map: ${error.message}
                </div>`;
        }
    }

    // Show directions from current location to customer
    function showDirections() {
        if (!customerLat || !customerLng) {
            alert("Customer location coordinates are not available");
            return;
        }

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                position => {
                    const userLat = position.coords.latitude;
                    const userLng = position.coords.longitude;

                    const start = new google.maps.LatLng(userLat, userLng);
                    const end = new google.maps.LatLng(customerLat, customerLng);

                    const request = {
                        origin: start,
                        destination: end,
                        travelMode: 'DRIVING'
                    };

                    directionsService.route(request, function (result, status) {
                        if (status == 'OK') {
                            directionsRenderer.setDirections(result);

                            // Calculate distance
                            const route = result.routes[0];
                            let distance = 0;
                            for (let i = 0; i < route.legs.length; i++) {
                                distance += route.legs[i].distance.value;
                            }
                            distance = distance / 1000; // Convert to km

                            document.getElementById('status').innerHTML = `
                                <div class="alert alert-success">
                                    <i class="fas fa-route"></i> Directions calculated (${distance.toFixed(1)} km)
                                </div>`;
                        } else {
                            document.getElementById('status').innerHTML = `
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i> Could not calculate directions
                                </div>`;
                        }
                    });
                },
                error => {
                    alert("Could not get your current location for directions");
                }
            );
        } else {
            alert("Geolocation is not supported by your browser");
        }
    }

    // Start tracking with high accuracy
    function startTracking() {
        const options = {
            enableHighAccuracy: true, // Request GPS if available
            timeout: 30000, // 30 second timeout
            maximumAge: 0 // Don't use cached positions
        };

        if (navigator.geolocation) {
            // First try to get current position
            navigator.geolocation.getCurrentPosition(
                position => {
                    updatePosition(position);
                    // Then start watching for updates
                    watchId = navigator.geolocation.watchPosition(
                        updatePosition,
                        handleGeolocationError,
                        options
                    );
                },
                handleGeolocationError,
                options
            );
        } else {
            showError("Geolocation is not supported by this browser.");
        }
    }

    // Process new position data
    function updatePosition(position, isInitial = false) {
        const lat = position.coords.latitude;
        const lon = position.coords.longitude;
        const accuracy = position.coords.accuracy;
        const timestamp = new Date(position.timestamp).toLocaleTimeString();

        // Debug output
        document.getElementById('debug').innerHTML = `
            <strong>Current Position:</strong><br>
            Coordinates: ${lat.toFixed(6)}, ${lon.toFixed(6)}<br>
            Accuracy: ${Math.round(accuracy)} meters<br>
            Source: ${position.coords.altitude ? 'GPS' : 'Network/WiFi'}<br>
            Time: ${timestamp}
        `;

        // Validate coordinates (reject 0,0 or obviously wrong locations)
        if (lat === 0 && lon === 0) {
            showError("Invalid location received (0,0)");
            return;
        }

        // Reject low accuracy locations (> 1km)
        if (accuracy > 1000) {
            showError(`Location accuracy too low (${Math.round(accuracy)} meters). Please move to better signal area.`);
            return;
        }

        // Update map display
        const latLng = new google.maps.LatLng(lat, lon);
        marker.setPosition(latLng);
        map.setCenter(latLng);
        accuracyCircle.setCenter(latLng);
        accuracyCircle.setRadius(accuracy);

        // Update server
        updateServerLocation(lat, lon, accuracy, timestamp);
    }

    // Send location to server
    function updateServerLocation(lat, lon, accuracy, timestamp) {
        const formData = new FormData();
        formData.append('lat', lat);
        formData.append('lon', lon);
        formData.append('accuracy', accuracy);
        formData.append('order_id', orderId);

        fetch('update-location.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            if (data.success) {
                document.getElementById('status').innerHTML = `
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> Location updated at ${timestamp}<br>
                        Accuracy: ${Math.round(accuracy)} meters
                    </div>`;
            } else {
                throw new Error(data.error || 'Failed to update location');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError(`<i class="fas fa-exclamation-circle"></i> Error updating location: ${error.message}`);
        });
    }

    // Handle geolocation errors
    function handleGeolocationError(error) {
        let message = "<i class='fas fa-exclamation-triangle'></i> Error getting location: ";
        switch (error.code) {
            case error.PERMISSION_DENIED:
                message = "Location access was denied. Please enable permissions in your browser settings.";
                break;
            case error.POSITION_UNAVAILABLE:
                message = "Location information is unavailable. Please check your network/GPS connection.";
                break;
            case error.TIMEOUT:
                message = "Location request timed out. Please ensure you have GPS signal or are outdoors.";
                break;
            case error.UNKNOWN_ERROR:
                message = "An unknown error occurred while getting location.";
                break;
        }
        showError(message);

        // Show troubleshooting tips
        document.getElementById('troubleshooting').innerHTML = `
            <div class="alert alert-warning">
                <h5><i class="fas fa-question-circle"></i> Troubleshooting Tips:</h5>
                <ul class="mb-0">
                    <li>Ensure location/GPS is enabled on your device</li>
                    <li>Move to an area with better GPS signal (outdoors)</li>
                    <li>Check browser permissions for location access</li>
                    <li>Restart your device if problems persist</li>
                </ul>
            </div>`;
    }

    function showError(message) {
        document.getElementById('status').innerHTML = `
            <div class="alert alert-danger">
                ${message}
            </div>`;
    }

    function retryLocation() {
        document.getElementById('status').innerHTML = `
            <div class="alert alert-info">
                <div class="spinner-border spinner-border-sm" role="status"></div>
                Attempting to get location...
            </div>`;
        document.getElementById('troubleshooting').innerHTML = '';
        startTracking();
    }

    // Stop tracking
    function stopTracking() {
        if (watchId) {
            navigator.geolocation.clearWatch(watchId);
            watchId = null;
        }
        document.getElementById('status').innerHTML = `
            <div class="alert alert-warning">
                <i class="fas fa-pause-circle"></i> Tracking stopped
            </div>`;
    }

    // OTP-related functions
    function verifyDelivery() {
        if (confirm("Verify delivery for order #" + orderId + "?")) {
            stopTracking();

            document.getElementById('status').innerHTML = `
                <div class="alert alert-info">
                    <div class="spinner-border spinner-border-sm" role="status"></div>
                    Sending OTP to customer...
                </div>`;

            fetch('resend-delivery-otp.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ order_id: orderId })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    document.getElementById('otpModal').style.display = 'flex';
                    document.getElementById('status').innerHTML = `
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> OTP sent to customer
                        </div>`;
                    startOTPTimer(900); // 15 minutes timer
                } else {
                    throw new Error(data.error || "Failed to send OTP");
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError("Could not verify delivery: " + error.message);
                startTracking();
            });
        }
    }

    function startOTPTimer(duration) {
        let timer = duration, minutes, seconds;
        const timerElement = document.getElementById('otpTimer');

        const interval = setInterval(function () {
            minutes = parseInt(timer / 60, 10);
            seconds = parseInt(timer % 60, 10);

            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;

            timerElement.textContent = "OTP expires in: " + minutes + ":" + seconds;

            if (--timer < 0) {
                clearInterval(interval);
                timerElement.textContent = "OTP expired";
                document.getElementById('otpInput').disabled = true;
            }
        }, 1000);
    }

    function validateOTPInput(input) {
        input.value = input.value.replace(/[^0-9]/g, '');
    }

    function submitOTP() {
        const otpInput = document.getElementById('otpInput').value;
        const errorElement = document.getElementById('otpError');

        if (otpInput.length !== 6) {
            errorElement.textContent = "Please enter a 6-digit OTP";
            return;
        }

        const formData = new FormData();
        formData.append('order_id', orderId);
        formData.append('otp', otpInput);

        fetch('verify-delivery-otp.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                markAsDelivered();
            } else {
                errorElement.textContent = data.error || "Invalid OTP. Please try again.";
            }
        })
        .catch(error => {
            errorElement.textContent = "Error verifying OTP. Please try again.";
            console.error('Error:', error);
        });
    }

    function resendOTP() {
        fetch('resend-delivery-otp.php', {
            method: 'POST',
            body: JSON.stringify({ order_id: orderId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("New OTP has been sent to the customer.");
                startOTPTimer(300);
                document.getElementById('otpInput').value = '';
                document.getElementById('otpInput').disabled = false;
            } else {
                alert("Failed to resend OTP: " + (data.error || "Unknown error"));
            }
        });
    }

    function closeOTPModal() {
        document.getElementById('otpModal').style.display = 'none';
        startTracking();
    }

    function markAsDelivered() {
        document.getElementById('status').innerHTML = `
            <div class="alert alert-info">
                <div class="spinner-border spinner-border-sm" role="status"></div>
                Completing delivery...
            </div>`;

        closeOTPModal();

        const formData = new FormData();
        formData.append('order_id', orderId);

        fetch('update-order-status.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('status').innerHTML = `
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> Order delivered successfully!
                    </div>`;
                fetch('clear-delivery-otp.php', { method: 'POST' });
                setTimeout(() => {
                    window.location.href = 'delivery-dashboard.php';
                }, 2000);
            } else {
                throw new Error(data.error || 'Failed to update order status');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError(`<i class="fas fa-exclamation-circle"></i> ${error.message}`);
            startTracking();
        });
    }
</script>

  <!-- Replace with your Google Maps API key -->
  <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAotC3fg_IyzRYHOklsbPJAs8irInjCD7g&callback=initMap&libraries=places">
  </script>
</body>
</html>