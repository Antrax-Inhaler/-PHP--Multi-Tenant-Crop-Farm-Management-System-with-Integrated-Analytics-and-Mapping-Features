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

        // Fetch crop activities for this crop
        $cropId = $row['Id'];
        $sqlActivities = "
            SELECT 
                activity_date, 
                activity_type, 
                description 
            FROM crop_activity 
            WHERE crop_id = $cropId";
        
        $resultActivities = $conn->query($sqlActivities);
        $activities = [];  // Initialize activities as an empty array
        if ($resultActivities->num_rows > 0) {
            while ($activityRow = $resultActivities->fetch_assoc()) {
                $activities[] = $activityRow;
            }
        }

        // Include weather and activity data with crop info
        $row['weather'] = $weather;
        $row['activities'] = $activities; // Always set activities (empty or with data)
        $crops[] = $row;

        if ($defaultCropId == 0) {
            $defaultCropId = $row['Id']; // Set the first crop as the default if no crop is selected
        }
    }
}

// Output the JSON response
echo json_encode(['crops' => $crops, 'defaultCropId' => $defaultCropId]);
?>
