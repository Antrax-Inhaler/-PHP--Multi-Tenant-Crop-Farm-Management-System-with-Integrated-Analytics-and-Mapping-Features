<?php
$user_id = $_settings->userdata('id');
$sql = "SELECT DISTINCT f.Id, f.Name AS FarmName, f.Latitude, f.Longitude, c.Name AS CropName, c.Type, cd.Name AS PestDiseaseName, cd.SizeOfAreaAffected, cd.Status, f.VendorListId
        FROM farm f
        JOIN crop c ON f.Id = c.FarmId
        JOIN croppestdisease cd ON c.Id = cd.CropID
        WHERE cd.Status != 'Fixed'";

$result = $conn->query($sql);

$currentVendorFarms = [];
$otherFarms = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $farmData = [
            "farmName" => $row["FarmName"],
            "cropName" => $row["CropName"],
            "pestDisease" => $row["PestDiseaseName"],
            "sizeOfAreaAffected" => $row["SizeOfAreaAffected"],
            "status" => $row["Status"],
            "lat" => $row["Latitude"],
            "lng" => $row["Longitude"],
        ];

        if ($row["VendorListId"] == $user_id) {
            $currentVendorFarms[$row["Id"]][] = $farmData;
        } else {
            $otherFarms[$row["Id"]][] = $farmData;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crop Locations Map with Advanced Search and Sidebar</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Set map container size */
        #map {
            height: calc(100vh - 50px); /* Adjust map height */
            width: 100%; /* Full width */
        }
        /* Navigation header styles */
        #nav-header {
            height: 50px; /* Fixed height for header */
            width: 100%; /* Full width */
            background-color: #333; /* Header background color */
            color: #fff; /* Header text color */
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            box-sizing: border-box;
        }
        /* Navigation link styles */
        .nav-link {
            text-decoration: none;
            color: #fff; /* Link text color */
            font-size: 16px;
            display: flex;
            align-items: center;
        }
        /* Navigation link hover effect */
        .nav-link:hover {
            color: #ddd; /* Hover text color */
        }
        /* Icon styles */
        .icon {
            margin-right: 5px;
            font-size: 24px;
        }
        /* Search input style */
        #searchInput {
            width: 300px; /* Adjust width as needed */
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .flex {
            display: flex;
            justify-content: flex-start;
        }
        @media (max-width: 768px) {
            .labelmap {
                display: none;
            }
            #searchInput {
                width: 100%;
            }
        }
        .active {
            color: #2ddc9a;
        }
        .container-fluid {
            padding-right: 0px;
            padding-left: 0px;
        }
    </style>
</head>
<body>
    <!-- Navigation header -->
    <div id="nav-header">
        <div class="flex">
            <a href="<?php echo base_url ?>vendor/?page=map/map" class="nav-link">
                <div class="icon"><i class="fas fa-map"></i></div>
                <div class="labelmap">Farm Map</div>
            </a>
            <a href="<?php echo base_url ?>vendor/?page=map/farm-pestanddisease" class="nav-link active">
                <div class="icon"><i class="fas fa-bug"></i></div>
                <div class="labelmap">Pests & Diseases</div>
            </a>
            <a href="<?php echo base_url ?>vendor/?page=map/farm-harvest" class="nav-link">
                <div class="icon"><i class="fas fa-seedling"></i></div>
                <div class="labelmap">Harvest</div>
            </a>
        </div>
        <input type="text" id="searchInput" placeholder="Search by crop name, pest/disease, farm name, status, or area size (hectares)">
    </div>

    <!-- Map container -->
    <div id="map"></div>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC-dFHYjTqEVLndbN2gdvXsx09jfJHmNc8&callback=initMap" async defer></script>

    <script>
        // Initialize the map
        function initMap() {
            // Create a map centered at a default location
            const map = new google.maps.Map(document.getElementById("map"), {
                center: { lat: 13.232900, lng: 121.156900 }, // Default center coordinates (adjust as needed)
                zoom: 12, // Adjust the zoom level as needed
            });

            // Custom marker icons
            const vendorMarkerIcon = '../uploads/vendor_marker.png'; // Marker for vendor's farms
            const otherMarkerIcon = '../uploads/other_marker.png'; // Marker for other farms

            // Function to add markers and circles to the map
            function addMarkers(farms, markerIcon) {
                farms.forEach((farm) => {
                    farm.forEach((crop) => {
                        // Create marker for farm location
                        const marker = new google.maps.Marker({
                            position: { lat: parseFloat(crop.lat), lng: parseFloat(crop.lng) },
                            map: map,
                            title: crop.cropName,
                            icon: markerIcon, // Use custom marker icon
                        });

                        // Calculate radius in meters from size of affected area (assuming it's in hectares)
                        const radiusInMeters = Math.sqrt(crop.sizeOfAreaAffected * 10000 / Math.PI);

                        // Create circle for affected area
                        const circle = new google.maps.Circle({
                            strokeColor: '#FF0000',
                            strokeOpacity: 0.8,
                            strokeWeight: 2,
                            fillColor: '#FF0000',
                            fillOpacity: 0.35,
                            map: map,
                            center: { lat: parseFloat(crop.lat), lng: parseFloat(crop.lng) },
                            radius: radiusInMeters,
                        });

                        // Create info window content for farm
                        const infowindow = new google.maps.InfoWindow({
                            content: generateInfoWindowContent(farm),
                        });

                        // Open info window when marker is clicked
                        marker.addListener("click", () => {
                            infowindow.open(map, marker);
                        });
                    });
                });
            }

            // Function to generate info window content for farms
            function generateInfoWindowContent(farm) {
                let content = `<strong>${farm[0].farmName}</strong><br>`;
                farm.forEach((farmDetail) => {
                    content += `
                        <div class="card mt-3">
                            <div class="card-body">
                                <strong>Crop Affected:</strong> ${farmDetail.cropName}<br>
                                <strong>Pest/Disease:</strong> ${farmDetail.pestDisease}<br>
                                <strong>Size of Area Affected:</strong> ${farmDetail.sizeOfAreaAffected} hectares<br>
                                <strong>Status:</strong> ${farmDetail.status}<br>
                                <a href="https://www.google.com/maps/dir/?api=1&destination=${farmDetail.lat},${farmDetail.lng}" target="_blank">Get Directions</a>
                            </div>
                        </div>
                    `;
                });
                return content;
            }

            // Add markers and circles for the current vendor's farms
            const currentVendorFarms = <?php echo json_encode(array_values($currentVendorFarms)); ?>;
            addMarkers(currentVendorFarms, vendorMarkerIcon);

            // Add markers and circles for other farms
            const otherFarms = <?php echo json_encode(array_values($otherFarms)); ?>;
            addMarkers(otherFarms, otherMarkerIcon);
        }
    </script>
</body>
</html>
