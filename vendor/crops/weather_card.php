<style>
    .weather-card {
        display: flex;
        align-items: center;
        border-radius: 15px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
        padding: 20px;
        max-width: 700px;
        width: 100%;
        gap: 20px;
        transition: all 0.3s ease;
        animation: fadeIn 1.5s ease-in-out;
        font-family: 'Arial', sans-serif;
    }

    .weather-card:hover {
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
        transform: translateY(-5px);
    }

    .weather-icon {
        width: 120px;
        height: 120px;
        transition: transform 0.3s ease-in-out;
    }

    .weather-icon:hover {
        transform: rotate(360deg);
    }

    .weather-info {
        flex-grow: 1;
        animation: slideIn 1s ease-out;
        color: #333;
    }

    .greeting {
        font-size: 26px;
        color: #333;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 10px;
        animation: bounce 1.5s infinite alternate;
    }

    .temperature {
        font-size: 55px;
        font-weight: bold;
        color: #ff7b00;
        display: flex;
        align-items: center;
    }

    .temperature::before {
        content: '\f2c9'; /* Font Awesome thermometer icon */
        font-family: "Font Awesome 5 Free";
        font-weight: 900;
        margin-right: 8px;
        color: #ff7b00;
    }

    .location {
        font-size: 18px;
        color: #555;
        margin-bottom: 8px;
    }

    .weather-desc {
        font-size: 18px;
        font-weight: bold;
        color: #333;
        margin-bottom: 10px;
    }

    .details {
        font-size: 14px;
        color: #666;
        display: flex;
        gap: 20px;
    }

    .details span {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .humidity-icon::before, .wind-icon::before {
        font-family: "Font Awesome 5 Free";
        font-weight: 900;
    }

    .humidity-icon::before {
        content: '\f043'; /* Font Awesome humidity icon */
    }

    .wind-icon::before {
        content: '\f72e'; /* Font Awesome wind icon */
    }

    /* Animations */
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes slideIn {
        from { transform: translateX(-100px); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }

    @keyframes bounce {
        from { transform: translateY(0); }
        to { transform: translateY(-10px); }
    }
</style>

<body>

<div class="weather-card" id="weatherCard">
    <img src="" alt="Weather Icon" class="weather-icon" id="weatherIcon">
    <div class="weather-info">
        <div class="greeting" id="greeting">Good Morning! 🌞</div>
        <div class="location" id="location">--</div>
        <div class="temperature" id="temperature">--°C</div>
        <div class="weather-desc" id="weatherDesc">--</div>
        <div class="details">
            <span class="humidity-icon" id="humidity">Humidity: --%</span>
            <span class="wind-icon" id="windSpeed">Wind: -- m/s</span>
        </div>
    </div>
</div>

<script>
    // Get user's location using the Geolocation API
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(position => {
            const lat = position.coords.latitude;
            const lon = position.coords.longitude;
            getWeatherData(lat, lon);
        }, showError);
    } else {
        alert("Geolocation is not supported by this browser.");
    }

    function showError(error) {
        switch(error.code) {
            case error.PERMISSION_DENIED:
                alert("User denied the request for Geolocation.");
                break;
            case error.POSITION_UNAVAILABLE:
                alert("Location information is unavailable.");
                break;
            case error.TIMEOUT:
                alert("The request to get user location timed out.");
                break;
            case error.UNKNOWN_ERROR:
                alert("An unknown error occurred.");
                break;
        }
    }

    // Fetch weather data based on latitude and longitude
    function getWeatherData(lat, lon) {
        const apiKey = ""; // Replace with your OpenWeather API key
        const url = `https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${lon}&appid=${apiKey}&units=metric`;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                updateWeatherCard(data);
            })
            .catch(error => {
                console.error("Error fetching weather data: ", error);
            });
    }

    // Update the weather card with fetched data
    function updateWeatherCard(weatherData) {
        const location = weatherData.name; // Get location name from OpenWeather response
        const temperature = weatherData.main.temp;
        const weatherDescription = weatherData.weather[0].description;
        const humidity = weatherData.main.humidity;
        const windSpeed = weatherData.wind.speed;
        const iconCode = weatherData.weather[0].icon;
        const weatherIconUrl = `http://openweathermap.org/img/wn/${iconCode}@2x.png`;

        document.getElementById('location').textContent = location;
        document.getElementById('weatherIcon').src = weatherIconUrl;
        document.getElementById('temperature').textContent = `${Math.round(temperature)}°C`;
        document.getElementById('weatherDesc').textContent = capitalizeFirstLetter(weatherDescription);
        document.getElementById('humidity').textContent = `Humidity: ${humidity}%`;
        document.getElementById('windSpeed').textContent = `Wind: ${windSpeed} m/s`;

        const greeting = getDynamicGreeting(weatherData);
        document.getElementById('greeting').textContent = greeting;
    }

    // Capitalize the first letter of the weather description
    function capitalizeFirstLetter(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    function getDynamicGreeting(weatherData) {
    const currentHour = new Date().getHours();
    const weatherDesc = weatherData.weather[0].main.toLowerCase();
    const temperature = weatherData.main.temp;
    const humidity = weatherData.main.humidity;
    const windSpeed = weatherData.wind.speed;
    
    let greeting = "";
    let emoji = "";
    let adviceList = [];

    // Time-based greetings
    if (currentHour < 6) {
        greeting = "Good early morning! Time to prepare for the day's farm tasks!";
        emoji = "🌄";
    } else if (currentHour < 12) {
        greeting = "Good morning! It's a great time to check on your crops.";
        emoji = "🌞";
    } else if (currentHour < 16) {
        greeting = "Good afternoon! Consider tending to your crops or irrigating.";
        emoji = "☀️";
    } else if (currentHour < 18) {
        greeting = "Good late afternoon! Perfect time to wrap up your work.";
        emoji = "🌇";
    } else if (currentHour < 21) {
        greeting = "Good evening! Ensure your crops are safe for the night.";
        emoji = "🌆";
    } else {
        greeting = "Good night! Rest well for another productive day tomorrow.";
        emoji = "🌙";
    }

    // Weather-related advice
    if (weatherDesc.includes("rain")) {
        adviceList.push("Ensure proper drainage to avoid waterlogging. Check your irrigation systems and consider delaying watering. ☔");
    } else if (weatherDesc.includes("clear")) {
        adviceList.push("This is a great opportunity to water your crops if needed. Protect sensitive plants from excessive heat and monitor soil moisture. 🌱");
    } else if (weatherDesc.includes("cloud")) {
        adviceList.push("Mild weather means good working conditions, but keep an eye on your crops in case rain comes later. ☁️");
    } else if (weatherDesc.includes("snow")) {
        adviceList.push("Ensure that your greenhouse is insulated and that plants are protected from the cold. ❄️");
    } else if (weatherDesc.includes("storm")) {
        adviceList.push("Secure crop covers and store tools. Check that vulnerable plants are shielded from wind and rain damage. ⛈️");
    } else if (weatherDesc.includes("wind")) {
        adviceList.push("Secure any loose materials and make sure your crops are protected from wind damage. 🌬️");
    }

    // Temperature-based advice
    if (temperature > 30) {
        adviceList.push("Be mindful of heat stress in your crops. Ensure they are well-watered and consider providing shade for sensitive plants. 🔥");
    } else if (temperature < 10) {
        adviceList.push("Low temperatures may impact crop growth. Consider protective measures like mulching or using covers for delicate crops. 🧣");
    }

    // Humidity and pest/disease control
    if (humidity > 70) {
        adviceList.push("High humidity can lead to fungal diseases. Monitor your crops for signs of mold or mildew. 🌿");
    }

    // Wind and irrigation advice
    if (windSpeed > 10) {
        adviceList.push("Strong winds may dry out the soil quickly. Check your soil moisture and consider watering your crops if needed. 💨");
    }

    // Season-based greetings and crop management tips
    const month = new Date().getMonth();
    if (month >= 2 && month <= 4) {
        adviceList.push("This is the ideal time for planting many crops. Make sure your soil is prepared, and consider adding compost for a nutrient boost. 🌷");
    } else if (month >= 5 && month <= 7) {
        adviceList.push("Be diligent about irrigation and check for signs of heat stress in your crops. Apply mulches to retain soil moisture. 🌻");
    } else if (month >= 8 && month <= 10) {
        adviceList.push("Start preparing your storage areas and monitor weather conditions for optimal harvesting. 🍁");
    } else {
        adviceList.push("Prepare for the cold by insulating greenhouses and protecting crops with frost covers. ❄️");
    }

    // Randomly select one piece of advice
    const randomAdvice = adviceList.length > 0 ? adviceList[Math.floor(Math.random() * adviceList.length)] : "";

    // Return greeting with selected advice
    return `${greeting} ${emoji} ${randomAdvice}`;
}


</script>

</body>
