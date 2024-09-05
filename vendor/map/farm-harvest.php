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
    .flex{
      display: flex;
      justify-content: flex-start;
    }
    @media (max-width: 768px) {
  /* Hide desktop menu on mobile screens */
  .labelmap {
    display: none;
  }
  #searchInput{
    width: 100%;
  }
}
.active{

  color: #2ddc9a;}
  .container-fluid{
    padding-right: 0px;
    padding-left: 0px;
  }
  </style>
  <!-- Navigation header -->
  <div id="nav-header">
  <div class="flex" >
      <a href="<?php echo base_url ?>vendor/?page=map/map" class="nav-link ">
        <div class="icon"><i class="fas fa-map"></i></div>
        <div class="labelmap">Farm Map</div>
      </a>
      <a href="<?php echo base_url ?>vendor/?page=map/farm-pestanddisease" class="nav-link ">
        <div class="icon"><i class="fas fa-bug"></i></div>
        <div class="labelmap">Pests & Diseases</div>       
      </a>
      <a href="<?php echo base_url ?>vendor/?page=map/farm-harvest" class="nav-link active">
        <div class="icon"><i class="fas fa-seedling"></i></div>
        <div class="labelmap">Harvest</div>
      </a>
    </div>
    <input type="text" id="searchInput" placeholder="Search by crop name, type, planting date, etc.">
  </div>

  <!-- Map container -->
  <div id="map"></div>

  <script>
    // Initialize the map
    function initMap() {
      // Create a map centered at a default location
      const map = new google.maps.Map(document.getElementById("map"), {
        center: { lat: 13.232900, lng: 121.156900 },// Default center coordinates (adjust as needed)
        zoom: 12, // Adjust the zoom level as needed
      });

      // Custom marker icon
      const customMarker = '../uploads/marker100.png';

      // Array to store farm locations and associated crops with harvest information fetched from PHP
      const farmData = [
        <?php
          // Your PHP code to fetch farm locations and associated crops with harvest information based on the SQL query
          $user_id = $_settings->userdata('id');
          $sql = "SELECT f.Name as FarmName, f.Latitude as FarmLat, f.Longitude as FarmLng,
                         c.Name as CropName, c.Type, c.Description, c.Picture1,
                         h.HarvestedDate, h.AmountOfHarvest, h.Paid
                  FROM farm f
                  INNER JOIN crop c ON f.Id = c.FarmId
                  INNER JOIN harvest h ON c.Id = h.CropId
                  WHERE c.delete_flag = 0
                  ORDER BY f.Name ASC, c.Name ASC";

          $result = $conn->query($sql);

          if ($result->num_rows > 0) {
            // Initialize an array to hold farm data
            $farmDataArray = [];
            while($row = $result->fetch_assoc()) {
              // Group crops by farm
              $farmDataArray[$row["FarmName"]][] = [
                "cropName" => $row["CropName"],
                "cropDetails" => [
                  "Type" => $row["Type"],
                  "Description" => $row["Description"],
                  "Picture1" => $row["Picture1"],
                  "HarvestedDate" => $row["HarvestedDate"],
                  "AmountOfHarvest" => $row["AmountOfHarvest"],
                  "Paid" => $row["Paid"]
                ],
                "lat" => $row["FarmLat"],
                "lng" => $row["FarmLng"]
              ];
            }

            // Generate JavaScript array for farm data
            foreach ($farmDataArray as $farmName => $crops) {
              echo "{ farmName: '{$farmName}', crops: [";
              foreach ($crops as $crop) {
                echo "{ cropName: '{$crop['cropName']}', cropDetails: " . json_encode($crop['cropDetails']) . ", lat: {$crop['lat']}, lng: {$crop['lng']} },";
              }
              echo "] },\n";
            }
          } else {
            echo "{ farmName: 'No farms found', crops: [] }"; // Default empty data
          }
        ?>
      ];

      // Array to store markers for filtering
      const markers = [];

      // Add farm markers and crop info windows to the map
      farmData.forEach((farm) => {
        const farmMarker = new google.maps.Marker({
          position: { lat: parseFloat(farm.crops[0].lat), lng: parseFloat(farm.crops[0].lng) }, // Use first crop coordinates
          map: map,
          title: farm.farmName,
          icon: customMarker, // Use custom marker icon
        });

        const infowindow = new google.maps.InfoWindow({
          content: generateInfoWindowContent(farm),
        });

        farmMarker.addListener("click", () => {
          infowindow.open(map, farmMarker);
        });

        // Push marker to markers array for filtering
        markers.push({ marker: farmMarker, farm });
      });

      // Function to generate info window content for farms and associated crops with harvest details
      function generateInfoWindowContent(farm) {
        let content = `<strong>${farm.farmName}</strong><br>`;
        farm.crops.forEach((crop) => {
          content += `
            <div class="card mt-3">
              <div class="card-body">
                <div class="row">
                  <div class="col-md-4">
                    <img src="${crop.cropDetails.Picture1}" alt="Crop Image" width="100">
                  </div>
                  <div class="col-md-8">
                    <strong>${crop.cropName}</strong><br>
                    <ul class="list-unstyled">
                      <li><strong title="Type">Type:</strong> ${crop.cropDetails.Type}</li>
                      <li><strong title="Harvested Date">Harvested Date:</strong> ${crop.cropDetails.HarvestedDate}</li>
                      <li><strong title="Amount of Harvest">Amount of Harvest:</strong> ${crop.cropDetails.AmountOfHarvest}</li>
                      <li><strong title="Paid">Paid:</strong> ${crop.cropDetails.Paid ? 'Yes' : 'No'}</li>
                      <li><strong title="Description">Description:</strong> ${crop.cropDetails.Description}</li>
                    </ul>
                  </div>
                </div>
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
          const { marker, farm } = item;
          const farmName = farm.farmName.toLowerCase();
          let found = false;

          farm.crops.forEach((crop) => {
            const cropName = crop.cropName.toLowerCase();
            const cropType = crop.cropDetails.Type.toLowerCase();
            const harvestedDate = crop.cropDetails.HarvestedDate.toLowerCase();

            if (cropName.includes(searchQuery) || cropType.includes(searchQuery) || harvestedDate.includes(searchQuery)) {
              found = true;
            }
          });

          if (farmName.includes(searchQuery) || found) {
            marker.setVisible(true); // Show marker if matches search
          } else {
            marker.setVisible(false); // Hide marker if does not match search
          }
        });
      });
    }
  </script>
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCPgOaKhjwvksUVP6qBQpjdq3bTQa57NuQ&callback=initMap" async></script>