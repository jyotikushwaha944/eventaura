<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event List</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />

    <style>
        #map {
            height: 400px;
            width: 100%;
        }
        .form-control-sm {
            font-size: 0.875rem; 
        }
        .form-group {
            max-width: 500px; 
        }
        .leaflet-control-geocoder {
            max-width: 300px; 
        }

        #autocomplete-dropdown {
            width: 100%; 
        }
        .dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            margin-top: 0.5rem;
            min-width: 160px;
            max-width: 300px; 
            z-index: 1000;
            overflow-wrap: break-word; 
            word-wrap: break-word; 
            text-align: left; 
        }
        .dropdown-menu.show {
            display: block;
        }
        .dropdown-item {
            font-size: 0.55rem; 
            cursor: pointer;
            white-space: normal; 
            overflow: hidden; 
            text-overflow: ellipsis; 
            padding: 0.5rem 0.5rem !important;  
            line-height: 1.25; 
        }

        .user-profile {
            position: relative;
        }
        
        .logo img {
           max-height: 100px; 
           height: auto; 
        }
    </style>
</head>
<body>
<header class="bg-primary"> 
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center py-3">
            <div class="logo">
                <a href="/eventaura/index.php">
                    <img src="/eventaura/img/lojoo.png" alt="logo" class="img-fluid" style="max-height: 100px;"> 
                </a>
            </div>
            <nav class="main-nav d-flex align-items-center">
                <div class="search-bar d-flex ml-4">
                    <!-- Search Events Form -->
                    <form class="form-inline mr-2" action="/eventaura/searchevents.php" method="POST">
                        <div class="input-group">
                            <input class="form-control form-control-sm" type="text" name="search_query" placeholder="Search events">
                            <div class="input-group-append">
                                <button class="btn btn-outline-light btn-sm" type="submit"> 
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                    <!-- Search Places Form -->
                    <form class="form-inline" action="/eventaura/searchbylocation.php" method="POST">
                        <div class="input-group position-relative">
                            <input id="location-input" class="form-control form-control-sm" type="text" name="search_location_query" placeholder="Choose location" autocomplete="off">
                            <div class="input-group-append">
                                <button class="btn btn-outline-light btn-sm" type="submit"> 
                                    <i class="fas fa-search-location"></i>
                                </button>
                            </div>
                            <div id="autocomplete-dropdown" class="dropdown-menu"></div>
                        </div>
                        <input type="hidden" id="lat" name="latitude">
                        <input type="hidden" id="lng" name="longitude">
                    </form>
                    <ul class="nav mb-0">
                        <li class="nav-item">
                            <a class="nav-link text-white" href="/eventaura/createEvent.php">Create Event</a> 
                        </li>
                        <?php if (isset($_SESSION['usertype_id']) && $_SESSION['usertype_id'] === 3): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link text-white dropdown-toggle" href="#" id="manageMenuLink" role="button"> 
                                    Manage
                                </a>
                                <div class="dropdown-menu" aria-labelledby="manageMenuLink">
                                    <a class="dropdown-item" href="/eventaura/admin/manage_users.php"><i class="fas fa-user-cog"></i> Users</a>
                                    <a class="dropdown-item" href="/eventaura/admin/manage_events.php"><i class="fas fa-calendar-alt"></i> Events</a>
                                    <a class="dropdown-item" href="/eventaura/admin/manage_venues.php"><i class="fas fa-map-marker-alt"></i> Venues</a> <!-- New Link -->
                                </div>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link text-white" href="/eventaura/aboutus.php">About Us</a> 
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </nav>
            <div class="user-profile ml-4 d-flex align-items-center">
                <?php if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn']): ?>
                    <div class="dropdown">
                        <a class="btn btn-outline-light btn-sm dropdown-toggle" href="#" role="button" id="dropdownMenuLink" aria-haspopup="true" aria-expanded="false"> 
                            Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                            <a class="dropdown-item" href="/eventaura/mytickets.php"><i class="fas fa-ticket-alt"></i> My Tickets</a>
                            <a class="dropdown-item" href="/eventaura/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a class="btn btn-outline-light btn-sm mr-2" href="/eventaura/login.php">Log In</a> 
                    <a class="btn btn-outline-light btn-sm" href="/eventaura/signup.php">Sign Up</a> 
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>


<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const profileDropdown = document.querySelector('.user-profile .dropdown-menu');
        const profileButton = document.querySelector('.user-profile .dropdown-toggle');

        if(profileButton){
            profileButton.addEventListener('click', function (event) {
            event.preventDefault();
            profileDropdown.classList.toggle('show');
        });
    }
       
        // Hide dropdown when clicking outside of it
        document.addEventListener('click', function (event) {
            if (!profileDropdown.contains(event.target) && !profileButton.contains(event.target)) {
                profileDropdown.classList.remove('show');
            }
        });

        // Manage dropdown logic
        const manageDropdown = document.querySelector('.nav-item.dropdown .dropdown-menu');
        const manageButton = document.querySelector('.nav-item.dropdown .dropdown-toggle');
        if(manageDropdown && manageButton){
            manageButton.addEventListener('click', function (event) {
            event.preventDefault();
            manageDropdown.classList.toggle('show');
        });

        // Hide dropdown when clicking outside of it
        document.addEventListener('click', function (event) {
            if (!manageDropdown.contains(event.target) && !manageButton.contains(event.target)) {
                manageDropdown.classList.remove('show');
            }
        });

        }
     
        // Autocomplete functionality for location input
        if (L.Control.Geocoder) {
            const locationInput = document.getElementById('location-input');
            const autocompleteDropdown = document.getElementById('autocomplete-dropdown');
            const latInput = document.getElementById('lat');
            const lngInput = document.getElementById('lng');
            const geocoder = L.Control.Geocoder.nominatim();

            locationInput.addEventListener('input', function () {
                const query = this.value;
                if (query.length >= 4) {
                    geocoder.geocode(query, function (results) {
                        autocompleteDropdown.innerHTML = '';
                        autocompleteDropdown.style.display = 'none';

                        if (results.length > 0) {
                            results.forEach(function (result) {
                                const option = document.createElement('div');
                                option.className = 'dropdown-item';
                                option.innerText = result.name;
                                option.addEventListener('click', function () {
                                    locationInput.value = result.name;
                                    latInput.value = result.center.lat;
                                    lngInput.value = result.center.lng;
                                    autocompleteDropdown.innerHTML = '';
                                    autocompleteDropdown.style.display = 'none';
                                });
                                autocompleteDropdown.appendChild(option);
                            });
                            autocompleteDropdown.style.display = 'block';
                        }
                    });
                } else {
                    autocompleteDropdown.innerHTML = '';
                    autocompleteDropdown.style.display = 'none';
                }
            });

            document.addEventListener('click', function (event) {
                if (!locationInput.contains(event.target)) {
                    autocompleteDropdown.innerHTML = '';
                    autocompleteDropdown.style.display = 'none';
                }
            });
        } else {
            console.error('Leaflet Control Geocoder library is not loaded.');
        }
    });
</script>
</body>
</html>
