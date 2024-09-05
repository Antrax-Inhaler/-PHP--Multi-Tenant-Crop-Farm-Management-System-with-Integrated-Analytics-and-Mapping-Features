<?php
require_once('./../../config.php');

$farmId = isset($_GET['farmId']) ? intval($_GET['farmId']) : 0;

$events = [];

if ($farmId > 0) {
    // First, retrieve the farm's latitude and longitude
    $sqlFarm = "SELECT Latitude, Longitude FROM farm WHERE Id = $farmId";
    $resultFarm = $conn->query($sqlFarm);

    if ($resultFarm->num_rows > 0) {
        $farm = $resultFarm->fetch_assoc();
        $latitude = $farm['Latitude'];
        $longitude = $farm['Longitude'];

        // Make the OpenWeatherMap API request
        $apiKey = "2f745fa85d563da5adb87b6cd4b81caf";
        $url = "https://api.openweathermap.org/data/2.5/weather?lat={$latitude}&lon={$longitude}&appid={$apiKey}&units=metric";
        $weatherResponse = file_get_contents($url);
        $weatherData = json_decode($weatherResponse, true);

        if ($weatherData && isset($weatherData['weather'][0]['icon'])) {
            $weatherIcon = $weatherData['weather'][0]['icon']; // OpenWeatherMap icon code

            // Query your database to get historical weather data for the calendar
            $sqlWeather = "SELECT Date(RecordedAt) as date, WeatherDescription, Temperature, MinTemperature, MaxTemperature, RainVolume
                           FROM weather 
                           WHERE FarmId = $farmId";
            $resultWeather = $conn->query($sqlWeather);

            if ($resultWeather->num_rows > 0) {
                while($row = $resultWeather->fetch_assoc()) {
                    $events[] = [
                        'title' => '',
                        'start' => $row['date'],
                        'weatherDescription' => $row['WeatherDescription'],
                        'temp' => $row['Temperature'],
                        'minTemp' => $row['MinTemperature'],
                        'maxTemp' => $row['MaxTemperature'],
                        'rainVolume' => $row['RainVolume'],
                        'icon' => $weatherIcon // Use the icon from the OpenWeatherMap API
                    ];
                }
            }
        }
    }
}

echo json_encode($events);
?>
