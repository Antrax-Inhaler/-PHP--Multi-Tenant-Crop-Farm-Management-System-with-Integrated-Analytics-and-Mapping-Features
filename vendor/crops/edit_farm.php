<?php
require_once('./../../config.php');

if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `farm` where Id = '{$_GET['id']}' and delete_flag = 0 ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k = $v;
        }
    } else {
?>
    <center>Unknown Farm</center>
    <style>
        #uni_modal .modal-footer{
            display:none
        }
    </style>
    <div class="text-right">
        <button class="btn btn-default bg-gradient-dark btn-flat" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
    </div>
<?php
    exit;
    }
}
?>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCPgOaKhjwvksUVP6qBQpjdq3bTQa57NuQ&callback=initMap" async></script>

<style>
    #map2 {
        height: 500px;
        width: 100%;
    }
</style>
<div class="container-fluid">
    <form action="" id="farm-form">
        <input type="hidden" name="Id" value="<?php echo isset($Id) ? $Id : '' ?>">
        <div class="form-group">
            <label for="Name" class="control-label">Farm Name</label>
            <input name="Name" id="Name" type="text" class="form-control form-control-sm form-control-border" value="<?php echo isset($Name) ? $Name : ''; ?>" required>
        </div>
        <div class="col-md-6">
                <div class="form-group">
                    <label for="location" class="control-label">Select Farm Location</label>
                    <div id="map2"></div>
                    <input type="hidden" id="latitude" name="Latitude" value="<?php echo isset($Latitude) ? $Latitude : ''; ?>">
                    <input type="hidden" id="longitude" name="Longitude" value="<?php echo isset($Longitude) ? $Longitude : ''; ?>" >
                </div>
            </div>
        <div class="form-group">
            <label for="Size" class="control-label">Size (hectares)</label>
            <input name="Size" id="Size" type="number" step="0.01" class="form-control form-control-sm form-control-border" value="<?php echo isset($Size) ? $Size : ''; ?>" required>
        </div>
        <div class="form-group">
            <label for="Description" class="control-label">Description</label>
            <textarea name="Description" id="Description" rows="4" class="form-control form-control-sm rounded-0" required><?php echo isset($Description) ? $Description : ''; ?></textarea>
        </div>
        <div class="form-group">
            <label for="Image" class="control-label">Farm Image</label>
            <input type="file" id="Image" name="Image" class="form-control form-control-sm form-control-border" onchange="displayImg(this,$(this))" accept="image/png, image/jpeg" <?= !isset($Id) ? 'required' : '' ?>>
        </div>
        <div class="form-group text-center">
            <img src="<?= validate_image(isset($Image) ? $Image : "") ?>" alt="Farm Image" id="cimg" class="border border-gray img-thumbnail">
        </div>
    </form>
</div>

<script>
    function displayImg(input, _this) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#cimg').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        } else {
            $('#cimg').attr('src', '<?= validate_image(isset($Image) ? $Image : "") ?>');
        }
    }

    $(document).ready(function(){
        $('#uni_modal #farm-form').submit(function(e){
            e.preventDefault();
            var _this = $(this);
            $('.err-msg').remove();
            if(_this[0].checkValidity() == false){
                _this[0].reportValidity();
                return false;
            }
            var el = $('<div>');
                el.addClass("alert err-msg");
                el.hide();
            start_loader();
            $.ajax({
                url: _base_url_ + "classes/Master.php?f=save_farm",
                data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
                error: err => {
                    console.error(err);
                    el.addClass('alert-danger').text("An error occurred");
                    _this.prepend(el);
                    el.show('.modal');
                    end_loader();
                },
                success: function(resp){
                    if(typeof resp == 'object' && resp.status == 'success'){
                        location.reload();
                    } else if(resp.status == 'failed' && !!resp.msg){
                        el.addClass('alert-danger').text(resp.msg);
                        _this.prepend(el);
                        el.show('.modal');
                    } else {
                        el.text("An error occurred");
                        console.error(resp);
                    }
                    $("html, body").scrollTop(0);
                    end_loader();
                }
            });
        });

        $('.summernote').summernote({
            height: 200,
            toolbar: [
                [ 'style', [ 'style' ] ],
                [ 'font', [ 'bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear'] ],
                [ 'fontname', [ 'fontname' ] ],
                [ 'fontsize', [ 'fontsize' ] ],
                [ 'color', [ 'color' ] ],
                [ 'para', [ 'ol', 'ul', 'paragraph', 'height' ] ],
                [ 'table', [ 'table' ] ],
                [ 'view', [ 'undo', 'redo', 'fullscreen', 'codeview', 'help' ] ]
            ]
        });
    });
</script>
<script>
  // Initialize the map
  function initMap() {
    // Create a map centered at a default location
    const map = new google.maps.Map(document.getElementById("map2"), {
      zoom: 14, // Adjust the zoom level as needed
    });

    // Custom marker icon
    const customMarker = '../uploads/marker100.png';

    // Variable to store the center coordinates
    let centerCoords = { lat: 13.2312895125931, lng: 121.194747 }; // Default location

    // Array to store farm locations and associated crops with information fetched from PHP
    const farmData = [
      <?php
        // Your PHP code to fetch farm locations and associated crops with information based on the SQL query
        $user_id = $_settings->userdata('id');
        $sql = "SELECT c.Name as CropName, c.Type, c.PlannedPlantingDate, c.DatePlanted, c.SizeOfPlantation, c.Description, c.Picture1, f.Name as FarmName, f.Latitude as FarmLat, f.Longitude as FarmLng
                FROM crop c
                INNER JOIN farm f ON c.FarmId = f.Id
                INNER JOIN vendor_list v ON c.VendorId = v.id
                WHERE v.user_id = '{$user_id}' AND v.delete_flag = 0
                ORDER BY f.Name ASC, c.Name ASC";

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
          // Initialize an array to hold farm data
          $farmDataArray = [];
          while($row = $result->fetch_assoc()) {
            // Group crops by farm
            $farmDataArray[$row["FarmName"]][] = [
              "cropName" => $row["CropName"],
              "cropDetails" => [
                "Type" => $row["Type"],
                "PlannedPlantingDate" => $row["PlannedPlantingDate"],
                "DatePlanted" => $row["DatePlanted"],
                "SizeOfPlantation" => $row["SizeOfPlantation"],
                "Description" => $row["Description"],
                "Picture1" => $row["Picture1"]
              ],
              "lat" => $row["FarmLat"],
              "lng" => $row["FarmLng"]
            ];
          }

          // Generate JavaScript array for farm data
          foreach ($farmDataArray as $farmName => $crops) {
            echo "{ farmName: '{$farmName}', crops: [";
            foreach ($crops as $crop) {
              echo "{ cropName: '{$crop['cropName']}', cropDetails: " . json_encode($crop['cropDetails']) . ", lat: {$crop['lat']}, lng: {$crop['lng']} },";
            }
            echo "] },\n";
          }
        } else {
          echo "{ farmName: 'No farms found', crops: [] }"; // Default empty data
        }
      ?>
    ];

    // Add farm markers and crop info windows to the map
    farmData.forEach((farm) => {
      const farmMarker = new google.maps.Marker({
        position: { lat: parseFloat(farm.crops[0].lat), lng: parseFloat(farm.crops[0].lng) }, // Use first crop coordinates
        map: map,
        title: farm.farmName,
        icon: customMarker, // Use custom marker icon
      });

      const infowindow = new google.maps.InfoWindow({
        content: generateInfoWindowContent(farm),
      });

      farmMarker.addListener("click", () => {
        infowindow.open(map, farmMarker);
      });

      // Set the center coordinates to the first farm marker
      if (!centerCoords) {
        centerCoords = { lat: parseFloat(farm.crops[0].lat), lng: parseFloat(farm.crops[0].lng) };
      }
    });

    // Center the map on the user's location if geolocation is available
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        (position) => {
          const userLocation = {
            lat: position.coords.latitude,
            lng: position.coords.longitude,
          };

          map.setCenter(userLocation);

          const userMarker = new google.maps.Marker({
            position: userLocation,
            map: map,
            icon: customMarker, // Use custom marker icon
            draggable: true,
          });

          // Update the Latitude and Longitude input fields when the marker is dragged
          google.maps.event.addListener(userMarker, 'dragend', function () {
            const latLng = userMarker.getPosition();
            document.getElementById('latitude').value = latLng.lat();
            document.getElementById('longitude').value = latLng.lng();
          });
        },
        () => {
          // If the user denies the geolocation request, center the map on the default location
          map.setCenter(centerCoords);
        }
      );
    } else {
      // If the browser doesn't support geolocation, center the map on the default location
      map.setCenter(centerCoords);
    }

    // Add a draggable marker for selecting new farm location
    const newFarmMarker = new google.maps.Marker({
      position: centerCoords,
      map: map,
      draggable: true,
      icon: customMarker, // Use custom marker icon
    });

    // Update the Latitude and Longitude input fields when the marker is dragged
    google.maps.event.addListener(newFarmMarker, 'dragend', function () {
      const latLng = newFarmMarker.getPosition();
      document.getElementById('latitude').value = latLng.lat();
      document.getElementById('longitude').value = latLng.lng();
    });
  }

  // Function to generate info window content for farms and associated crops
  function generateInfoWindowContent(farm) {
    let content = `<strong>${farm.farmName}</strong><br>`;
    farm.crops.forEach((crop) => {
      content += `
        <div class="card mt-3">
          <div class="card-body">
            <div class="row">
              <div class="col-md-4">
                <img src="../${crop.cropDetails.Picture1}" alt="Crop Image" width="100">
              </div>
              <div class="col-md-8">
                <strong>${crop.cropName}</strong><br>
                <ul class="list-unstyled">
                  <li><strong title="Type">Type:</strong> ${crop.cropDetails.Type}</li>
                  <li><strong title="Planned Planting Date">Planned Planting Date:</strong> ${crop.cropDetails.PlannedPlantingDate}</li>
                  <li><strong title="Date Planted">Date Planted:</strong> ${crop.cropDetails.DatePlanted}</li>
                  <li><strong title="Size of Plantation">Size of Plantation:</strong> ${crop.cropDetails.SizeOfPlantation}</li>
                  <li><strong title="Description">Description:</strong> ${crop.cropDetails.Description}</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      `;
    });
    return content;
  }
</script>
