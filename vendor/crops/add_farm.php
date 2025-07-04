<?php
require_once('./../../config.php');
?>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCPgOaKhjwvksUVP6qBQpjdq3bTQa57NuQ&libraries=drawing&callback=initMap"
    async defer></script>

<style>
    #map2 {
        height: 500px;
        width: 100%;
    }
</style>

<form action="" id="farm-form">
    <input type="hidden" name="VendorListId" value="<?= $_settings->userdata('id') ?>">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="name" class="control-label">Farm Name</label>
                <input name="Name" id="name" type="text" class="form-control form-control-sm form-control-border" required>
            </div>
            <div class="form-group">
                <label for="description" class="control-label">Description</label>
                <textarea name="Description" id="description" rows="4" class="form-control form-control-sm rounded-0 summernote" required></textarea>
            </div>
            <div class="form-group">
                <label for="size" class="control-label">Size (in hectares)</label>
                <input name="Size" id="size" type="number" step="0.01" class="form-control form-control-sm form-control-border" required>
            </div>
            <div class="form-group">
                <label for="logo" class="control-label">Upload Image</label>
                <input type="file" id="logo" name="Image" class="form-control form-control-sm form-control-border" onchange="displayImg(this,$(this))" accept="image/png, image/jpeg" required>
            </div>
            <div class="form-group col-md-6 text-center">
                <img src="<?= validate_image('') ?>" alt="Farm Image" id="cimg" class="border border-gray img-thumbnail">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="location" class="control-label">Select Farm Location and Draw Boundary</label>
                <div id="map2"></div>
                <!-- Hidden fields to store center coordinates and boundary data -->
                <input type="number" id="latitude" name="Latitude">
                <input type="number" id="longitude" name="Longitude">
                <input type="hidden" id="boundary" name="Boundary">
            </div>
        </div>
    </div>
</form>

<script>
    function displayImg(input, _this) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#cimg').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        } else {
            $('#cimg').attr('src', '<?= validate_image('') ?>');
        }
    }

    $(document).ready(function () {
        $('#uni_modal').on('shown.bs.modal', function () {
            $('.summernote').summernote({
                height: "40vh",
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear']],
                    ['fontname', ['fontname']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ol', 'ul', 'paragraph', 'height']],
                    ['table', ['table']],
                    ['view', ['undo', 'redo', 'fullscreen', 'codeview', 'help']]
                ]
            });
        });

        $('#uni_modal #farm-form').submit(function (e) {
            e.preventDefault();
            var _this = $(this);
            $('.err-msg').remove();
            if (_this[0].checkValidity() == false) {
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
                success: function (resp) {
                    if (typeof resp == 'object' && resp.status == 'success') {
                        location.reload();
                    } else if (resp.status == 'failed' && !!resp.msg) {
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
    });
</script>
<script>
  let map;
  let drawingManager;
  let selectedShape;

  // Initialize the map
  function initMap() {
    const defaultLocation = { lat: 13.2312895125931, lng: 121.194747 }; // Default location

    map = new google.maps.Map(document.getElementById("map2"), {
      zoom: 14,
      center: defaultLocation,
    });

    // Initialize the drawing manager for polygons
    drawingManager = new google.maps.drawing.DrawingManager({
      drawingMode: google.maps.drawing.OverlayType.POLYGON,
      drawingControl: true,
      drawingControlOptions: {
        position: google.maps.ControlPosition.TOP_CENTER,
        drawingModes: ['polygon'],
      },
      polygonOptions: {
        draggable: true,
        editable: true,
      },
    });

    drawingManager.setMap(map);

    // Event listener for completing the drawing
    google.maps.event.addListener(drawingManager, 'overlaycomplete', function (event) {
      if (event.type === google.maps.drawing.OverlayType.POLYGON) {
        if (selectedShape) {
          selectedShape.setMap(null);
        }
        selectedShape = event.overlay;
        selectedShape.type = event.type;

        // Store the boundary and center coordinates
        calculatePolygonData(selectedShape);
      }
    });
  }

  // Function to calculate the polygon's center and boundary
  function calculatePolygonData(polygon) {
    const path = polygon.getPath().getArray();
    const bounds = new google.maps.LatLngBounds();
    const boundaryCoordinates = [];

    path.forEach(function (latLng) {
      bounds.extend(latLng);
      boundaryCoordinates.push({ lat: latLng.lat(), lng: latLng.lng() });
    });

    const center = bounds.getCenter();

    // Update the form fields with boundary and center coordinates
    document.getElementById('latitude').value = center.lat();
    document.getElementById('longitude').value = center.lng();
    document.getElementById('boundary').value = JSON.stringify(boundaryCoordinates);
  }
</script>

<script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBaEr_ZLsaoWcbipd--a1S5EPQe2RaEfio&libraries=drawing&callback=initMap"
    async defer>
</script>
