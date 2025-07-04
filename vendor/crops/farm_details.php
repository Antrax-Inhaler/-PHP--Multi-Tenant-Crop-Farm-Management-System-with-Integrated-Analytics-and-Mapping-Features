<?php
$farm_id = $_GET['id'] ?? null;

if (!$farm_id) {
    die("Farm ID is required.");
}

// Rest of your code to handle farm details

// Fetch farm details
$farm_query = "
    SELECT 
        farm.Name as farm_name, 
        farm.Latitude as farm_latitude, 
        farm.Longitude as farm_longitude, 
        farm.Size as farm_size,
        farm.Description as farm_description,
        farm.Image as farm_image
    FROM farm 
    WHERE farm.Id = $farm_id
";
$farm_result = $conn->query($farm_query);
$farm = $farm_result->fetch_assoc();

if (!$farm) {
    die("Farm not found.");
}

// Fetch total crops
$crops_query = "
    SELECT COUNT(*) as total_crops 
    FROM crop 
    WHERE FarmId = $farm_id
";
$crops_result = $conn->query($crops_query);
$total_crops = $crops_result->fetch_assoc()['total_crops'];

// Fetch pest and disease count
$pest_query = "
    SELECT COUNT(*) as pest_count 
    FROM crop 
    WHERE FarmId = $farm_id AND PestID IS NOT NULL
";
$pest_result = $conn->query($pest_query);
$pest_count = $pest_result->fetch_assoc()['pest_count'];

// Fetch crops details
$crops_details_query = "
    SELECT 
        crop.Id as crop_id, 
        crop.Name as crop_name, 
        crop.Picture1 as crop_image 
    FROM crop 
    WHERE FarmId = $farm_id AND is_deleted = 0
";
$crops_details_result = $conn->query($crops_details_query);

$crops = [];
while ($row = $crops_details_result->fetch_assoc()) {
    $crops[] = $row;
}
?>

    <style>

        .farm_card, .crop_card {
            width: 250px;
            height: 250px;
            border-radius: 10px;
            border: solid 1px #ccc;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }
        .farm_card:hover, .crop_card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .card_img_container {
            width: 100%;
            height: 180px;
        }
        .farm_image, .crop_image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .crop_name {
            height: 70px;
            overflow-y: auto;
        }
        .crop_list {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-start;
            gap: 20px;
        }
        #map {
            width: 100%;
            height: 300px;
            margin-bottom: 20px;
        }
        .add_crop_card{
            width: 250px;
            height: 250px;
            border-radius: 10px;
            border: dashed 2px #ccc;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }
        .add_crop_card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .sidebar-container {
        display: flex;
        gap: 20px;
    }
    .sidebar {
        width: 500px; /* Adjust width as needed */
        position: sticky;
        top: 20px; /* Adjust top spacing */
        height: calc(100vh - 40px); /* Adjust height to fit screen */
        overflow-y: auto;
        z-index: 1038;

    }
    .main-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    .icon-button {
            display: inline-block;
            padding: 10px;
            border: 3px solid yellow;
            border-radius: 15px;
            text-decoration: none;
            text-align: center;
            height: 50px;
        }
        .icon-button:hover {
            background-color: gold;
            border-color: gold;
        }
        .icon-button yellow  {
            color: yellow;
            width: 50px;
            height: 50px;
        }
        .topper{
            display: flex;
            justify-content: space-between;
            flex-direction: row;
        }
        .tutorial-body {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f4f4f4;
        }
        .tutorial-container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            max-width: 1000px;
            width: 90%;
            margin: 20px;
        }
    </style>
    <div class="" ></div>
    <div class="topper" >
    <h1><?= htmlspecialchars($farm['farm_name']) ?></h1>
    <a href="<?php echo base_url ?>vendor/?page=crops/index2" class="icon-button">
       <i style="color: yellow;" class="fas fa-tractor yellow"></i>
    </a>
    </div>
        <!-- Info Boxes -->
         <div class="row" >
         <div class="col-12 col-sm-4 col-md-4">
    <div class="info-box">
        <span class="info-box-icon <?= $total_crops > 0 ? 'bg-gradient-success' : 'bg-gradient-info' ?> elevation-1"><i class="fas fa-seedling"></i></span>
        <div class="info-box-content">
            <div class="info-header">Total Crops</div>
            <span class="info-box-number text-right h4"><?= htmlspecialchars($total_crops) ?></span>
        </div>
    </div>
</div>

<div class="col-12 col-sm-4 col-md-4">
    <div class="info-box">
        <span class="info-box-icon <?= $pest_count > 0 ? 'bg-gradient-warning' : 'bg-gradient-info' ?> elevation-1"><i class="fas fa-bug"></i></span>
        <div class="info-box-content">
            <div class="info-header">Pest and Disease</div>
            <span class="info-box-number text-right h4"><?= htmlspecialchars($pest_count) ?></span>
        </div>
    </div>
</div>

    <div class="col-12 col-sm-4 col-md-4">
    <style>
.header-temp-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: -4px; /* Adjust the margin bottom as needed */
}

.weather-details-container {
    display: flex;
    justify-content: space-between;
    margin-top: -3px; /* Adjust the margin top as needed */
}
.weather-info{
    cursor: pointer;
}

</style>
        <div id="weather-info" class="weather-info" >
        <div class="info-box">
    <span class="info-box-icon bg-gradient-info elevation-1"><i class="fas fa-weather"></i></span>
    <div class="info-box-content">
        <div class="header-temp-container">
            <div class="info-header h3">Loading...</div>
            <span class="info-box-number text-right h3">&#176;</span>
        </div>
        <div id="weather-info" class="weather-info">
            <div class="weather-details-container">
                <div class="weather-description"></div>
                <div class="temperature">
                    <span class="info-box-number text-right b"></span>
                </div>
            </div>
        </div>
    </div>
</div>
        </div>

</div>
    </div>
    </div>
    <style>
          .sidebar-container {
        display: flex;
        gap: 20px;
    }
    .sidebar {
        width: 500px; /* Adjust width as needed */
        position: sticky;
        top: 20px; /* Adjust top spacing */
        height: calc(100vh - 40px); /* Adjust height to fit screen */
        overflow-y: auto;
        z-index: 1038;

    }
    .main-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    #create_schedule {
    align-items: center;
    background-color: #007bff;
    border: none;
    color: white;
    padding: 10px 20px;
    font-size: 16px;
    border-radius: 5px;
    cursor: pointer;
    text-decoration: none;
    font-weight: bold;
    text-align: center;
  }

  #create_schedule i {
  }

  #create_schedule:hover {
    background-color: #0056b3;
  }
    </style>
    <div class="sidebar-container">
        <div class="main-content" >
   <div id="show"></div>
        <div id="map"></div>

        <!-- Crops -->
        <button id="create_schedule" class="btn btn-primary">
    <i class="fas fa-calendar-alt"></i>  Plan Your Planting Schedule
</button>
<script>
  document.getElementById('create_schedule').addEventListener('click', function() {
    window.location.href = '<?php echo base_url ?>vendor/?page=crops/scheduler';
  });
</script>

        <h2>Crops</h2>
        <div class="crop_list">
    <?php foreach($crops as $crop): ?>
        <div class="crop_card" data-crop-id="<?= $crop['crop_id'] ?>">
            <div class="card_img_container">
                <img class="crop_image" src="../<?= htmlspecialchars($crop['crop_image']) ?>" alt="<?= htmlspecialchars($crop['crop_name']) ?>">
            </div>
            <div style="padding: 4px;">
                <h6><?= htmlspecialchars($crop['crop_name']) ?></h6>
            </div>
        </div>
    <?php endforeach; ?>
    <div class="add_crop_card">
        <button id="create_new">Add Crop</button>
    </div>
</div>
    </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
    <script>
        // Fetch weather information
 // Fetch weather information
 const farmLatitude = <?= htmlspecialchars($farm['farm_latitude']) ?>;
    const farmLongitude = <?= htmlspecialchars($farm['farm_longitude']) ?>;
    const apiKey = "";

    function getWeather() {
        const url = `https://api.openweathermap.org/data/2.5/weather?lat=${farmLatitude}&lon=${farmLongitude}&appid=${apiKey}&units=metric`;
        fetch(url)
            .then(response => response.json())
            .then(data => {
                document.getElementById("weather-info").innerHTML = `
                <style>
.header-temp-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: -4px; /* Adjust the margin bottom as needed */
}

.weather-details-container {
    display: flex;
    justify-content: space-between;
    margin-top: -3px; /* Adjust the margin top as needed */
}


</style>
<div class="info-box">
    <span class="info-box-icon bg-gradient-info elevation-1"><img src="https://openweathermap.org/img/w/${data.weather[0].icon}.png"></span>
    <div class="info-box-content">
        <div class="header-temp-container">
            <div class="info-header h3">${data.name}, ${data.sys.country}</div>
            <span class="info-box-number text-right h3">${data.main.temp} &#176;</span>
        </div>
        <div id="weather-info" class="weather-info">
            <div class="weather-details-container">
                <div class="weather-description">${data.weather[0].main} - ${data.weather[0].description}</div>
                <div class="temperature">
                    <span class="info-box-number text-right b">min ${data.main.temp_min} max ${data.main.temp_max}</span>
                </div>
            </div>
        </div>
    </div>
</div>


                `;
            })
            .catch(() => {
                document.getElementById("weather-info").innerHTML = `<h3 class="error">Weather information not found</h3>`;
            });
    }
   
    // Fetch weather on load
    window.addEventListener("load", getWeather);

        // Initialize map
        function initMap() {
            var farmLocation = {lat: farmLatitude, lng: farmLongitude};
            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 15,
                center: farmLocation
            });
            var marker = new google.maps.Marker({
                position: farmLocation,
                map: map
            });
        }

        // Load Google Maps script
        var script = document.createElement('script');
        script.src = `https://maps.googleapis.com/maps/api/js?key=AIzaSyCPgOaKhjwvksUVP6qBQpjdq3bTQa57NuQ&callback=initMap`;
        script.async = true;
        document.head.appendChild(script);

        // Fetch weather on load
        window.addEventListener("load", getWeather);
    </script>

<script>
    document.querySelectorAll('.crop_card').forEach(card => {
        card.addEventListener('click', function() {
            const cropId = this.getAttribute('data-crop-id');
            window.location.href = '<?php echo base_url ?>vendor/?page=crops/crop_details&id=' + cropId;
        });
    });

    $(document).ready(function(){
    $('#create_new').click(function(){
        // Get the farm ID from the URL
        var farmId = getUrlParameter('id');
        // Open the modal and pass the farm ID as a parameter
        uni_modal('Add New Crop', "crops/manage_crop.php?id=" + farmId, 'large');
    });
    $('#create_schedule').click(function(){
        // Get the farm ID from the URL
        var farmId = getUrlParameter('id');
        // Open the modal and pass the farm ID as a parameter
        uni_modal('Add New Planting Schedule', "crops/manage_sched.php?id=" + farmId, 'large');
    });
});

// Function to extract URL parameters
function getUrlParameter(name) {
    name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
    var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
    var results = regex.exec(location.search);
    return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
};

</script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Select the weather info box
        const weatherInfo = document.getElementById('weather-info');

        // Add click event listener
        if (weatherInfo) {
            weatherInfo.addEventListener('click', () => {
                // Navigate to the desired page
                window.location.href = "<?php echo base_url ?>vendor/?page=crops/weather_calendar";
            });
        }
    });
</script>

<?php
include 'gemini.php';
?>
