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
  .centerer{
        width: 100% !important;
    }
    #nav-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px;
}

.filter-container {
  position: relative;
}

#filterToggleBtn {
  background-color: #4CAF50;
  color: white;
  padding: 8px 16px;
  border: none;
  cursor: pointer;
  border-radius: 5px;
}

.filter-dropdown {
  position: absolute;
  top: 40px;
  right: 0;
  background-color: white;
  border: 1px solid #ccc;
  border-radius: 5px;
  box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
  padding: 15px;
  width: 300px;
  z-index: 1000;
}

.filter-dropdown .close-btn {
  position: absolute;
  top: 5px;
  right: 5px;
  background: none;
  border: none;
  font-size: 20px;
  cursor: pointer;
}

.hidden {
  display: none;
}

#map {
  height: 100vh; /* Ensure the map takes up full height */
  width: 100%;
}
.gm-style-iw-d{
  
}
.card{
  box-shadow: none;
}
  </style>
<div id="nav-header">
<div class="flex">
<a href="<?php echo base_url ?>./?page=map" class="nav-link">
        <div class="icon"><i class="fas fa-shopping-cart"></i></div> <!-- Icon representing ecommerce -->
        <div class="labelmap">Ecommerce Map</div>
    </a>
    <a href="<?php echo base_url ?>./?page=map/farm-map" class="nav-link active">
        <div class="icon"><i class="fas fa-seedling"></i></div> <!-- Icon representing farming/agriculture -->
        <div class="labelmap">Farm Map</div>
    </a>

</div>



  <div style="display: flex; justify-content: center;" >
  <input type="text" id="searchInput" placeholder="Search by crop name, type, planting date, etc.">

  <div class="filter-container">
    <button id="filterToggleBtn">Filters</button>

    <div id="filterDropdown" class="filter-dropdown hidden bg-white p-4 border rounded shadow">
  <button id="closeFilterBtn" class="close btn btn-outline-secondary">&times;</button>

  <div class="form-group">
    <label for="cropNameSelect">Select Crop Name:</label>
    <select id="cropNameSelect" class="form-control">
      <option value="">Select Crop Name</option>
      <?php
        $cropNameSql = "SELECT DISTINCT Name FROM crop WHERE delete_flag = 0 AND is_deleted = 0";
        $cropNameResult = $conn->query($cropNameSql);
        if ($cropNameResult->num_rows > 0) {
          while($nameRow = $cropNameResult->fetch_assoc()) {
            echo "<option value='{$nameRow['Name']}'>{$nameRow['Name']}</option>";
          }
        }
      ?>
    </select>
  </div>

  <div class="form-group">
    <label for="cropTypeSelect">Select Crop Type:</label>
    <select id="cropTypeSelect" class="form-control" disabled>
      <option value="">Select Crop Type</option>
    </select>
  </div>

  <div class="form-group">
    <label for="plantingDateFrom">Planting Date From:</label>
    <input type="date" id="plantingDateFrom" class="form-control">
  </div>

  <div class="form-group">
    <label for="plantingDateTo">Planting Date To:</label>
    <input type="date" id="plantingDateTo" class="form-control">
  </div>

  <div class="form-group">
    <label for="datePlantedFrom">Date Planted From:</label>
    <input type="date" id="datePlantedFrom" class="form-control">
  </div>

  <div class="form-group">
    <label for="datePlantedTo">Date Planted To:</label>
    <input type="date" id="datePlantedTo" class="form-control">
  </div>

  <div class="form-group">
    <label for="sizeOfPlantationFrom">Size Of Plantation From:</label>
    <input type="number" id="sizeOfPlantationFrom" class="form-control" placeholder="Size Of Plantation From" step="any">
  </div>

  <div class="form-group">
    <label for="sizeOfPlantationTo">Size Of Plantation To:</label>
    <input type="number" id="sizeOfPlantationTo" class="form-control" placeholder="Size Of Plantation To" step="any">
  </div>

  <button id="filterButton" class="btn btn-primary">Filter</button>
</div>
  </div>

  </div>
</div>

  <!-- Map container -->
  <div id="map"></div>

  <script>
// Initialize the map
function initMap() {
    let markers = [];
    
    // Create a map centered at a default location
    const map = new google.maps.Map(document.getElementById("map"), {
        center: { lat: 13.232900, lng: 121.156900 }, // Default center coordinates
        zoom: 12, // Adjust the zoom level as needed
         gestureHandling: 'greedy'
    });

    // Custom marker icons
    const plantedMarker = 'uploads/marker100.png';
    const plannedMarker = 'uploads/markerhand.png'; // Different icon for planned crops

    // Arrays to store planted and planned crop locations
    const plantedCrops = [];
    const plannedCrops = [];

    <?php
    // Fetch both planted and planned crops in a single query
    $sql = "SELECT c.Id as CropId, c.Name as CropName, c.Type, c.PlannedPlantingDate, 
                   c.DatePlanted, c.SizeOfPlantation, c.Description, c.Picture1, 
                   c.Latitude, c.Longitude, v.contact as ContactNumber, v.facebook as Facebook
            FROM crop c
            INNER JOIN vendor_list v ON c.VendorId = v.id
            WHERE c.delete_flag = 0 AND c.Latitude IS NOT NULL AND c.Longitude IS NOT NULL
            ORDER BY c.Name ASC";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            if ($row['DatePlanted']) {
                // Planted crops
                echo "plantedCrops.push({ cropId: {$row['CropId']}, cropName: '{$row['CropName']}', cropDetails: " . json_encode([
                    "Type" => $row["Type"],
                    "PlannedPlantingDate" => $row["PlannedPlantingDate"],
                    "DatePlanted" => $row["DatePlanted"],
                    "SizeOfPlantation" => $row["SizeOfPlantation"],
                    "Description" => $row["Description"],
                    "Picture1" => $row["Picture1"]
                ]) . ", contactNumber: '{$row['ContactNumber']}', facebook: '{$row['Facebook']}', lat: {$row['Latitude']}, lng: {$row['Longitude']} });\n";
            } else {
                // Planned crops (without a DatePlanted)
                echo "plannedCrops.push({ cropId: {$row['CropId']}, cropName: '{$row['CropName']}', cropDetails: " . json_encode([
                    "Type" => $row["Type"],
                    "PlannedPlantingDate" => $row["PlannedPlantingDate"],
                    "SizeOfPlantation" => $row["SizeOfPlantation"],
                    "Description" => $row["Description"],
                    "Picture1" => $row["Picture1"]
                ]) . ", contactNumber: '{$row['ContactNumber']}', facebook: '{$row['Facebook']}', lat: {$row['Latitude']}, lng: {$row['Longitude']} });\n";
            }
        }
    } else {
        echo "plantedCrops = []; plannedCrops = [];"; // Default empty data
    }
    ?>

    // Function to generate markers and info windows
    function generateMarkers(cropArray, map, icon) {
        cropArray.forEach((crop) => {
            const cropMarker = new google.maps.Marker({
                position: { lat: parseFloat(crop.lat), lng: parseFloat(crop.lng) },
                map: map,
                title: crop.cropName,
                icon: icon,
            });

            const infowindow = new google.maps.InfoWindow({
                content: generateInfoWindowContent(crop),
            });

            cropMarker.infowindow = infowindow; // Associate infowindow with marker

            cropMarker.addListener("click", () => {
                infowindow.open(map, cropMarker);
            });

            // Store marker in markers array
            markers.push({ marker: cropMarker, crop });
        });
    }

    // Generate markers for planted crops
    generateMarkers(plantedCrops, map, plantedMarker);

    // Generate markers for planned crops
    generateMarkers(plannedCrops, map, plannedMarker);

    // Function to generate info window content for crops
    function generateInfoWindowContent(crop) {
    return `
        <div class="">
            <div class="">
                <div class="row">
                    <div class="col-md-4">
                        <img src="${crop.cropDetails.Picture1}" alt="Crop Image" width="100">
                    </div>
                    <div class="col-md-8">
                        <strong>${crop.cropName}</strong><br>
                        <ul class="list-unstyled">
                            <li><strong>Type:</strong> ${crop.cropDetails.Type}</li>
                            <li><strong>Planned Planting Date:</strong> ${crop.cropDetails.PlannedPlantingDate}</li>
                            <li>${crop.cropDetails.DatePlanted ? `<strong>Date Planted:</strong> ${crop.cropDetails.DatePlanted}` : ''}</li>
                            <li><strong>Size of Plantation:</strong> ${crop.cropDetails.SizeOfPlantation}</li>
                            <li><strong>Description:</strong> ${crop.cropDetails.Description}</li>
                            <li>
                                <strong>Contact:</strong> 
                                <a href="tel:${crop.contactNumber}" style="color: blue; text-decoration: underline;">
                                    <i class="fa fa-phone"></i> ${crop.contactNumber}
                                </a>
                            </li>
                            <li><a href="./?page=map/crop-preview&id=${crop.cropId}" style="background-color: green;" class="btn btn-sm btn-primary">View Details</a></li>
                            <li> 
                                <a href="https://www.facebook.com/${crop.facebook}" class="btn btn-sm btn-info">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    `;
}

document.getElementById('filterButton').addEventListener('click', function() {
    const selectedCropName = document.getElementById('cropNameSelect').value.toLowerCase().trim();
    const selectedCropType = document.getElementById('cropTypeSelect').value.toLowerCase().trim();

    const plantingDateFrom = document.getElementById('plantingDateFrom').value ? new Date(document.getElementById('plantingDateFrom').value) : null;
    const plantingDateTo = document.getElementById('plantingDateTo').value ? new Date(document.getElementById('plantingDateTo').value) : null;

    const datePlantedFrom = document.getElementById('datePlantedFrom').value ? new Date(document.getElementById('datePlantedFrom').value) : null;
    const datePlantedTo = document.getElementById('datePlantedTo').value ? new Date(document.getElementById('datePlantedTo').value) : null;

    const sizeOfPlantationFrom = document.getElementById('sizeOfPlantationFrom').value ? parseFloat(document.getElementById('sizeOfPlantationFrom').value) : null;
    const sizeOfPlantationTo = document.getElementById('sizeOfPlantationTo').value ? parseFloat(document.getElementById('sizeOfPlantationTo').value) : null;

    markers.forEach(({ marker, crop }) => {
        let matchesName = true;
        let matchesType = true;
        let matchesPlantingDate = true;
        let matchesDatePlanted = true;
        let matchesSizeOfPlantation = true;

        if (selectedCropName) {
            matchesName = crop.cropName.toLowerCase().trim() === selectedCropName;
        }
        if (selectedCropType) {
            matchesType = crop.cropDetails.Type.toLowerCase().trim() === selectedCropType;
        }
        if (plantingDateFrom || plantingDateTo) {
            const cropPlantingDate = crop.cropDetails.PlannedPlantingDate ? new Date(crop.cropDetails.PlannedPlantingDate) : null;
            matchesPlantingDate = cropPlantingDate && (!plantingDateFrom || cropPlantingDate >= plantingDateFrom) && (!plantingDateTo || cropPlantingDate <= plantingDateTo);
        }
        if (datePlantedFrom || datePlantedTo) {
            const cropDatePlanted = crop.cropDetails.DatePlanted ? new Date(crop.cropDetails.DatePlanted) : null;
            matchesDatePlanted = cropDatePlanted && (!datePlantedFrom || cropDatePlanted >= datePlantedFrom) && (!datePlantedTo || cropDatePlanted <= datePlantedTo);
        }
        if (sizeOfPlantationFrom || sizeOfPlantationTo) {
            const cropSize = crop.cropDetails.SizeOfPlantation ? parseFloat(crop.cropDetails.SizeOfPlantation) : null;
            matchesSizeOfPlantation = cropSize !== null && (!sizeOfPlantationFrom || cropSize >= sizeOfPlantationFrom) && (!sizeOfPlantationTo || cropSize <= sizeOfPlantationTo);
        }

        // Show marker if it matches name and type, and any of the date or size filters
        // Also show if only crop name/type is selected without filters
        if (matchesName && matchesType && (matchesPlantingDate || matchesDatePlanted || matchesSizeOfPlantation || (!plantingDateFrom && !plantingDateTo && !datePlantedFrom && !datePlantedTo && !sizeOfPlantationFrom && !sizeOfPlantationTo))) {
            marker.setVisible(true);
        } else {
            marker.setVisible(false);
        }
    });
});





}

// Load the Google Maps API and initialize the map
</script>

<script>
  document.getElementById('cropNameSelect').addEventListener('change', function() {
    const selectedCropName = this.value;

    if (selectedCropName) {
        // Fetch the types for the selected crop name using AJAX
        fetchCropTypes(selectedCropName);
    } else {
        document.getElementById('cropTypeSelect').innerHTML = '<option value="">Select Crop Type</option>';
        document.getElementById('cropTypeSelect').disabled = true;
    }
});

function fetchCropTypes(cropName) {
    const cropTypeSelect = document.getElementById('cropTypeSelect');
    cropTypeSelect.disabled = false;
    
    // Clear existing options
    cropTypeSelect.innerHTML = '<option value="">Select Crop Type</option>';
    
    // Fetch the crop types via AJAX
    fetch(`map/fetch_crop_types.php?cropName=${cropName}`)
        .then(response => response.json())
        .then(data => {
            data.forEach(type => {
                const option = document.createElement('option');
                option.value = type;
                option.textContent = type;
                cropTypeSelect.appendChild(option);
            });
        })
        .catch(error => console.error('Error fetching crop types:', error));
}

</script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const filterToggleBtn = document.getElementById('filterToggleBtn');
    const filterDropdown = document.getElementById('filterDropdown');
    const closeFilterBtn = document.getElementById('closeFilterBtn');

    filterToggleBtn.addEventListener('click', function() {
        filterDropdown.classList.toggle('hidden');
    });

    closeFilterBtn.addEventListener('click', function() {
        filterDropdown.classList.add('hidden');
    });
});

</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCPgOaKhjwvksUVP6qBQpjdq3bTQa57NuQ&callback=initMap" async></script>