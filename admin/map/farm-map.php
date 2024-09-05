<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Crop Locations Map with Advanced Search and Sidebar</title>
  <style>
    /* Set map and sidebar container size */
    #map {
      height: 600px; /* Adjust height as needed */
      width: calc(100% - 80px); /* Adjust width to leave space for sidebar */
      float: left; /* Float map to the left */
    }

    /* Sidebar styles */
    #sidebar {
      height: 600px; /* Same height as map */
      width: 80px; /* Adjust width as needed */
      background-color: #f4f4f4; /* Sidebar background color */
      float: right; /* Float sidebar to the right */
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: space-around;
      padding: 10px 0;
    }

    /* Sidebar link styles */
    .sidebar-link {
      text-decoration: none;
      color: #333; /* Link text color */
      font-size: 16px;
      text-align: center;
    }

    /* Sidebar link hover effect */
    .sidebar-link:hover {
      background-color: #ddd; /* Hover background color */
      color: #000; /* Hover text color */
      border-radius: 5px;
      padding: 5px;
    }

    /* Icon styles */
    .icon {
      display: block;
      margin-bottom: 5px;
      font-size: 24px;
    }

    /* Search input style */
    #searchInput {
      width: calc(100% - 100px); /* Adjust width to fit with sidebar */
      margin-bottom: 10px;
      padding: 8px;
      border-radius: 5px;
      border: 1px solid #ccc;
    }
  </style>
</head>
<body>
  <h1>Crop Locations Map with Advanced Search and Sidebar</h1>

  <!-- Search input -->
  <input type="text" id="searchInput" placeholder="Search by crop name, pest/disease, farm name, status, or area size (hectares)">

  <!-- Sidebar navigation -->
  <div id="sidebar">
    <a href="<?php echo base_url ?>vendor/?page=map/map" class="sidebar-link">
      <span class="icon">🌱</span>
      Map
    </a>
    <a href="<?php echo base_url ?>vendor/?page=map/farm-pestanddisease" class="sidebar-link">
      <span class="icon">🦠</span>
      Pests & Diseases
    </a>
    <a href="<?php echo base_url ?>vendor/?page=map/farm-harvest" class="sidebar-link">
      <span class="icon">🌾</span>
      Harvest
    </a>
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

      // Custom marker icon (replace with your marker image)
      const customMarker = '../uploads/marker100.png';

      // Array to store farm locations and associated crops with information fetched from PHP
      const farmData = [
        <?php
          // Your PHP code to fetch farm locations with pest and disease information
          $user_id = $_settings->userdata('id');
          $sql = "SELECT DISTINCT f.Id, f.Name AS FarmName, f.Latitude, f.Longitude, c.Name AS CropName, c.Type, cd.Name AS PestDiseaseName, cd.SizeOfAreaAffected, cd.Status
                  FROM farm f
                  JOIN crop c ON f.Id = c.FarmId
                  JOIN croppestdisease cd ON c.Id = cd.CropID
                  WHERE cd.Status != 'Fixed'";

          $result = $conn->query($sql);

          if ($result->num_rows > 0) {
            // Initialize an array to hold farm data
            $farmDataArray = [];
            while($row = $result->fetch_assoc()) {
              // Group farms by unique identifier (like FarmId)
              $farmDataArray[$row["Id"]][] = [
                "farmName" => $row["FarmName"],
                "cropName" => $row["CropName"],
                "pestDisease" => $row["PestDiseaseName"],
                "sizeOfAreaAffected" => $row["SizeOfAreaAffected"],
                "status" => $row["Status"],
                "lat" => $row["Latitude"],
                "lng" => $row["Longitude"]
              ];
            }

            // Generate JavaScript array for farm data
            foreach ($farmDataArray as $farmId => $farms) {
              echo "{ farmId: '{$farmId}', farms: [";
              foreach ($farms as $farm) {
                echo "{ farmName: '{$farm['farmName']}', cropName: '{$farm['cropName']}', pestDisease: '{$farm['pestDisease']}', sizeOfAreaAffected: '{$farm['sizeOfAreaAffected']}', status: '{$farm['status']}', lat: {$farm['lat']}, lng: {$farm['lng']} },";
              }
              echo "] },\n";
            }
          }
        ?>
      ];

      // Add farm markers, circles, and info windows to the map
      const markers = []; // Array to store markers for filtering

      farmData.forEach((farm) => {
        farm.farms.forEach((crop) => {
          // Create marker for farm location
          const marker = new google.maps.Marker({
            position: { lat: parseFloat(crop.lat), lng: parseFloat(crop.lng) },
            map: map,
            title: crop.cropName,
            icon: customMarker, // Use custom marker icon
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

          // Create info window content for farm (not changed as per your request)
          const infowindow = new google.maps.InfoWindow({
            content: generateInfoWindowContent(farm),
          });

          // Push marker to markers array for filtering
          markers.push({ marker, crop });

          // Open info window when marker is clicked
          marker.addListener("click", () => {
            infowindow.open(map, marker);
          });
        });
      });

      // Function to generate info window content for farms
      function generateInfoWindowContent(farm) {
        let content = `<strong>${farm.farms[0].farmName}</strong><br>`;
        farm.farms.forEach((farmDetail) => {
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

      // Function to filter markers based on search input
      document.getElementById('searchInput').addEventListener('input', function() {
        const searchQuery = this.value.toLowerCase().trim();
        markers.forEach((item) => {
          const { marker, crop } = item;
          const cropName = crop.cropName.toLowerCase();
          const farmName = crop.farmName.toLowerCase();
          const pestDisease = crop.pestDisease.toLowerCase();
          const status = crop.status.toLowerCase();
          const sizeOfArea = crop.sizeOfAreaAffected.toString().toLowerCase();

          if (cropName.includes(searchQuery) ||
              farmName.includes(searchQuery) ||
              pestDisease.includes(searchQuery) ||
              status.includes(searchQuery) ||
              sizeOfArea.includes(searchQuery)) {
            marker.setVisible(true); // Show marker if matches search
          } else {
            marker.setVisible(false); // Hide marker if does not match search
          }
        });
      });
    }
  </script>
</body>
</html>
