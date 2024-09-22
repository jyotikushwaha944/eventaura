<?php
session_start();
require 'header.php'; 
$events = isset($_SESSION['search_events']['events']) ? $_SESSION['search_events']['events'] : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        #map {
            height: 400px;
            width: 100%;
        }
    </style>
    <script>
        var map;
        var markers = [];
        var bounds = L.latLngBounds();

        var defaultIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });
        var activeIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        function initMap() {
            map = L.map('map').setView([51.505, -0.09], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            <?php foreach ($events as $event): ?>
                var latLng = [<?php echo htmlspecialchars($event['latitude']); ?>, <?php echo htmlspecialchars($event['longitude']); ?>];
                var marker = L.marker(latLng, {icon: defaultIcon})
                    .bindPopup("<b><?php echo htmlspecialchars($event['name']); ?></b><br><?php echo htmlspecialchars($event['description']); ?>");
                markers.push(marker);
                marker.addTo(map);
                bounds.extend(latLng);
            <?php endforeach; ?>

            map.fitBounds(bounds, {padding: [50, 50]});
        }

        $(document).ready(function() {
            initMap();

            $('.list-group-item').hover(
                function() {
                    $(this).addClass('active');
                    var lat = $(this).data('lat');
                    var lng = $(this).data('lng');
                    markers.forEach(marker => {
                        if (marker.getLatLng().lat == lat && marker.getLatLng().lng == lng) {
                            marker.setIcon(activeIcon);
                        }
                    });
                }, function() {
                    $(this).removeClass('active');
                    var lat = $(this).data('lat');
                    var lng = $(this).data('lng');
                    markers.forEach(marker => {
                        if (marker.getLatLng().lat == lat && marker.getLatLng().lng == lng) {
                            marker.setIcon(defaultIcon);
                        }
                    });
                }
            );

            $('.list-group-item').click(function() {
                var lat = $(this).data('lat');
                var lng = $(this).data('lng');
                map.setView([lat, lng], 13);
            });
        });
    </script>
</head>
<body>
    <div class="container-fluid mt-5">
        <h2 class="mb-4 text-center">Search Results</h2>
        <?php if (empty($events)): ?>
            <div class="alert alert-warning text-center">No events found.</div>
        <?php else: ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="list-group">
                        <?php foreach ($events as $event): ?>
                            <a href="event_detail.php?event_id=<?php echo htmlspecialchars($event['id']); ?>" class="list-group-item list-group-item-action" data-lat="<?php echo htmlspecialchars($event['latitude']); ?>" data-lng="<?php echo htmlspecialchars($event['longitude']); ?>">
                                <h5 class="mb-1"><?php echo htmlspecialchars($event['name']); ?></h5>
                                <p class="mb-1"><?php echo htmlspecialchars($event['description']); ?></p>
                                <small>Start DateTime: <?php echo htmlspecialchars($event['start_datetime']); ?></small><br>
                                <small>End DateTime: <?php echo htmlspecialchars($event['end_datetime']); ?></small><br>
                                <small>Location: <?php echo htmlspecialchars($event['venue']); ?></small><br>
                                <small>Price: <?php echo htmlspecialchars($event['price']); ?></small>
                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <a href="registerTicket.php?event_id=<?php echo htmlspecialchars($event['id']); ?>" class="btn btn-primary mt-2">Register</a>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div id="map"></div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>