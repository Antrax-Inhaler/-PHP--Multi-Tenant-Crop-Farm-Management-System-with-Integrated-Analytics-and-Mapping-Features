
    <style>
        .ebody {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .weather-container {
            width: 100%;
            max-width: 1200px;
            margin: 20px;
            padding: 20px;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        .header-temp-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .weather-details-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
        }
        .weather-day {
            flex: 1;
            margin: 10px;
            padding: 20px;
            background: #f7f7f7;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }
        .weather-day img {
            max-width: 100px;
        }
        .info-box {
            display: flex;
            align-items: center;
            padding: 20px;
            background: #d9ecf2;
            border-radius: 8px;
        }
        .info-box-icon {
            margin-right: 20px;
        }
        .info-box-content {
            flex: 1;
        }
        .info-header {
            font-size: 24px;
            font-weight: bold;
        }
        .info-box-number {
            font-size: 24px;
            color: #333;
        }
    </style>
<div class="ebody" >
    <div  id="weather-info" class="weather-container">
        <div class="info-box">
            <span class="info-box-icon bg-gradient-info elevation-1"><i class="fas fa-spinner fa-spin"></i></span>
            <div class="info-box-content">
                <div class="header-temp-container">
                    <div class="info-header h3">Loading...</div>
                    <span class="info-box-number text-right h3">&#176;</span>
                </div>
            </div>
        </div>
    </div>
    </div>
    <script>
        const farmLatitude = <?= htmlspecialchars($farm['farm_latitude']) ?>;
        const farmLongitude = <?= htmlspecialchars($farm['farm_longitude']) ?>;
        const apiKey = "";

        function getWeather() {
            const url = `https://api.openweathermap.org/data/2.5/forecast/daily?lat=${farmLatitude}&lon=${farmLongitude}&cnt=16&appid=${apiKey}&units=metric`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.cod === "200") {
                        const weatherInfo = document.getElementById("weather-info");
                        weatherInfo.innerHTML = `
                            <div class="info-box">
                                <span class="info-box-icon bg-gradient-info elevation-1"><img src="https://openweathermap.org/img/w/${data.list[0].weather[0].icon}.png"></span>
                                <div class="info-box-content">
                                    <div class="header-temp-container">
                                        <div class="info-header h3">${data.city.name}, ${data.city.country}</div>
                                        <span class="info-box-number text-right h3">${data.list[0].temp.day} &#176;C</span>
                                    </div>
                                </div>
                            </div>
                            <div class="weather-details-container">
                                ${data.list.map(day => `
                                    <div class="weather-day">
                                        <h4>${new Date(day.dt * 1000).toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric' })}</h4>
                                        <img src="https://openweathermap.org/img/w/${day.weather[0].icon}.png" alt="${day.weather[0].description}">
                                        <div class="temperature">Day: ${day.temp.day} &#176;C</div>
                                        <div class="temperature">Night: ${day.temp.night} &#176;C</div>
                                        <div class="weather-description">${day.weather[0].main} - ${day.weather[0].description}</div>
                                    </div>
                                `).join('')}
                            </div>
                        `;
                    } else {
                        document.getElementById("weather-info").innerHTML = `<h3 class="error">Weather information not found</h3>`;
                    }
                })
                .catch(() => {
                    document.getElementById("weather-info").innerHTML = `<h3 class="error">Weather information not found</h3>`;
                });
        }

        getWeather();
    </script>
