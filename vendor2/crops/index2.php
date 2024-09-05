<?php
// Assuming $_settings->userdata('id') is available and safe from SQL injection
$user_id = $_settings->userdata('id');

// Number of Farms Query
$farm_count_query = "SELECT COUNT(*) as farm_count FROM farm WHERE VendorListId = '{$user_id}'";
$result_farms = $conn->query($farm_count_query);
$row_farms = $result_farms->fetch_assoc();
$total_farms = $row_farms['farm_count'];

// Total Crops Query
$crop_count_query = "SELECT COUNT(*) as crop_count FROM crop WHERE VendorId = '{$user_id}' AND is_deleted = 0";
$result_crops = $conn->query($crop_count_query);
$row_crops = $result_crops->fetch_assoc();
$total_crops = $row_crops['crop_count'];

// Crop with Pest and Disease Query
$pest_disease_count_query = "SELECT COUNT(DISTINCT crop.Id) as pest_disease_count FROM crop JOIN croppestdisease ON crop.Id = croppestdisease.CropID WHERE crop.VendorId = '{$user_id}' AND crop.is_deleted = 0";
$result_pest_disease = $conn->query($pest_disease_count_query);
$row_pest_disease = $result_pest_disease->fetch_assoc();
$total_pest_disease = $row_pest_disease['pest_disease_count'];
?>

<style>
  #cover-image {
    width: calc(100%);
    height: 50vh;
    object-fit: cover;
    object-position: center center;
  }

  /* Additional CSS for formatting */
  .info-header {
    font-size: 1.25rem;
    margin-bottom: 10px;
  }
      
        @media (max-width: 1000px) {
    /* Hide desktop menu on mobile screens */

    .info-header {
            display: inline-block;
            white-space: nowrap;
            animation: moveLeft 10s linear infinite;
        }

        @keyframes moveLeft {
            0% { transform: translateX(100%); }
            100% { transform: translateX(-120%); }
        }

        .move{
            overflow: hidden;
            width: 200px;
        }

        .info-box-content {
            white-space: nowrap;
            overflow: hidden;

        }

        .info-box-number {
            white-space: nowrap;
        }
    /* Add a new element for the mobile menu icon */
    .move {
     width: 80%;
    }
  }
</style>
<h2 class="text-center mt-4 mb-4">Farm Information</h2>
<div class="row">
    <div class="col-6 col-sm-4 col-md-4">
        <div class="info-box">
            <span class="info-box-icon bg-gradient-primary elevation-1"><i class="fas fa-th-list"></i></span>
            <div class="info-box-content">
                <div class="move">
                    <div class="info-header">Number of Farms</div>
                </div>
                <span id="totalFarms" class="info-box-number text-right h4">0</span>
            </div>
        </div>
    </div>

    <div class="col-6 col-sm-4 col-md-4">
        <div class="info-box">
            <span class="info-box-icon bg-gradient-secondary elevation-1"><i class="fas fa-boxes"></i></span>
            <div class="info-box-content">
                <div class="move">
                    <div class="info-header">Total Crops</div>
                </div>
                <span id="totalCrops" class="info-box-number text-right h4">0</span>
            </div>
        </div>
    </div>

    <div class="col-6 col-sm-4 col-md-4">
        <div class="info-box">
            <span class="info-box-icon bg-gradient-success elevation-1"><i class="fas fa-dollar-sign"></i></span>
            <div class="info-box-content">
                <div class="move">
                    <div class="info-header">Crop with Pest and Disease</div>
                </div>
                <span id="totalPestDisease" class="info-box-number text-right h4">0</span>
            </div>
        </div>
    </div>

    <div class="col-6 col-sm-4 col-md-4">
        <div class="info-box">
            <span class="info-box-icon bg-gradient-info elevation-1"><i class="fas fa-money-bill-wave"></i></span>
            <div class="info-box-content">
                <div class="move">
                    <div class="info-header">Number of Harvest</div>
                </div>
                <span id="totalHarvest" class="info-box-number text-right h4">₱0.00</span>
            </div>
        </div>
    </div>

    <div class="col-6 col-sm-4 col-md-4">
        <div class="info-box">
            <span class="info-box-icon bg-gradient-warning elevation-1"><i class="fas fa-chart-line"></i></span>
            <div class="info-box-content">
                <div class="move">
                    <div class="info-header">Total Sales (This Month)</div>
                </div>
                <span id="totalSalesMonth" class="info-box-number text-right h4">₱0.00</span>
            </div>
        </div>
    </div>

    <div class="col-6 col-sm-4 col-md-4">
        <div class="info-box">
            <span class="info-box-icon bg-gradient-warning elevation-1"><i class="fas fa-list"></i></span>
            <div class="info-box-content">
                <div class="move">
                    <div class="info-header">Total Pending Orders</div>
                </div>
                <span id="totalPendingOrdersFarm" class="info-box-number text-right h4">0</span>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animate values for each info box
        animateValue('totalFarms', 0, <?= format_num($total_farms) ?>, 2000);
        animateValue('totalCrops', 0, <?= format_num($total_crops) ?>, 2000);
        animateValue('totalPestDisease', 0, <?= format_num($total_pest_disease) ?>, 2000);
        animateValue('totalHarvest', 0, <?= format_num(0) ?>, 2000); // Replace with actual value
        animateValue('totalSalesMonth', 0, <?= format_num(0) ?>, 2000); // Replace with actual value
        animateValue('totalPendingOrdersFarm', 0, <?= format_num(0) ?>, 2000); // Replace with actual value
        // Add more animateValue calls for additional info boxes as needed
    });

    function animateValue(id, start, end, duration) {
        let range = end - start;
        let current = start;
        let increment = end > start ? 1 : -1;
        let stepTime = Math.abs(Math.floor(duration / range));
        let obj = document.getElementById(id);

        if (Math.abs(end - start) < 1) {
            increment = end > start ? 0.01 : -0.01;
            stepTime = duration / (Math.abs(end - start) * 100);
        }

        let timer = setInterval(function() {
            current += increment;
            obj.innerHTML = current.toFixed(2); // Adjust toFixed value as needed
            if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
                clearInterval(timer);
                obj.innerHTML = end.toFixed(2); // Ensure the final value is correct
            }
        }, stepTime);
    }
</script>


  <style>
    /* Set map container size */
    #map {
      height: 250px;
      width: 100%;
    }
  </style>
  <div class="sidebar-container">

    <!-- Icon Sidebar (Initially Hidden on Larger Screens) -->
    <div class="sidebar-container">
    <div class="icon-sidebar">
    <button class="icon-item" data-target="map"><i class="fas fa-map"></i></button>
    <button class="icon-item" data-target="schedule"><i class="fas fa-calendar-alt"></i></button>
    <button class="icon-item" data-target="graphs"><i class="fas fa-chart-pie"></i></button>
    <button class="icon-item" data-target="charts"><i class="fas fa-chart-bar"></i></button>
    <button class="icon-item" data-target="line-chart"><i class="fas fa-chart-line"></i></button>
</div>

  <div class="main-content">


  <script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCPgOaKhjwvksUVP6qBQpjdq3bTQa57NuQ&callback=initMap"
    async
  ></script> 
  <script>
  // Initialize the map
function initMap() {
  // Create a map centered at a default location
  const map = new google.maps.Map(document.getElementById("map"), {
    zoom: 14, // Adjust the zoom level as needed
  });

  // Custom marker icon
  const customMarker = '../uploads/marker100.png';

  // Variable to store the center coordinates
  let centerCoords;

  // Array to store farm locations and associated crops with information fetched from PHP
  const farmData = [
    <?php
      // Your PHP code to fetch farm locations and associated crops with information based on the SQL query
      $user_id = $_settings->userdata('id');
      $sql = "SELECT c.Name as CropName, c.Type, c.PlannedPlantingDate, c.DatePlanted, c.SizeOfPlantation, c.Description, c.Picture1, f.Name as FarmName, f.Latitude as FarmLat, f.Longitude as FarmLng
              FROM crop c
              INNER JOIN farm f ON c.FarmId = f.Id
              INNER JOIN vendor_list v ON c.VendorId = v.id
              WHERE v.user_id = '{$user_id}' AND v.delete_flag = 0 
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
              "PlannedPlantingDate" => $row["PlannedPlantingDate"],
              "DatePlanted" => $row["DatePlanted"],
              "SizeOfPlantation" => $row["SizeOfPlantation"],
              "Description" => $row["Description"],
              "Picture1" => $row["Picture1"]
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

    // Set the center coordinates to the first farm marker
    if (!centerCoords) {
      centerCoords = { lat: parseFloat(farm.crops[0].lat), lng: parseFloat(farm.crops[0].lng) };
    }
  });

  // Set the center of the map to the first farm marker
  if (centerCoords) {
    map.setCenter(centerCoords);
  }
}

// Function to generate info window content for farms and associated crops
function generateInfoWindowContent(farm) {
  let content = `<strong>${farm.farmName}</strong><br>`;
  farm.crops.forEach((crop) => {
    content += `
      <div class="card mt-3">
        <div class="card-body">
          <div class="row">
            <div class="col-md-4">
              <img src="../${crop.cropDetails.Picture1}" alt="Crop Image" width="100">
            </div>
            <div class="col-md-8">
              <strong>${crop.cropName}</strong><br>
              <ul class="list-unstyled">
                <li><strong title="Type">Type:</strong> ${crop.cropDetails.Type}</li>
                <li><strong title="Planned Planting Date">Planned Planting Date:</strong> ${crop.cropDetails.PlannedPlantingDate}</li>
                <li><strong title="Date Planted">Date Planted:</strong> ${crop.cropDetails.DatePlanted}</li>
                <li><strong title="Size of Plantation">Size of Plantation:</strong> ${crop.cropDetails.SizeOfPlantation}</li>
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

  </script>

<style>
    .farm_card {
        width: 250px;
        height: 280px; /* Increased height to accommodate the button */
        border-radius: 10px;
        border: solid 1px #ccc;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: transform 0.3s, box-shadow 0.3s;
        background-color: white;
        text-align: left;
        position: relative;
    }
    .farm_card:hover {
        transform: translateY(-10px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }
    .card_img_container {
        width: 100%;
        height: 170px;
    }
    .farm_image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .farm_list {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-start;
        gap: 20px;
    }
    .crop_name {
        height: 50px;
        overflow-y: auto;
    }
    .add_farm_card {
        width: 250px;
        height: 270px;
        border-radius: 10px;
        border: dashed 2px #ccc;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: transform 0.3s, box-shadow 0.3s;
        background-color: white;
        cursor: pointer;
        text-align: left;
    }
    .add_farm_card:hover {
        transform: translateY(-10px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }
    .sidebar-container {
        display: flex;
        gap: 20px;
    }
    .sidebar {
        width: 410px; /* Adjust width as needed */
        position: sticky;
        top: 20px; /* Adjust top spacing */
        height: calc(100vh - 40px); /* Adjust height to fit screen */

    }
    .main-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    /* Sidebar Container */
.sidebar-container {
    display: flex;
    position: relative;
    gap: 20px;
}

/* Sidebar Toggle Button */
.sidebar-toggle {
    display: none; /* Initially hidden on larger screens */
    cursor: pointer;
}

/* Icon Sidebar */
.icon-sidebar {
    display: none; /* Initially hidden on larger screens */
    flex-direction: column;
    gap: 10px;
    position: fixed;
    top: 20px;
    right: 20px; /* Position on the right side */
    background-color: #fff;
    padding: 10px;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    z-index: 1000; /* Ensure it's above other content */
}
    @media (max-width: 768px) {
        .sidebar-container {
            flex-direction: column;
            align-items: flex-start;
        }
        .sidebar-toggle {
            display: block;
        }
        .icon-sidebar {
            display: flex;
        }
        .main-content {
            margin-top: 20px;
        }
        .farm_card {
            width: 100%;
        }
        .add_farm_card{
            width: 100%;
        }
    }
    .dropleft {
        top: 0;
        right: 0;
        position: absolute;
    }
    .enter_button{
        background-color: aqua;
    }
    #create_new{
        height: 100%;
    }
</style>

<div class="farm_list">
    <?php
    $query = "
        SELECT 
            farm.Id as farm_id, 
            farm.Name as farm_name, 
            farm.Image as farm_image, 
            crop.Id as crop_id, 
            crop.Name as crop_name 
        FROM 
            farm 
        LEFT JOIN 
            crop 
        ON 
            farm.Id = crop.FarmId 
        WHERE 
            farm.VendorListId = '{$_settings->userdata('id')}' AND farm.delete_flag = 0
        ORDER BY 
            farm.Id, crop.Name
    ";
    $result = $conn->query($query);

    $farms = [];
    while($row = $result->fetch_assoc()) {
        $farms[$row['farm_id']]['farm_name'] = $row['farm_name'];
        $farms[$row['farm_id']]['farm_image'] = $row['farm_image'];
        if (!empty($row['crop_id'])) {
            $farms[$row['farm_id']]['crops'][] = $row['crop_name'];
        }
    }

    foreach($farms as $farm_id => $farm) {
    ?>
        <div class="farm_card">
            <div class="dropdown dropleft">
                <button class="btn btn-link dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item edit_farm" href="#" data-id="<?= $farm_id ?>">Edit Crop</a>
                <a class="dropdown-item delete_farm" href="#" data-id="<?= $farm_id ?>">Delete Crop</a>
                </div>
            </div>
            <div class="card_img_container">
                <img class="farm_image" src="../<?= htmlspecialchars($farm['farm_image']) ?>" alt="<?= htmlspecialchars($farm['farm_name']) ?>">
            </div>
            <div style="padding: 4px;">
                <h6><?= htmlspecialchars($farm['farm_name']) ?></h6>
                <div class="crop_name">
                    <span class="card-text"><strong>Crops:</strong></span>
                    <span>
                        <?php if (!empty($farm['crops'])) {
                            foreach($farm['crops'] as $crop_name) {
                                echo "<span>" . htmlspecialchars($crop_name) . ",&nbsp;</span>";
                            }
                        } else {
                            echo "<span>No crops available</span>";
                        } ?>
                    </span>
                </div>
            </div>
            <button class="enter_button" data-farm-id="<?= $farm_id ?>">Enter</button>
        </div>
    <?php
    }
    ?>
    <div class="add_farm_card">
        <button id="create_new">Add Farm</button>
    </div>
</div>

    <div class="sidebar">
    <div id="map" class="sidebar">
        Map
    </div>
    <h2 id="schedule-section" class="text-center mt-4 mb-4">Crop Planting Schedule</h2>
    <div id="schedule"  class="card crop-schedule-card" class="section">
    <div class="card-header">
        <h3>Crop Planting Schedule</h3>
        <!-- Filter buttons -->
        <div class="text-right mt-n4">
            <button class="btn btn-sm btn-outline-primary filter-btn" data-filter="all" title="All Crops"><i class="fas fa-globe"></i></button>
            <button class="btn btn-sm btn-outline-success filter-btn" data-filter="Rice" title="Rice"><i class="fas fa-seedling"></i></button>
            <button class="btn btn-sm btn-outline-warning filter-btn" data-filter="Corn" title="Corn"><i class="fas fa-bee"></i></button>
            <button class="btn btn-sm btn-outline-info filter-btn" data-filter="Sweet Potato" title="Sweet Potato"><i class="fas fa-carrot"></i></button>
            <button class="btn btn-sm btn-outline-danger filter-btn" data-filter="Vegetable" title="Vegetable"><i class="fas fa-leaf"></i></button>
        </div>
    </div>
    <div class="card-body scrollable-container" style="height: 400px; overflow-y: auto;">
        <?php
        // Assuming $conn is your database connection object

        // Initialize filter variable
        $filter = '';

        // Check if filter is set
        if (isset($_GET['filter'])) {
            $filter = $conn->real_escape_string($_GET['filter']);
        }

        // Construct SQL query
        $query = "
            SELECT c.*, f.Name as farm_name, v.shop_name as vendor_name, v.contact
            FROM crop c
            LEFT JOIN farm f ON c.FarmId = f.Id
            LEFT JOIN vendor_list v ON c.VendorId = v.id
            WHERE c.DatePlanted IS NULL AND c.is_deleted = 0";

        if (!empty($filter) && $filter !== 'all') {
            $query .= " AND c.Type = '$filter'";
        }

        // Execute query
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Display each crop's details
                ?>
          <style>
    .farm_sched_card {
        width: 100%;
        padding: 9px;
        height: auto;
        border: 1px solid grey;
        border-radius: 10px;
        margin-bottom: 4px;
        box-sizing: border-box;
        background: linear-gradient(to bottom, #f0f0f0, #ffffff); /* Vertical gradient */
        box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .farm_sched_card:hover {
        transform: translateY(-3px);
        box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.2);
    }
    .sched_card_row1,
    .sched_card_row2 {
        display: flex;
        justify-content: space-between;
    }
    .sched_card_row1 h3,
    .sched_card_row2 p {
        margin: 0;
        padding: 0;
    }
    .overflow,
    .overflow1 {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .overflow {
        width: 150px; /* Adjust the width as needed */
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .overflow1 {
        width: 200px; /* Adjust the width as needed */
        text-align: left;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
</style>

<button class="farm_sched_card">
    <div class="sched_card_row1">
        <div class="overflow1">
            <h3><?= htmlspecialchars($row['farm_name']) ?></h3>
        </div>
        <div>
            <h3><?= htmlspecialchars($row['PlannedPlantingDate']) ?></h3>
        </div>
    </div>
    <div class="sched_card_row2">
        <div class="overflow1">
            <p>Crop: <?= htmlspecialchars($row['Name']) ?> - <?= htmlspecialchars($row['Type']) ?></p>
        </div>
        <div class="overflow">
            <p>Farmer: <?= htmlspecialchars($row['vendor_name']) ?></p>
        </div>
    </div>
</button>



                <?php
            }
        } else {
            // If no crops match the initial criteria
            echo "No farm found.";
        }
        ?>
     
    </div>
</div>

<script>
     $(function(){
        $('.delete_farm').click(function(){
            _conf("Are you sure you want to delete this farm?", 'delete_farm', [$(this).attr('data-id')]);
        });
    });

    function delete_farm(id) {
        start_loader();
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=delete_farm",
            method: "POST",
            data: {id: id},
            dataType: "json",
            error: function(err) {
                console.log(err);
                alert_toast("An error occurred in deleting crop.", 'error');
                end_loader();
            },
            success: function(resp) {
                if (typeof resp === 'object' && resp.status === 'success') {
                    location.reload();
                } else {
                    alert_toast("An error occurred 2333333.", 'error');
                    end_loader();
                }
            }
        });
    }
        $('.edit_farm').click(function(){
        uni_modal('Update Farm',"crops/edit_farm.php?id="+$(this).attr('data-id'),'large')
    });
    // JavaScript to handle filter button clicks
    document.addEventListener('DOMContentLoaded', function () {
        const filterButtons = document.querySelectorAll('.filter-btn');
        
        filterButtons.forEach(button => {
            button.addEventListener('click', function () {
                const filterValue = this.getAttribute('data-filter');
                filterCrops(filterValue);
            });
        });

        function filterCrops(filter) {
            const cropItems = document.querySelectorAll('.crop-item');

            cropItems.forEach(item => {
                if (filter === 'all' || item.classList.contains(filter)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });

            const noCropsFoundMessage = document.getElementById('no-crops-found');
            const visibleCrops = document.querySelectorAll('.crop-item[style="display: block;"]');

            if (visibleCrops.length === 0) {
                noCropsFoundMessage.style.display = 'block';
            } else {
                noCropsFoundMessage.style.display = 'none';
            }
        }
    });
</script>


<?php

$crop_status_query = "
    SELECT 
        CASE 
            WHEN Status = 'Alive' THEN 'Alive'
            WHEN Status = 'Diseased' THEN 'Diseased'
            WHEN Status = 'End of Lifespan' THEN 'End of Lifespan'
            ELSE 'Unproductive' 
        END AS status,
        COUNT(*) AS count
    FROM crop
    WHERE VendorId = '{$user_id}' AND is_deleted = 0
    GROUP BY status
";
$result_crop_status = $conn->query($crop_status_query);

$crop_status_data = [];
while ($row = $result_crop_status->fetch_assoc()) {
    $crop_status_data[$row['status']] = $row['count'];
}

// Query to fetch crop types distribution data
$crop_types_query = "
    SELECT 
        Type,
        COUNT(*) AS count
    FROM crop
    WHERE VendorId = '{$user_id}' AND is_deleted = 0
    GROUP BY Type
";
$result_crop_types = $conn->query($crop_types_query);

$crop_types_labels = [];
$crop_types_counts = [];
while ($row = $result_crop_types->fetch_assoc()) {
    $crop_types_labels[] = $row['Type'];
    $crop_types_counts[] = $row['count'];
}

// Query to fetch crop growth over time data
$crop_growth_query = "
    SELECT 
        PlannedPlantingDate,
        COUNT(*) AS planned_count,
        SUM(CASE WHEN DatePlanted IS NOT NULL THEN 1 ELSE 0 END) AS planted_count
    FROM crop
    WHERE VendorId = '{$user_id}' AND PlannedPlantingDate IS NOT NULL AND is_deleted = 0
    GROUP BY PlannedPlantingDate
    ORDER BY PlannedPlantingDate
";
$result_crop_growth = $conn->query($crop_growth_query);

$crop_growth_labels = [];
$crop_planned_counts = [];
$crop_planted_counts = [];
while ($row = $result_crop_growth->fetch_assoc()) {
    $crop_growth_labels[] = $row['PlannedPlantingDate'];
    $crop_planned_counts[] = $row['planned_count'];
    $crop_planted_counts[] = $row['planted_count'];
}
?>
<div id="graphs" class="card">
 <div class="card mt-4">
        <div class="card-header">
            <h3>Crop Status Distribution</h3>
        </div>
        <div class="card-body">
            <canvas id="cropStatusChart" width="400" height="300"></canvas>
        </div>
    </div>
    </div>
    <!-- Bar Chart for Crop Types -->
    <div id="charts" class="card">
    <div class="card mt-4">
        <div class="card-header">
            <h3>Crop Types Distribution</h3>
        </div>
        <div class="card-body">
            <canvas id="cropTypeChart" width="400" height="300"></canvas>
        </div>
    </div>
    </div>
    <!-- Line Chart for Crop Growth Over Time -->
    <div class="card mt-4">
    <div id="line-chart" class="card">
        <div class="card-header">
            <h3>Crop Growth Over Time</h3>
        </div>
        <div class="card-body">
            <canvas id="cropGrowthChart" width="400" height="300"></canvas>
        </div>
    </div>
</div>
</div>
<script>
    // JavaScript for rendering charts using Chart.js
    document.addEventListener('DOMContentLoaded', function () {
        // Pie Chart for Crop Status
        var cropStatusData = {
            labels: <?= json_encode(array_keys($crop_status_data)) ?>,
            datasets: [{
                label: 'Crop Status',
                data: <?= json_encode(array_values($crop_status_data)) ?>,
                backgroundColor: ['#28a745', '#dc3545', '#ffc107', '#6c757d']
            }]
        };

        var cropStatusChart = new Chart(document.getElementById('cropStatusChart'), {
            type: 'pie',
            data: cropStatusData
        });

        // Bar Chart for Crop Types
        var cropTypeData = {
            labels: <?= json_encode($crop_types_labels) ?>,
            datasets: [{
                label: 'Number of Crops',
                data: <?= json_encode($crop_types_counts) ?>,
                backgroundColor: '#007bff' // Blue color for bars
            }]
        };

        var cropTypeChart = new Chart(document.getElementById('cropTypeChart'), {
            type: 'bar',
            data: cropTypeData,
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Line Chart for Crop Growth Over Time
        var cropGrowthData = {
            labels: <?= json_encode($crop_growth_labels) ?>,
            datasets: [{
                label: 'Planned Planting',
                data: <?= json_encode($crop_planned_counts) ?>,
                borderColor: '#17a2b8', // Blue color for line
                fill: false
            }, {
                label: 'Date Planted',
                data: <?= json_encode($crop_planted_counts) ?>,
                borderColor: '#28a745', // Green color for line
                fill: false
            }]
        };

        var cropGrowthChart = new Chart(document.getElementById('cropGrowthChart'), {
            type: 'line',
            data: cropGrowthData,
            options: {
                scales: {
                    x: {
                        type: 'time',
                        time: {
                            unit: 'day'
                        }
                    },
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>
    </div>
    </div>
<script>
  document.querySelectorAll('.enter_button').forEach(button => {
        button.addEventListener('click', function() {
            const farmId = this.getAttribute('data-farm-id');
            window.location.href = '<?php echo base_url ?>vendor/?page=crops/farm_details&id=' + farmId;
        });
    });
</script>
<script>
  	$(document).ready(function(){
		$('#create_new').click(function(){
			uni_modal('Add New Product',"crops/add_farm.php",'large')
    })
    })

</script>
<script>
  // Smooth scroll to different sections of the page
document.addEventListener('DOMContentLoaded', function () {
    const iconButtons = document.querySelectorAll('.icon-item');

    iconButtons.forEach(button => {
        button.addEventListener('click', function () {
            const target = this.getAttribute('data-target');
            const element = document.getElementById(target);

            if (element) {
                element.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
});

</script>
