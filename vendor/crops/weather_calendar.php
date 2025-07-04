<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farm Weather Calendar</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
</head>
<body>
<h1>Farm Weather Calendar</h1>

<select id="farmSelector">
    <!-- This will be populated by get_farms.php -->
</select>

<div id="calendar"></div>

<style>
    .weather-card {
        text-align: center;
        font-family: Arial, sans-serif;
        background-color: transparent;
        border-radius: 6px;
        padding: 5px;
    }

    .weather-icon {
        width: 50px;
        height: 50px;
        display: block;
        margin: 0 auto 5px auto;
    }

    .temp {
        font-size: 16px;
        color: #ffb100; /* Red for temperature */
    }

    .fc-h-event {
        background-color: transparent;
        border: 1px solid transparent;
        display: block;
    }

    .min-max {
        font-size: 12px;
        color: #333;
    }

    .weather-desc {
        font-weight: bold;
        font-size: 14px;
        margin-bottom: 5px;
        color: black;
    }

    .wind-humidity {
        font-size: 12px;
        color: #333;
    }

    .sun-info {
        font-size: 12px;
        color: #333;
        display: flex;
        justify-content: space-between;
        padding: 5px;
    }

    .centerer {
        width: 100% !important;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        events: function(fetchInfo, successCallback, failureCallback) {
            let farmId = document.getElementById('farmSelector').value;
            if (!farmId) return;

            fetch(`crops/get_weather.php?farmId=${farmId}`)
                .then(response => response.json())
                .then(data => {
                    const events = data.map(item => {
                        return {
                            title: `${item.weatherDescription}`,
                            start: item.recordedAt,
                            extendedProps: {
                                temperature: item.temperature,
                                minTemp: item.minTemp,
                                maxTemp: item.maxTemp,
                                humidity: item.humidity,
                                windSpeed: item.windSpeed,
                                feelsLike: item.feelsLike,
                                icon: item.weatherIconUrl,
                                sunrise: item.sunrise,
                                sunset: item.sunset
                            }
                        };
                    });
                    successCallback(events);
                });
        },
        eventContent: function(arg) {
            let container = document.createElement('div');
            container.classList.add('weather-card');

            let weatherIcon = document.createElement('img');
            weatherIcon.src = arg.event.extendedProps.icon;
            weatherIcon.classList.add('weather-icon');

            let description = document.createElement('div');
            description.classList.add('weather-desc');
            description.textContent = arg.event.title;

            let tempDiv = document.createElement('div');
            tempDiv.classList.add('temp');
            tempDiv.textContent = `Temp: ${arg.event.extendedProps.temperature}°C`;

            let minMaxDiv = document.createElement('div');
            minMaxDiv.classList.add('min-max');
            minMaxDiv.textContent = `High: ${arg.event.extendedProps.maxTemp}°C / Low: ${arg.event.extendedProps.minTemp}°C`;

            let windHumidityDiv = document.createElement('div');
            windHumidityDiv.classList.add('wind-humidity');
            windHumidityDiv.innerHTML = `Wind: ${arg.event.extendedProps.windSpeed} m/s<br>Humidity: ${arg.event.extendedProps.humidity}%`;

            // Sunrise and sunset information with emojis
            let sunInfoDiv = document.createElement('div');
            sunInfoDiv.classList.add('sun-info');
            sunInfoDiv.innerHTML = `
                <div>🌇 ${new Date(arg.event.extendedProps.sunrise).toLocaleTimeString()}</div>
                <div>🌅 ${new Date(arg.event.extendedProps.sunset).toLocaleTimeString()}</div>
            `;

            container.appendChild(weatherIcon);
            container.appendChild(description);
            container.appendChild(tempDiv);
            container.appendChild(minMaxDiv);
            container.appendChild(windHumidityDiv);
            container.appendChild(sunInfoDiv); // Append sunrise/sunset info with emojis

            return { domNodes: [container] };
        }
    });

    calendar.render();

    // Fetch farms and populate the selector
    fetch('crops/get_farms.php')
        .then(response => response.json())
        .then(data => {
            const farmSelector = document.getElementById('farmSelector');
            data.farms.forEach(farm => {
                let option = document.createElement('option');
                option.value = farm.Id;
                option.text = farm.Name;
                farmSelector.appendChild(option);
            });
            farmSelector.value = data.defaultFarmId;

            // Load initial weather data
            calendar.refetchEvents();
        });

    // Refetch weather data when farm selection changes
    document.getElementById('farmSelector').addEventListener('change', function () {
        calendar.refetchEvents();
    });
});
</script>

</body>
</html>
