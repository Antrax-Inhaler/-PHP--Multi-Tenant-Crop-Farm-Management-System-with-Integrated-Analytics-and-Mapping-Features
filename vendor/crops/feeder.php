<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Weather Calendar</title>
    <link href='https://cdn.jsdelivr.net/npm/@fullcalendar/core@4.3.0/main.min.css' rel='stylesheet' />
    <link href='https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@4.3.0/main.min.css' rel='stylesheet' />
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f9fc;
            margin: 20px;
            padding: 0;
        }
        #calendar {
            max-width: 900px;
            margin: 0 auto;
        }
        .future-weather {
            background-color: rgba(255, 165, 0, 0.3);
        }
    </style>
</head>
<body>

<div>
    <label for="farm-select">Select Farm:</label>
    <select id="farm-select"></select>
</div>

<div id='calendar'></div>

<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@4.3.0/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@4.3.0/main.min.js'></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script>
$(document).ready(function() {
    let apiKey = '2f745fa85d563da5adb87b6cd4b81caf';
    
    // Fetch farms owned by the current vendor
    $.ajax({
        url: 'crops/get_farms.php',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            let farmSelect = $('#farm-select');
            farmSelect.empty();
            
            data.farms.forEach(function(farm) {
                farmSelect.append('<option value="' + farm.Id + '">' + farm.Name + '</option>');
            });
            
            // Initialize calendar with the default farm's weather
            if (data.defaultFarmId) {
                loadWeatherCalendar(data.defaultFarmId);
            }

            // Change event for selecting a different farm
            farmSelect.change(function() {
                loadWeatherCalendar($(this).val());
            });
        }
    });

    // Function to load the calendar and fetch weather data
    function loadWeatherCalendar(farmId) {
        $('#calendar').fullCalendar('destroy'); // Clear existing calendar events
        
        // Fetch latitude and longitude of the selected farm
        $.ajax({
            url: 'crops/get_weather.php',
            method: 'GET',
            data: { farmId: farmId },
            dataType: 'json',
            success: function(data) {
                let events = data.map(event => ({
                    title: event.weatherDescription,
                    start: event.start,
                    className: event.isFuture ? 'future-weather' : ''
                }));

                // Initialize the calendar with existing weather data
                $('#calendar').fullCalendar({
                    plugins: ['dayGrid'],
                    defaultView: 'dayGridMonth',
                    events: events,
                    eventRender: function(info) {
                        if (info.event.extendedProps.icon) {
                            let iconUrl = 'https://openweathermap.org/img/wn/' + info.event.extendedProps.icon + '@2x.png';
                            $(info.el).css('background-image', 'url(' + iconUrl + ')');
                            $(info.el).css('background-size', 'cover');
                        }
                    }
                });

                // Fetch latitude and longitude for the selected farm
                $.ajax({
                    url: 'crops/get_farm_location.php',
                    method: 'GET',
                    data: { farmId: farmId },
                    dataType: 'json',
                    success: function(locationData) {
                        let farmLatitude = locationData.latitude;
                        let farmLongitude = locationData.longitude;

                        // Fetch and display weather forecast for the next 5 days
                        $.ajax({
                            url: 'https://api.openweathermap.org/data/2.5/forecast',
                            method: 'GET',
                            data: {
                                lat: farmLatitude,
                                lon: farmLongitude,
                                appid: apiKey,
                                units: 'metric'
                            },
                            dataType: 'json',
                            success: function(forecastData) {
                                forecastData.list.forEach(function(item) {
                                    let date = item.dt_txt.split(' ')[0];
                                    let description = item.weather[0].description;
                                    let icon = item.weather[0].icon;

                                    $('#calendar').fullCalendar('renderEvent', {
                                        title: description,
                                        start: date,
                                        className: 'future-weather',
                                        extendedProps: {
                                            icon: icon
                                        }
                                    });
                                });
                            }
                        });
                    }
                });
            }
        });
    }
});

</script>

</body>
</html>
