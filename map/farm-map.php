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
#aiResponseModal {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: #fff;
    padding: 20px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    z-index: 10000;
}

#closeModal {
    cursor: pointer;
    float: right;
    font-size: 20px;
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
    <button id="aiFilterDropdownToggle" class="ml-2 btn btn-info">AI Recommendation</button>

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
  <div id="aiFilterDropdown" class="filter-dropdown hidden bg-white p-4 border rounded shadow">
    <button id="closeAIFilter" class="close btn btn-outline-secondary">&times;</button>
<div class="form-group">
    <label for="aiCropNameSelect">Select Crop Name:</label>
    <select id="aiCropNameSelect" class="form-control">
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
    <label for="aiCropTypeSelect">Select Crop Type:</label>
    <select id="aiCropTypeSelect" class="form-control" disabled>
      <option value="">Select Crop Type</option>
    </select>
</div>

    <div class="form-group">
      <label for="aiPlantingDateFrom">Planting Date From:</label>
      <input type="date" id="aiPlantingDateFrom" class="form-control">
    </div>
    <div class="form-group">
      <label for="aiPlantingDateTo">Planting Date To:</label>
      <input type="date" id="aiPlantingDateTo" class="form-control">
    </div>
    <div class="form-group">
        <label for="harvestAmount">Harvest Amount:</label>
        <input type="number" id="harvestAmount" class="form-control" placeholder="Enter amount in kg">
    </div>
    <button id="aiFetchRecommendation" class="btn btn-success">Get Recommendation</button>
  </div>
</div>

<!-- AI Recommendation Modal -->
<div class="modal fade" id="aiRecommendationModal" tabindex="-1" aria-labelledby="aiRecommendationLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="aiRecommendationLabel">AI Recommendation</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p id="aiRecommendationResult">Fetching recommendation...</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

  </div>
</div>
<!-- Modal HTML -->
<div id="aiResponseModal" style="display:none;">
    <div class="modal-content">
        <span id="closeModal">&times;</span>
        <h2>AI Response</h2>
        <p id="loadingMessage">Loading...</p>
        <p id="aiResponseText" style="display:none;"></p>
    </div>
</div>

  <!-- Map container -->
  <div id="map"></div>

  <script>
// Initialize the map
function initMap() {
    let markers = [];
    let polygons = []; // To store farm boundaries

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
    const farms = []; // To store farm boundary data

    <?php
    // Fetch both planted and planned crops and farm boundaries in a single query
    $sql = "SELECT c.Id as CropId, c.Name as CropName, c.Type, c.PlannedPlantingDate, 
                   c.DatePlanted, c.SizeOfPlantation, c.Description, c.Picture1, 
                   c.Latitude, c.Longitude, v.contact as ContactNumber, v.facebook as Facebook,
                   f.boundary 
            FROM crop c
            INNER JOIN vendor_list v ON c.VendorId = v.id
            LEFT JOIN farm f ON f.Id = c.FarmId
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
                // Planned crops
                echo "plannedCrops.push({ cropId: {$row['CropId']}, cropName: '{$row['CropName']}', cropDetails: " . json_encode([
                    "Type" => $row["Type"],
                    "PlannedPlantingDate" => $row["PlannedPlantingDate"],
                    "SizeOfPlantation" => $row["SizeOfPlantation"],
                    "Description" => $row["Description"],
                    "Picture1" => $row["Picture1"]
                ]) . ", contactNumber: '{$row['ContactNumber']}', facebook: '{$row['Facebook']}', lat: {$row['Latitude']}, lng: {$row['Longitude']} });\n";
            }

            if ($row['boundary']) {
                // Store farm boundaries
                echo "farms.push({ farmName: '{$row['CropName']}', boundary: {$row['boundary']} });\n";
            }
        }
    } else {
        echo "plantedCrops = []; plannedCrops = []; farms = [];";
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

            markers.push({ marker: cropMarker, crop });
        });
    }

    // Function to create farm boundaries
    function generateFarmBoundaries(farms, map) {
        farms.forEach((farm) => {
            const farmBoundaryCoords = farm.boundary.map(coord => ({ lat: coord.lat, lng: coord.lng }));

            const farmPolygon = new google.maps.Polygon({
                paths: farmBoundaryCoords,
                strokeColor: "#FF0000",
                strokeOpacity: 0.8,
                strokeWeight: 2,
                fillColor: "#FF0000",
                fillOpacity: 0.35
            });

            farmPolygon.setMap(map);
            polygons.push(farmPolygon); // Store polygon in polygons array
        });
    }

    // Generate markers for planted crops
    generateMarkers(plantedCrops, map, plantedMarker);

    // Generate markers for planned crops
    generateMarkers(plannedCrops, map, plannedMarker);

    // Generate farm boundaries
    generateFarmBoundaries(farms, map);

    // Info window content
    function generateInfoWindowContent(crop) {
        return `
            <div>
                <strong>${crop.cropName}</strong><br>
                <ul>
                    <li><strong>Type:</strong> ${crop.cropDetails.Type}</li>
                    <li><strong>Planned Planting Date:</strong> ${crop.cropDetails.PlannedPlantingDate}</li>
                    ${crop.cropDetails.DatePlanted ? `<li><strong>Date Planted:</strong> ${crop.cropDetails.DatePlanted}</li>` : ''}
                    <li><strong>Size of Plantation:</strong> ${crop.cropDetails.SizeOfPlantation}</li>
                    <li><strong>Description:</strong> ${crop.cropDetails.Description}</li>
                    <li>
                        <strong>Contact:</strong> <a href="tel:${crop.contactNumber}">${crop.contactNumber}</a>
                    </li>
                    <li><a href="./?page=map/crop-preview&id=${crop.cropId}" class="btn btn-sm btn-primary">View Details</a></li>
                    <li><a href="https://www.facebook.com/${crop.facebook}" class="btn btn-sm btn-info"><i class="fab fa-facebook-f"></i></a></li>
                </ul>
            </div>
        `;
    }
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
  document.getElementById('aiCropNameSelect').addEventListener('change', function() {
    const selectedCropName = this.value;

    if (selectedCropName) {
        // Fetch the types for the selected crop name using AJAX
        fetchAiCropTypes(selectedCropName);
    } else {
        document.getElementById('aiCropTypeSelect').innerHTML = '<option value="">Select Crop Type</option>';
        document.getElementById('aiCropTypeSelect').disabled = true;
    }
});

function fetchAiCropTypes(cropName) {
    const aiCropTypeSelect = document.getElementById('aiCropTypeSelect');
    aiCropTypeSelect.disabled = false;
    
    // Clear existing options
    aiCropTypeSelect.innerHTML = '<option value="">Select Crop Type</option>';
    
    // Fetch the crop types via AJAX
    fetch(`map/fetch_crop_types.php?cropName=${cropName}`)
        .then(response => response.json())
        .then(data => {
            data.forEach(type => {
                const option = document.createElement('option');
                option.value = type;
                option.textContent = type;
                aiCropTypeSelect.appendChild(option);
            });
        })
        .catch(error => console.error('Error fetching crop types:', error));
}

</script>
<script>
document.getElementById('aiFetchRecommendation').addEventListener('click', () => {
    const cropName = document.getElementById('aiCropNameSelect').value;
    const cropType = document.getElementById('aiCropTypeSelect').value;
    const plantingDateFrom = document.getElementById('aiPlantingDateFrom').value;
    const plantingDateTo = document.getElementById('aiPlantingDateTo').value;
    const harvestAmount = document.getElementById('harvestAmount').value;
    const aiResponseModal = document.getElementById('aiResponseModal');

    if (!aiResponseModal) {
        console.error("aiResponseModal element is missing in the DOM");
        return;
    }

    if (!cropName || !cropType || !plantingDateFrom || !plantingDateTo || !harvestAmount) {
        alert("Please fill in all fields.");
        return;
    }

    const cropFilter = {
        cropName,
        cropType,
        plantingDateFrom,
        plantingDateTo,
        harvestAmount
    };

    $.ajax({
        url: 'map/fetch_filtered_crops.php',
        method: 'POST',
        data: cropFilter,
        dataType: 'json',
        success: (crops) => {
            if (crops.length === 0) {
                alert("No crops match the given criteria.");
                return;
            }

            let cropListMessage = 'Based on your criteria, here are some crops you can consider:';
            crops.forEach(crop => {
                cropListMessage += `<br>- ${crop.Name} (${crop.Type}), Plantation Size: ${crop.SizeOfPlantation} hectares, Location: ${crop.Latitude}, ${crop.Longitude}`;
            });

            const message = `
                I am looking to purchase crops based on the following needs:
                Planting between ${plantingDateFrom} and ${plantingDateTo}. Explain why.
                Here is the filtered list of available crops for your reference: 
                ${cropListMessage}.
                
                Include recommended crop IDs in this format: -{{1, 2, 3}}-
            `;

            aiResponseModal.style.display = "block";
            document.getElementById('loadingMessage').style.display = "block";
            document.getElementById('aiResponseText').style.display = "none";

            $.ajax({
                url: 'map/ai.php',
                method: 'POST',
                data: { message },
                dataType: 'json',
                success: (response) => {
                    const aiResponse = response.content.replace(/\n/g, '<br>')
                        .replace(/\*\*(.*?)\*\*/g, '<b>$1</b>')
                        .replace(/(?<!\d)(\d+)\.\s/g, '<br>$1. ');

                        const cropIdMatch = aiResponse.match(/-{{\s*([\d,\s]+)\s*}}-/);
                        if (cropIdMatch) {
                        const cropIds = cropIdMatch[1].split(',').map(id => id.trim());
                        $.ajax({
                            url: 'map/fetch_crop_details.php',
                            method: 'POST',
                            data: { cropIds },
                            dataType: 'json',
                            success: (crops) => {
                                const cropCardsContainer = document.getElementById('cropCardsContainer');
                                cropCardsContainer.innerHTML = '';
                                crops.forEach(crop => {
                                const cropCard = document.createElement('div');
                                cropCard.className = 'crop-card';
                                cropCard.innerHTML = `
                                    <h3>${crop.Name} (${crop.Type})</h3>
                                    <p>Size: ${crop.SizeOfPlantation} hectares</p>
                                    <p>Location: ${crop.Latitude}, ${crop.Longitude}</p>
                                `;
                                cropCardsContainer.appendChild(cropCard);
                            });

                            },
                            error: (xhr, status, error) => {
                                console.error("Error fetching crop details: ", status, error);
                            }
                        });
                    }

                    document.getElementById('loadingMessage').style.display = "none";
                    document.getElementById('aiResponseText').style.display = "block";
                    document.getElementById('aiResponseText').innerHTML = aiResponse.replace(/-{{[\d, ]+}}-/g, '');
                },
                error: (xhr, status, error) => {
                    console.error("Error processing AI request: ", status, error);
                    document.getElementById('loadingMessage').style.display = "none";
                    document.getElementById('aiResponseText').style.display = "block";
                    document.getElementById('aiResponseText').innerHTML = "Error processing your request.";
                }
            });
        },
        error: (xhr, status, error) => {
            alert("Error fetching filtered crops.");
            console.error("Error details:", status, error);
        }
    });
});

document.getElementById('closeModal').addEventListener('click', () => {
    document.getElementById('aiResponseModal').style.display = "none";
});

</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Main filter elements
    const filterToggleBtn = document.getElementById('filterToggleBtn');
    const filterDropdown = document.getElementById('filterDropdown');
    const closeFilterBtn = document.getElementById('closeFilterBtn');

    // AI filter elements
    const aiFilterDropdownToggle = document.getElementById('aiFilterDropdownToggle');
    const aiFilterDropdown = document.getElementById('aiFilterDropdown');
    const closeAIFilter = document.getElementById('closeAIFilter');

    // Toggle filter dropdown
    filterToggleBtn?.addEventListener('click', function() {
        filterDropdown?.classList.toggle('hidden');
    });

    // Close filter dropdown
    closeFilterBtn?.addEventListener('click', function() {
        filterDropdown?.classList.add('hidden');
    });

    // Toggle AI filter dropdown
    aiFilterDropdownToggle?.addEventListener('click', function() {
        aiFilterDropdown?.classList.toggle('hidden');
    });

    // Close AI filter dropdown
    closeAIFilter?.addEventListener('click', function() {
        aiFilterDropdown?.classList.add('hidden');
    });
});

</script>
<script src="https://maps.googleapis.com/maps/api/js?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&callback=initMap" async></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
