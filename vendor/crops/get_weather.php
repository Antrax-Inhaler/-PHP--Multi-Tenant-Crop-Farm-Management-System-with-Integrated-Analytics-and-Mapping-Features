<?php
require_once('./../../config.php');

$farmId = isset($_GET['farmId']) ? intval($_GET['farmId']) : 0;

if ($farmId > 0) {
    // Query to get weather data for the selected farm
    $sqlWeather = "SELECT Id, Temperature, MinTemperature, MaxTemperature, FeelsLikeTemperature, Humidity, RainVolume, Cloudiness, WindSpeed, WeatherDescription, Sunrise, Sunset, RecordedAt 
                   FROM weather 
                   WHERE FarmId = $farmId";
    
    $resultWeather = $conn->query($sqlWeather);
    $weatherData = [];

    if ($resultWeather->num_rows > 0) {
        while ($row = $resultWeather->fetch_assoc()) {
            $recordedAt = date('Y-m-d', strtotime($row['RecordedAt']));

            // Map weather descriptions to your local image file names
            $weatherIcons = [
                'clear sky' => 'clear-sky.gif',
                'scattered clouds' => 'few-clouds.gif',
                'few clouds' => 'few-clouds.gif',

                'broken clouds' => 'scattered-clouds.gif',
                'overcast clouds' => 'broken-clouds.gif',
                'light rain' => 'light-rain.gif',
                'moderate rain' => 'rain.gif',
                'thunderstorm' => 'thunderstorm.gif',
                'snow' => 'snow.png',
                'scattered-clouds' => 'mist.gif',
                // Add more mappings if necessary
            ];

            // Set default icon if weather description is not mapped
            $weatherDescription = strtolower($row['WeatherDescription']);
            $iconFile = isset($weatherIcons[$weatherDescription]) ? $weatherIcons[$weatherDescription] : 'default.png';

            // Assuming your images are stored in /uploads/weather_icons/
            $weatherIconUrl = "../uploads/weather_icons/" . $iconFile;

            $weatherData[] = [
                'id' => $row['Id'],
                'temperature' => $row['Temperature'],
                'minTemp' => $row['MinTemperature'],
                'maxTemp' => $row['MaxTemperature'],
                'feelsLike' => $row['FeelsLikeTemperature'],
                'humidity' => $row['Humidity'],
                'rainVolume' => $row['RainVolume'],
                'cloudiness' => $row['Cloudiness'],
                'windSpeed' => $row['WindSpeed'],
                'weatherDescription' => $row['WeatherDescription'],
                'sunrise' => $row['Sunrise'],
                'sunset' => $row['Sunset'],
                'recordedAt' => $recordedAt,
                'weatherIconUrl' => $weatherIconUrl
            ];
        }
    }

    // Return the weather data as JSON
    echo json_encode($weatherData);
}
?>
