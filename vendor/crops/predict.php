
<!-- Crop Selection Dropdown -->
<select id="cropSelector">
    <option value="">Select a Crop</option>
</select>

<!-- Soil Quality Rating Section -->
<h2>Rate the Soil Quality (1-20)</h2>
<div id="soilQualitySection">
    <label for="soilQuality">Soil Quality Rating:</label>
    <select id="soilQuality">
        <option value="">Select Soil Quality Rating</option>
        <option value="1">1 - Severely compacted, lacking nutrients, extreme pH imbalance (acidic or alkaline), poor drainage, no organic matter, not suitable for cultivation</option>
        <option value="2">2 - Very compacted, low nutrient content, unbalanced pH, slow drainage, minimal organic matter, major issues with plant growth</option>
        <option value="3">3 - Compacted, low fertility, unbalanced pH, poor moisture retention, difficult to cultivate most crops</option>
        <option value="4">4 - Slightly compacted, low fertility, slightly unbalanced pH, poor drainage, minimal organic content</option>
        <option value="5">5 - Limited fertility, moderate compaction, below average drainage, some organic content, slight issues with pH</option>
        <option value="6">6 - Average fertility, slightly compacted, adequate drainage but low moisture retention, some pH imbalance</option>
        <option value="7">7 - Moderately fertile, minor compaction, decent drainage, moderate organic content, minor pH issues</option>
        <option value="8">8 - Reasonable fertility, some compaction, adequate structure and drainage, fair organic content, manageable pH</option>
        <option value="9">9 - Fertile, minor compaction, good drainage, moderate organic content, pH nearly balanced</option>
        <option value="10">10 - Fertile with good structure, well-drained, moderate organic content, pH slightly off but manageable for most crops</option>
        <option value="11">11 - Fertile, well-structured, good water retention, well-drained, organic content present, pH nearly ideal for crops</option>
        <option value="12">12 - Very fertile, well-drained, rich organic content, good water retention, minor pH imbalance</option>
        <option value="13">13 - Highly fertile, excellent structure and drainage, high organic content, ideal pH balance</option>
        <option value="14">14 - Very high fertility, optimal moisture retention, excellent structure, ideal pH, rich in organic matter</option>
        <option value="15">15 - Extremely fertile, balanced moisture retention, great structure, ideal pH for a wide range of crops, high organic matter</option>
        <option value="16">16 - Premium quality, optimal water retention, ideal pH balance, very high fertility, well-structured, high organic content</option>
        <option value="17">17 - Exceptional fertility, perfect drainage and structure, high organic content, ideal for most crops, balanced pH</option>
        <option value="18">18 - Outstanding fertility, superior drainage, optimal water retention, high organic matter, perfect pH balance</option>
        <option value="19">19 - Near perfect soil, high fertility, excellent water retention, perfect structure and pH balance, high organic content</option>
        <option value="20">20 - Ideal soil conditions: perfectly balanced pH, excellent water retention, structure, and drainage, highly fertile, rich in organic matter</option>

    </select>
</div>

<!-- Weather Data Section -->
<h2>Weather Data</h2>
<div id="weatherData">
    <!-- This section will display weather data for the selected crop -->
</div>

<!-- Button to Generate a Prediction Message -->
<button id="generateMessageBtn">Generate Prediction Message</button>

<!-- Input for Generated Message (Hidden) -->
<input type="hidden" id="messageInput">

<!-- Button to Submit Message to AI -->
<button id="submitBtn">Ask AI for Prediction</button>

<!-- Response Area -->
<div id="response"></div>

<script>
// Function to populate crop selector with crops from get_crop.php
function loadCrops() {
    $.ajax({
        url: 'crops/get_crop.php',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            let cropSelector = $('#cropSelector');
            cropSelector.empty();
            cropSelector.append('<option value="">Select a Crop</option>');

            data.crops.forEach(crop => {
                cropSelector.append(<option value="${crop.Id}" data-name="${crop.Name}" data-type="${crop.Type}" data-planting-date="${crop.DatePlanted}" data-size="${crop.SizeOfPlantation}" data-weather='${JSON.stringify(crop.weather)}'>${crop.Name}</option>);
            });
        }
    });
}

// Display weather data when a crop is selected
$('#cropSelector').on('change', function() {
    let selectedWeather = $(this).find(':selected').data('weather');
    let weatherData = $('#weatherData');
    weatherData.empty(); // Clear previous weather data

    if (selectedWeather.length > 0) {
        selectedWeather.forEach(weather => {
            weatherData.append(
                <div>
                    <strong>Recorded At:</strong> ${weather.RecordedAt}<br>
                    <strong>Temperature:</strong> ${weather.Temperature} °C (Feels Like: ${weather.FeelsLikeTemperature} °C)<br>
                    <strong>Min Temp:</strong> ${weather.MinTemperature} °C, <strong>Max Temp:</strong> ${weather.MaxTemperature} °C<br>
                    <strong>Humidity:</strong> ${weather.Humidity} %<br>
                    <strong>Rain Volume:</strong> ${weather.RainVolume} mm<br>
                    <strong>Cloudiness:</strong> ${weather.Cloudiness} %<br>
                    <strong>Wind Speed:</strong> ${weather.WindSpeed} m/s<br>
                    <strong>Description:</strong> ${weather.WeatherDescription}<br><br>
                </div>
            );
        });
    } else {
        weatherData.append('<p>No weather data available for this crop.</p>');
    }
});

// Handle Generate Message button click
$('#generateMessageBtn').on('click', function() {
    let selectedCrop = $('#cropSelector').find(':selected');
    if (!selectedCrop.val()) {
        alert('Please select a crop first.');
        return;
    }

    let cropName = selectedCrop.data('name');
    let cropType = selectedCrop.data('type');
    let plantingDate = selectedCrop.data('planting-date');
    let sizeOfPlantation = selectedCrop.data('size');

    let soilQuality = $('#soilQuality').val();
    if (!soilQuality) {
        alert('Please select a soil quality rating.');
        return;
    }

    let soilQualityText = $('#soilQuality option:selected').text();
    let selectedWeather = selectedCrop.data('weather');
    
    // Create weather summary
    let weatherSummary = '';
    selectedWeather.forEach(weather => {
        weatherSummary += 
            Weather on ${weather.RecordedAt}: Temperature ${weather.Temperature} °C, Feels Like ${weather.FeelsLikeTemperature} °C, 
            Humidity ${weather.Humidity} %, Rain Volume ${weather.RainVolume} mm, Cloudiness ${weather.Cloudiness} %, 
            Wind Speed ${weather.WindSpeed} m/s. Description: ${weather.WeatherDescription}.<br>;
    });

    // Generate message for AI
    let message = Predict the yield for ${cropName} (${cropType}). It was planted on ${plantingDate}, and the plantation size is ${sizeOfPlantation} hectares. The soil quality is rated as: ${soilQualityText}. Weather conditions experienced since planting are as follows: ${weatherSummary};

    $('#messageInput').val(message);
    alert('Message generated! Now you can ask the AI for the prediction.');
});

// Handle Ask AI button click
$('#submitBtn').on('click', function() {
    let generatedMessage = $('#messageInput').val();
    if (!generatedMessage) {
        alert('Please generate a message first.');
        return;
    }

    $('#response').html('<div class="loading"><span>Analyzing...</span></div>');

    $.ajax({
        url: 'crops/ai.php',
        method: 'POST',
        dataType: 'json',
        data: { message: generatedMessage },
        success: function(response) {
            var content = response.content;
            content = content.replace(/\*\*(.*?)\*\*/g, '<b>$1</b>');
            content = content.replace(/\*/g, '<br><br>');
            $('#response').html('<h6>' + content + '</h6>');
        },
        error: function(xhr, status, error) {
            $('#response').html('<h6>Oops! Something went wrong.</h6>');
        }
    });
});

$(document).ready(function() {
    loadCrops();
});
</script>
this is the crops/get_crop.php
<?php
require_once('./../../config.php');
$vendorId = $_settings->userdata('id'); // Current vendor ID

// Query to fetch crops associated with the current vendor
$sqlCrops = "
    SELECT 
        c.Id, 
        c.Name, 
        c.Type, 
        c.PlannedPlantingDate, 
        c.DatePlanted, 
        c.SizeOfPlantation, 
        c.Status,
        c.FarmId 
    FROM crop c
    WHERE c.VendorId = $vendorId 
    AND c.delete_flag = 0 
    AND c.is_deleted = 0";

$resultCrops = $conn->query($sqlCrops);

$crops = [];
$defaultCropId = 0;

if ($resultCrops->num_rows > 0) {
    while ($row = $resultCrops->fetch_assoc()) {
        // Fetch weather data for the farm associated with the crop
        $farmId = $row['FarmId'];
        $datePlanted = $row['DatePlanted'];

        $sqlWeather = "
            SELECT 
                Temperature, 
                MinTemperature, 
                MaxTemperature, 
                FeelsLikeTemperature, 
                Humidity, 
                RainVolume, 
                Cloudiness, 
                WindSpeed, 
                WeatherDescription, 
                RecordedAt
            FROM weather 
            WHERE FarmId = $farmId 
            AND RecordedAt BETWEEN '$datePlanted' AND NOW()";

        $resultWeather = $conn->query($sqlWeather);
        $weather = [];
        if ($resultWeather->num_rows > 0) {
            while ($weatherRow = $resultWeather->fetch_assoc()) {
                $weather[] = $weatherRow;
            }
        }

        // Include weather data with crop info
        $row['weather'] = $weather;
        $crops[] = $row;

        if ($defaultCropId == 0) {
            $defaultCropId = $row['Id']; // Set the first crop as the default if no crop is selected
        }
    }
}

echo json_encode(['crops' => $crops, 'defaultCropId' => $defaultCropId]);
?>