<?php
require_once('./../../config.php');

$farmId = isset($_GET['farmId']) ? intval($_GET['farmId']) : 0;

$events = [];

if ($farmId > 0) {
    // Fetch current weather data from the database
    $sqlWeather = "SELECT Date(RecordedAt) as date, WeatherDescription 
                   FROM weather 
                   WHERE FarmId = $farmId";
    $resultWeather = $conn->query($sqlWeather);

    if ($resultWeather->num_rows > 0) {
        while ($row = $resultWeather->fetch_assoc()) {
            $events[] = [
                'title' => $row['WeatherDescription'],
                'start' => $row['date'],
                'weatherDescription' => $row['WeatherDescription'],
                'isFuture' => false
            ];
        }
    }

    // Fetch weather forecast from OpenWeatherMap API for next 5 days
    $sqlFarmLocation = "SELECT Latitude, Longitude FROM farm WHERE Id = $farmId";
    $resultFarmLocation = $conn->query($sqlFarmLocation);

    if ($resultFarmLocation->num_rows > 0) {
        $farmLocation = $resultFarmLocation->fetch_assoc();
        $farmLatitude = $farmLocation['Latitude'];
        $farmLongitude = $farmLocation['Longitude'];

        $apiKey = '2f745fa85d563da5adb87b6cd4b81caf';
        $forecastUrl = "https://api.openweathermap.org/data/2.5/forecast?lat={$farmLatitude}&lon={$farmLongitude}&appid={$apiKey}&units=metric";

        $forecastData = json_decode(file_get_contents($forecastUrl), true);

        foreach ($forecastData['list'] as $forecast) {
            $date = explode(' ', $forecast['dt_txt'])[0];
            $description = $forecast['weather'][0]['description'];

            $events[] = [
                'title' => $description,
                'start' => $date,
                'weatherDescription' => $description,
                'isFuture' => true
            ];
        }
    }
}

echo json_encode($events);
?>
