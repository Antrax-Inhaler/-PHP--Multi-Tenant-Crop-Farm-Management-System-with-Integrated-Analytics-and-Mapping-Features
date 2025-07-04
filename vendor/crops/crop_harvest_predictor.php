<?php
// Fetch crop details based on crop ID
$crop_id = $_GET['id'] ?? 1;

if (!$crop_id) {
    die("Crop ID is required.");
}

// Fetch crop details, replacing Location with Latitude and Longitude
$crop_query = "
    SELECT 
        crop.Id as cropId,
        crop.Name as crop_name, 
        crop.Type as crop_type,
        crop.PlannedPlantingDate as planned_planting_date,
        crop.DatePlanted as date_planted,
        crop.SizeofPlantation as size_of_plantation,
        crop.Description as crop_description,
        crop.Picture1 as crop_image1,
        crop.Picture2 as crop_image2,
        crop.Picture3 as crop_image3,
        crop.Latitude as latitude,
        crop.Longitude as longitude
    FROM crop 
    WHERE crop.Id = $crop_id AND crop.is_deleted = 0
";

$crop_result = $conn->query($crop_query);
$crop = $crop_result->fetch_assoc();

if (!$crop) {
    die("Crop not found.");
}

// Combine Latitude and Longitude into a location string
$crop_location = $crop['latitude'] . ', ' . $crop['longitude'];

// Fetch pest and disease details for the crop
$pest_query = "
    SELECT
        Name, 
        SizeOfAreaAffected, 
        Status
    FROM croppestdisease 
    WHERE CropID = $crop_id
";
$pest_result = $conn->query($pest_query);
$pests = [];
while ($row = $pest_result->fetch_assoc()) {
    $pests[] = $row;
}

// Fetch weather data since planting date
$weather_query = "
    SELECT 
        Temperature, 
        MinTemperature, 
        MaxTemperature, 
        Humidity, 
        RainVolume, 
        RecordedAt 
    FROM weather 
    WHERE FarmId = (SELECT FarmId FROM crop WHERE Id = $crop_id) 
    AND RecordedAt >= '{$crop['date_planted']}'
    ORDER BY RecordedAt ASC
";

$weather_result = $conn->query($weather_query);
$weather_data = [];
while ($row = $weather_result->fetch_assoc()) {
    $weather_data[] = $row;
}

// Fetch all crop activities
$activity_query = "
    SELECT 
        activity_date, 
        activity_type, 
        description 
    FROM crop_activity 
    WHERE crop_id = $crop_id
    ORDER BY activity_date ASC
";

$activity_result = $conn->query($activity_query);
$activities = [];
while ($row = $activity_result->fetch_assoc()) {
    $activities[] = $row;
}

?>

<!-- Button to trigger the modal -->
<button id="generateMessageBtn">Generate Message</button>

<!-- Modal HTML -->
<div id="aiResponseModal" style="display:none;">
    <div class="modal-content">
        <span id="closeModal">&times;</span>
        <h2>AI Response</h2>
        <p id="loadingMessage">Loading, please wait...</p> <!-- Initial loading message -->
        <p id="aiResponseText" style="display:none;"></p> <!-- Hidden response text -->
    </div>
</div>

<!-- JavaScript -->
<script>
// Sample data from PHP
const data = {
    cropName: "<?php echo $crop['crop_name']; ?>",
    cropType: "<?php echo $crop['crop_type']; ?>",
    plantingDate: "<?php echo $crop['date_planted']; ?>",
    cropAge: Math.floor((new Date() - new Date("<?php echo $crop['date_planted']; ?>")) / (1000 * 60 * 60 * 24)),
    sizeOfPlantation: "<?php echo $crop['size_of_plantation']; ?>",
    latitude: "<?php echo $crop['latitude']; ?>",
    longitude: "<?php echo $crop['longitude']; ?>"
};

const weather_data = <?php echo json_encode($weather_data); ?>;
const activities = <?php echo json_encode($activities); ?>;
const pests = <?php echo json_encode($pests); ?>;

let weatherDetails = weather_data.map(weather => 
    `On ${weather.RecordedAt}, the temperature was ${weather.Temperature}°C, 
    humidity ${weather.Humidity}%, and rainfall ${weather.RainVolume}mm`
).join(", ");

let activityDetails = activities.map(activity => 
    `${activity.activity_type} was performed on ${activity.activity_date}: ${activity.description}`
).join(", ");

let pestDetails = pests.length > 0 ? pests.map(pest => 
    `Pest/Disease '${pest.Name}' has affected ${pest.SizeOfAreaAffected} hectares and is currently '${pest.Status}'`
).join(", ") : "No pests or diseases have been reported for this crop.";

let generatedMessage = `I am currently managing a crop of ${data.cropName} (${data.cropType}), 
    which was planted on ${data.plantingDate}. 
    The crop is currently ${data.cropAge} days old, covering an area of ${data.sizeOfPlantation} hectares, 
    located at ${data.latitude}, ${data.longitude} (Philippines). 
    Here is the full weather history from the day it was planted: ${weatherDetails}.
    Additionally, the following activities have been performed on the crop: ${activityDetails}.
    Pests or diseases that have affected the crop: ${pestDetails}.
    Based on this information, can you help me predict the possible yield for this crop?. Show the predicted yeild in kg only dont make any description or explantaion. Mentioning that I provided you a data is not necessary. 
    Please provide a response that’s easy to understand for Filipino farmers. Salamat!`;

document.getElementById("generateMessageBtn").addEventListener("click", function() {
    // Show modal immediately with loading message
    document.getElementById("aiResponseModal").style.display = "block";
    document.getElementById("loadingMessage").style.display = "block";
    document.getElementById("aiResponseText").style.display = "none";
    
    // AJAX request to get the AI response
    $.ajax({
        url: 'crops/ai.php',
        method: 'POST',
        dataType: 'json',
        data: { message: generatedMessage },
        success: function(response) {
            let content = response.content
                .replace(/\*\*(.*?)\*\*/g, '<b>$1</b>') // Handle bold text
                .replace(/(?<!\d)(\d+)\.\s/g, '<br>$1. ') // Add line break before numbered steps
                .replace(/\n/g, '<br>'); // Convert newlines to <br>
            
            // Show the AI response in the modal
            document.getElementById("loadingMessage").style.display = "none"; // Hide loading message
            document.getElementById("aiResponseText").style.display = "block"; // Show response text
            document.getElementById("aiResponseText").innerHTML = content;
        },
        error: function() {
            // Hide loading and show an error message
            document.getElementById("loadingMessage").style.display = "none";
            document.getElementById("aiResponseText").style.display = "block";
            document.getElementById("aiResponseText").innerHTML = "An error occurred while processing your request.";
        }
    });
});

document.getElementById("closeModal").addEventListener("click", function() {
    document.getElementById("aiResponseModal").style.display = "none";
});
</script>

<!-- Modal CSS -->
<style>
#aiResponseModal {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: #fff;
    padding: 20px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
}

#closeModal {
    cursor: pointer;
    float: right;
    font-size: 20px;
}
</style>
