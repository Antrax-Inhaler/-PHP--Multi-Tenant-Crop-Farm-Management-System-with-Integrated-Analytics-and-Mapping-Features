<?php
require_once('./../../config.php');

if (isset($_GET['farm_id']) && isset($_GET['planting_date'])) {
    $farmId = $_GET['farm_id'];
    $plantingDate = $_GET['planting_date'];

    // Query to fetch weather data from the crop's planting date to the current date
    $sqlWeather = "SELECT Temperature, Humidity, WindSpeed, RecordedAt 
                   FROM weather 
                   WHERE FarmId = $farmId 
                   AND RecordedAt >= '$plantingDate'
                   AND RecordedAt <= CURDATE()
                   ORDER BY RecordedAt ASC"; // Ascending order to get data from planting date to today

    $resultWeather = $conn->query($sqlWeather);

    $weatherData = [];
    if ($resultWeather->num_rows > 0) {
        while ($row = $resultWeather->fetch_assoc()) {
            $weatherData[] = $row; // Collect all weather records
        }
    }

    echo json_encode($weatherData);
}
?>
