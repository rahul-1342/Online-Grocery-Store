<?php
include("includes/config.php");
$order_id = intval($_GET['order_id']);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Track Delivery</title>
  <style>
    #map { height: 90vh; width: 100%; }
  </style>
</head>
<body>
  <h2>Track Your Delivery</h2>
  <div id="map"></div>

  <script>
    let map, marker;

    function initMap() {
      map = new google.maps.Map(document.getElementById("map"), {
        zoom: 16,
        center: { lat: 0, lng: 0 }
      });

      marker = new google.maps.Marker({
        position: { lat: 0, lng: 0 },
        map: map,
        title: "Delivery Boy"
      });

      fetchLocation();
      setInterval(fetchLocation, 5000); // update every 5 sec
    }

    function fetchLocation() {
      fetch('get-location.php?order_id=<?= $order_id ?>')
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            const latLng = { lat: parseFloat(data.lat), lng: parseFloat(data.lon) };
            marker.setPosition(latLng);
            map.setCenter(latLng);
          }
        });
    }
  </script>

  <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAotC3fg_IyzRYHOklsbPJAs8irInjCD7g&callback=initMap">
  </script>
</body>
</html>
