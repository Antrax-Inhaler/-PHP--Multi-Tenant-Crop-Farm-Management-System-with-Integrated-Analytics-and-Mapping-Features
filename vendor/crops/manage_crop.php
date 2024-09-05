<?php
require_once('./../../config.php');
$farm_id = $_GET['id'] ?? null;
$latitude = $longitude = $size = null;

if(isset($farm_id) && $farm_id > 0){
    $farm_qry = $conn->query("SELECT * from `farm` where Id = '{$farm_id}'");
    if($farm_qry->num_rows > 0){
        $farm = $farm_qry->fetch_assoc();
        $latitude = $farm['Latitude'];
        $longitude = $farm['Longitude'];
        $size = $farm['Size'];
        $farmName = $farm['Name'];

    }
}

$crop_id = $_GET['Id'] ?? null;
if(isset($crop_id) && $crop_id > 0){
    $qry = $conn->query("SELECT * from `crop` where id = '{$crop_id}'");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k = $v;
        }
    } else {
?>

        <center>Unknown Crop</center>
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
} else {
    $FarmId = $farm_id; // Assign $farm_id to $FarmId if it exists
}
?>
<div class="container-fluid">
    <form action="" id="crop-form">
        <input type="hidden" name="Id" value="<?php echo isset($Id) ? $Id : ''; ?>">
        <input type="hidden" name="VendorId" value="<?= $_settings->userdata('id') ?>">
        <input type="hidden" name="FarmId" value="<?php echo isset($FarmId) ? $FarmId : ''; ?>">
        <input type="hidden" name="Latitude" id="Latitude" value="<?php echo isset($Latitude) ? $Latitude : ''; ?>">
        <input type="hidden" name="Longitude" id="Longitude" value="<?php echo isset($Longitude) ? $Longitude : ''; ?>">
        
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="Name" class="control-label">Crop Name</label>
                    <input name="Name" id="Name" type="text" class="form-control form-control-sm form-control-border" value="<?php echo isset($Name) ? $Name : ''; ?>" required autocomplete="off">
                    <div id="crop-suggestions" class="suggestions-list"></div>
                </div>
                <div class="form-group">
                    <label for="Type" class="control-label">Crop Type</label>
                    <input name="Type" id="Type" type="text" class="form-control form-control-sm form-control-border" value="<?php echo isset($Type) ? $Type : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="PlannedPlantingDate" class="control-label">Planned Planting Date</label>
                    <input name="PlannedPlantingDate" id="PlannedPlantingDate" type="date" class="form-control form-control-sm form-control-border" value="<?php echo isset($PlannedPlantingDate) ? $PlannedPlantingDate : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="DatePlanted" class="control-label">Date Planted</label>
                    <input name="DatePlanted" id="DatePlanted" type="date" class="form-control form-control-sm form-control-border" value="<?php echo isset($DatePlanted) ? $DatePlanted : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="SizeOfPlantation" class="control-label">Size of Plantation</label>
                    <input name="SizeOfPlantation" id="SizeOfPlantation" type="number" step="any" class="form-control form-control-sm form-control-border" value="<?php echo isset($SizeOfPlantation) ? $SizeOfPlantation : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="Description" class="control-label">Description</label>
                    <textarea name="Description" id="Description" rows="4" class="form-control form-control-sm rounded-0" required><?php echo isset($Description) ? html_entity_decode($Description) : ''; ?></textarea>
                </div>
                <div class="form-group">
            <label for="map" class="control-label">Drag Crop Location</label>
            <div id="map2" style="border: 1px black; height: 400px; width: 100%;"></div>
        </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="Picture1" class="control-label">Crop Image</label>
                    <input type="file" id="Picture1" name="Picture1" class="form-control form-control-sm form-control-border" onchange="displayImg(this,$(this))" accept="image/png, image/jpeg" <?= !isset($Id) ? 'required' : '' ?>>
                </div>
                <div class="form-group text-center">
                    <img src="<?= validate_image(isset($Picture1) ? $Picture1 : "") ?>" alt="Crop Image" id="cimg" class="border border-gray img-thumbnail">
                </div>
                <div class="form-group">
                    <label for="Status" class="control-label">Status</label>
                    <select name="Status" id="Status" class="custom-select" required>
                        <option value="Alive" <?php echo isset($Status) && $Status == 'Alive' ? 'selected' : '' ?>>Active</option>
                        <option value="Diseased" <?php echo isset($Status) && $Status == 'Diseased' ? 'selected' : '' ?>>Inactive</option>
                        <option value="End of Lifespan" <?php echo isset($Status) && $Status == 'End of Lifespan' ? 'selected' : '' ?>>End of Lifespan</option>
                        <option value="Unproductive" <?php echo isset($Status) && $Status == 'Unproductive' ? 'selected' : '' ?>>Unproductive</option>
                    </select>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    $(document).ready(function(){
        // Predefined crop names for suggestions
        var cropSuggestions = [
    "Rice", "Corn", "Calamansi", "Sweet Potato", "Cassava",
    "Coconut", "Banana", "Mango", "Pineapple", "Sugarcane",
    "Coffee", "Cacao", "Tomato", "Eggplant", "Lettuce"
];


        // Function to update suggestions based on input value or show on focus
        function updateSuggestions(value) {
            var suggestions = cropSuggestions.filter(function (crop) {
                return crop.toLowerCase().includes(value.toLowerCase());
            });

            var suggestionsList = document.getElementById('crop-suggestions');
            suggestionsList.innerHTML = '';

            suggestions.forEach(function (suggestion) {
                var suggestionItem = document.createElement('div');
                suggestionItem.classList.add('suggestion-item');
                suggestionItem.textContent = suggestion;
                suggestionsList.appendChild(suggestionItem);
            });

            // Show/hide suggestions container based on input value
            suggestionsList.style.display = suggestions.length > 0 ? 'block' : 'none';
        }

        // Handle input change for crop name field
        $('#Name').on('input', function() {
            updateSuggestions($(this).val());
        });

        // Show suggestions on focus
        $('#Name').on('focus', function() {
            updateSuggestions($(this).val());
        });

        // Handle suggestion click to fill in the input field
        $(document).on('click', '.suggestion-item', function() {
            $('#Name').val($(this).text());
            $('#crop-suggestions').hide();
        });

        // Prevent form submission on Enter key press in suggestions
        $('#crop-suggestions').on('keydown', function(event) {
            if (event.keyCode === 13) {
                event.preventDefault();
                return false;
            }
        });

        // Card hover effect
        $(document).on('mouseenter', '.crop-item', function() {
            $(this).addClass('hovered');
        });

        $(document).on('mouseleave', '.crop-item', function() {
            $(this).removeClass('hovered');
        });

        // Submit form handling
        $('#crop-form').submit(function(e) {
            e.preventDefault();
            var _this = $(this);
            $('.err-msg').remove();
            if (_this[0].checkValidity() == false) {
                _this[0].reportValidity();
                return false;
            }
            // AJAX form submission code here
        });
    });
</script>


<script>
    function displayImg(input, _this) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#cimg').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        } else {
            $('#cimg').attr('src', '<?= validate_image(isset($image_path) ? $image_path : "") ?>');
        }
    }
    
    $(document).ready(function(){
        $('#uni_modal').on('shown.bs.modal',function(){
            // No need for Summernote initialization
        });
        
        $('#uni_modal #crop-form').submit(function(e){
            e.preventDefault();
            var _this = $(this);
            $('.err-msg').remove();
            if(_this[0].checkValidity() == false){
                _this[0].reportValidity();
                return false;
            }
            var el = $('<div>');
            el.addClass("alert err-msg alert-danger");
            el.hide();
            start_loader();
            $.ajax({
                url: _base_url_+"classes/Master.php?f=save_crop",
                data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
                error: function(xhr, status, error) {
                    console.error(error);
                    el.text("An error occurred");
                    $('#uni_modal .modal-body').prepend(el);
                    el.show();
                    end_loader();
                },
                success: function(resp){
                    if(typeof resp == 'object' && resp.status == 'success'){
                        location.reload();
                    } else if(resp.status == 'failed' && !!resp.msg){
                        el.text(resp.msg);
                        $('#uni_modal .modal-body').prepend(el);
                        el.show();
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
    function initMap() {
        var farmLat = <?= $latitude ?>;
        var farmLng = <?= $longitude ?>;
        var farmSize = <?= $size ?>;

        var map = new google.maps.Map(document.getElementById('map2'), {
            center: {lat: farmLat, lng: farmLng},
            zoom: 15
        });

        var farmCircle = new google.maps.Circle({
            strokeColor: '#FFFF00',
            strokeOpacity: 0.8,
            strokeWeight: 2,
            fillColor: '#FFFF00',
            fillOpacity: 0.35,
            map: map,
            center: {lat: farmLat, lng: farmLng},
            radius: farmSize * 10 // Adjust the multiplier for visual scaling
        });

        var customMarker = '../uploads/markerhand.png'; // Path to your custom marker image
        var marker = new google.maps.Marker({
            position: {lat: farmLat, lng: farmLng},
            map: map,
            icon: customMarker,
            draggable: true // Make the marker draggable
        });

        // Update latitude and longitude input fields when marker is dragged
        google.maps.event.addListener(marker, 'dragend', function(event) {
            var newLat = event.latLng.lat();
            var newLng = event.latLng.lng();

            // Update hidden input fields
            document.getElementById('Latitude').value = newLat;
            document.getElementById('Longitude').value = newLng;

            console.log('New position: ' + newLat + ', ' + newLng);
        });
    }
</script>

</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCPgOaKhjwvksUVP6qBQpjdq3bTQa57NuQ&callback=initMap" async></script>
