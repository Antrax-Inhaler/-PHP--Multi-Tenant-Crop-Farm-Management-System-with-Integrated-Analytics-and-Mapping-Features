
<?php
  $apiKey = ''; // Replace with your Google Maps API Key
?>

<div id="map"></div>
<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $apiKey ?>&callback=initMap" async></script>

<script>
  function initMap() {
    var map = new google.maps.Map(document.getElementById('map'), {
      zoom: 13,
      center: {lat: 12.8792, lng: 121.9236} // Coordinates of Naujan
    });

    // Add markers for popular destinations (replace with your desired locations and information)
    var markers = [
      {
        coords: {lat: 12.8333, lng: 121.9583}, // Caleruega Beach
        content: 'Caleruega Beach'
      },
      {
        coords: {lat: 12.8908, lng: 121.8961}, // Nagsagingan Beach
        content: 'Nagsagingan Beach'
      },
      {
        coords: {lat: 12.9278, lng: 121.8722}, // Banaue Beach
        content: 'Banaue Beach'
      }
    ];

    for (var i = 0; i < markers.length; i++) {
      var marker = new google.maps.Marker({
        position: markers[i].coords,
        map: map,
        title: markers[i].content
      });
    }
  }
</script>

<div class="content py-3">
    <div class="card rounded-0 card-outline card-navy shadow" >
        <div class="card-body rounded-0">
            <h2 class="text-center">About</h2>
            <center><hr class="bg-navy border-navy w-25 border-2"></center>
            <div>
                <?= file_get_contents("about.html") ?>
            </div>
        </div>
    </div>
</div>