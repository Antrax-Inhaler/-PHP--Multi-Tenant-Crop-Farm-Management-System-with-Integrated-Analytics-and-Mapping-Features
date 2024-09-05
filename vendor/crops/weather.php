<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather Data Display</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* CSS styles as provided earlier */
        .header-temp-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: -4px;
        }
        
        .weather-details-container {
            display: flex;
            justify-content: space-between;
            margin-top: -3px;
        }

        .info-box {
            display: flex;
            align-items: center;
            background: #f0f0f0;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .info-box-icon {
            font-size: 2em;
            padding: 10px;
            margin-right: 15px;
            color: #fff;
            background-color: #17a2b8;
            border-radius: 50%;
        }

        .info-box-content {
            width: 100%;
        }

        .info-header {
            font-size: 1.5em;
            color: #333;
        }

        .info-box-number {
            font-size: 1.2em;
            color: #666;
        }

        .weather-info {
            margin-top: 10px;
        }

        .weather-description {
            font-size: 1em;
            color: #666;
        }

        .temperature {
            font-size: 1.2em;
            color: #333;
        }
    </style>
</head>
<body>

<div id="weather-info" class="weather-info">
    <div class="info-box">
        <span class="info-box-icon bg-gradient-info elevation-1">
            <i class="fas fa-sun"></i> <!-- Icon for general weather condition -->
        </span>
        <div class="info-box-content">
            <div class="header-temp-container">
                <div class="info-header">Temperature</div>
                <span class="info-box-number" id="current-temperature">&#176;C</span>
            </div>
            <div class="weather-details-container">
                <div class="weather-description">
                    <i class="fas fa-cloud"></i> <span id="cloudiness">Cloudiness: %</span>
                </div>
                <div class="temperature">
                    <i class="fas fa-thermometer-half"></i> <span id="feels-like">Feels Like: &#176;C</span>
                </div>
            </div>
            <div class="weather-details-container">
                <div class="weather-description">
                    <i class="fas fa-tint"></i> <span id="humidity">Humidity: %</span>
                </div>
                <div class="temperature">
                    <i class="fas fa-wind"></i> <span id="wind-speed">Wind Speed: m/s</span>
                </div>
            </div>
            <div class="weather-details-container">
                <div class="weather-description">
                    <i class="fas fa-umbrella"></i> <span id="rain-volume">Rainfall: mm</span>
                </div>
                <div class="temperature">
                    <i class="fas fa-clock"></i> <span id="sunrise">Sunrise: </span>
                </div>
            </div>
            <div class="weather-details-container">
                <div class="weather-description">
                    <i class="fas fa-moon"></i> <span id="sunset">Sunset: </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript to fetch and display weather data -->
<script>
    // Map of weather descriptions to FontAwesome icons
    const weatherIcons = {
        'clear sky': 'fas fa-sun',
        'few clouds': 'fas fa-cloud-sun',
        'scattered clouds': 'fas fa-cloud',
        'broken clouds': 'fas fa-cloud-meatball',
        'shower rain': 'fas fa-cloud-showers-heavy',
        'rain': 'fas fa-cloud-rain',
        'thunderstorm': 'fas fa-poo-storm',
        'snow': 'fas fa-snowflake',
        'mist': 'fas fa-smog'
    };

    // Fetch weather data from the PHP script
    fetch('crops/fetch_weather.php')
        .then(response => response.json())
        .then(data => {
            if (data && Object.keys(data).length > 0) {
                // Update the weather description and icon
                const description = data.WeatherDescription.toLowerCase();
                const iconClass = weatherIcons[description] || 'fas fa-cloud-sun'; // Default icon if description not found
                
                document.getElementById('current-temperature').textContent = data.Temperature + '°C';
                document.getElementById('cloudiness').textContent = 'Cloudiness: ' + data.Cloudiness + '%';
                document.getElementById('feels-like').textContent = 'Feels Like: ' + data.FeelsLikeTemperature + '°C';
                document.getElementById('humidity').textContent = 'Humidity: ' + data.Humidity + '%';
                document.getElementById('wind-speed').textContent = 'Wind Speed: ' + data.WindSpeed + ' m/s';
                document.getElementById('rain-volume').textContent = 'Rainfall: ' + data.RainVolume + ' mm';
                document.getElementById('sunrise').textContent = 'Sunrise: ' + new Date(data.Sunrise).toLocaleTimeString();
                document.getElementById('sunset').textContent = 'Sunset: ' + new Date(data.Sunset).toLocaleTimeString();

                // Update the icon based on weather description
                const iconElement = document.querySelector('.info-box-icon i');
                iconElement.className = `fas ${iconClass}`;
            } else {
                console.log('No weather data available');
            }
        })
        .catch(error => console.error('Error fetching weather data:', error));
</script>

</body>
</html>
