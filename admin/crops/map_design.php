<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Crop Locations</title>
  <style>
    #map {
      height: 400px;
      width: 100%;
    }
  </style>
</head>
<body>
  <div id="map"></div>

  <script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCPgOaKhjwvksUVP6qBQpjdq3bTQa57NuQ&callback=initMap"
    async
  ></script> 
    <script>
    // Initialize the map
    function initMap() {
      const map = new google.maps.Map(document.getElementById("map"), {
        center: { lat: 13.4363, lng: 121.1887 }, // Default center coordinates (Naujan Oriental Mindoro)
        zoom: 10, // Adjust the zoom level as needed
      });

      // Fetch crop locations from PHP script
      fetch('farm_map.php')
        .then(response => response.json())
        .then(data => {
          // Add markers for each crop location with farm information as a tooltip
          data.forEach(crop => {
            const marker = new google.maps.Marker({
              position: { lat: parseFloat(crop.lat), lng: parseFloat(crop.lng) },
              map: map,
              title: crop.cropName,
            });

            const infowindow = new google.maps.InfoWindow({
              content: `<strong>${crop.cropName}</strong><br>Farm: ${crop.farmName}<br>Lat: ${crop.farmLat}, Lng: ${crop.farmLng}`,
            });

            marker.addListener("click", () => {
              infowindow.open(map, marker);
            });
          });
        })
        .catch(error => {
          console.error('Error fetching data:', error);
        });
    }
  </script>
  <script>
    // Load the map when the page finishes loading
    google.maps.event.addDomListener(window, "load", initMap);
  </script>
</body>
</html>
