<?php
require_once('./../../config.php');

if(isset($_GET['id']) && $_GET['id'] > 0) {
    $qry = $conn->query("SELECT h.*, c.Name as crop_name, c.Type as crop_type, c.Latitude as crop_lat, c.Longitude as crop_lng 
                        FROM `harvest` h 
                        JOIN `crop` c ON h.CropId = c.Id 
                        WHERE h.Id = '{$_GET['id']}'");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
        // Pre-fill name with crop name and variety
        $name = isset($crop_name) && isset($crop_type) ? $crop_name . ' (' . $crop_type . ')' : '';
    } else {
?>

<center>Unknown Harvest</center>
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
<script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCPgOaKhjwvksUVP6qBQpjdq3bTQa57NuQ&callback=initMap"
    async
  ></script> 
 <style>
    #map {
        height: 500px;
        width: 100%;
    }
</style>
<div class="container-fluid">
    <form action="" id="product-form">
        <input type="hidden" name="id" value="<?php echo isset($id) ? $id : ''; ?>">
        <input type="hidden" name="vendor_id" value="<?= $_settings->userdata('id') ?>">
        <input type="hidden" name="from_agronet" value="1">
        <div class="row">
            <div class="col-md-6">
                <!-- Name field (non-removable, crop name + type but user can add text) -->
                <div class="form-group">
                    <label for="name" class="control-label">Product Name</label>
                    <input name="name" id="name" type="text" class="form-control form-control-sm form-control-border" 
                           value="<?php echo isset($name) ? $name : ''; ?>" 
                           placeholder="Crop Name (Variety) - Additional Text" readonly
                           onfocus="this.removeAttribute('readonly');" required>
                </div>

                <!-- Category -->
                <div class="form-group">
                    <label for="category_id" class="control-label">Category</label>
                    <select id="category_id" name="category_id" class="form-control form-control-sm form-control-border select2" required>
                        <option value="" disabled <?= !isset($category_id) ? 'selected' : "" ?>></option>
                        <?php 
                        $categories = $conn->query("SELECT * FROM `category_list` where delete_flag = 0 and `status` = 1 and vendor_id= '{$_settings->userdata('id')}' ".(isset($category_id) ? " or id = '{$category_id}' " : '')." order by `name` asc ");
                        while($row = $categories->fetch_assoc()):
                        ?>
                        <option value="<?= $row['id'] ?>" <?= isset($category_id) && $category_id == $row['id'] ? 'selected': '' ?>><?= $row['name'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Description -->
                <div class="form-group">
                    <label for="description" class="control-label">Description</label>
                    <textarea name="description" id="description" rows="4" class="form-control form-control-sm rounded-0 summernote" required><?php echo isset($description) ? html_entity_decode($description) : ''; ?></textarea>
                </div>
            </div>

            <div class="col-md-6">
                <!-- Auto-fill Latitude and Longitude -->
                <div class="form-group">
                    <label for="location" class="control-label"></label>
                    <input type="hidden" id="latitude" name="latitude" class="form-control" value="<?php echo isset($crop_lat) ? $crop_lat : ''; ?>" required>
                    <input type="hidden" id="longitude" name="longitude" class="form-control" value="<?php echo isset($crop_lng) ? $crop_lng : ''; ?>" required>
                </div>

                <!-- Amount Stock (equals Amount of Harvest) -->
                <div class="form-group">
                    <label for="stock_amount" class="control-label">Amount of Harvest</label>
                    <input name="stock_amount" id="stock_amount" type="number" step="any" class="form-control form-control-sm form-control-border" 
                           value="<?php echo isset($AmountOfHarvest) ? $AmountOfHarvest : ''; ?>" required>
                </div>

                <!-- Price -->
                <div class="form-group">
                    <label for="price" class="control-label">Cost</label>
                    <input name="price" id="price" type="number" step="any" class="form-control form-control-sm form-control-border" value="<?php echo isset($price) ? $price : ''; ?>" required>
                </div>
            </div>
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
            $('#cimg').attr('src', '<?= validate_image(isset($image_path) ? $image_path : "") ?>');
        }
    }

    function displayImg2(input, _this) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#cimg_2').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        } else {
            $('#cimg_2').attr('src', '<?= validate_image(isset($image_path_2) ? $image_path_2 : "") ?>');
        }
    }

    function displayImg3(input, _this) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#cimg_3').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        } else {
            $('#cimg_3').attr('src', '<?= validate_image(isset($image_path_3) ? $image_path_3 : "") ?>');
        }
    }

    function displayImg4(input, _this) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#cimg_4').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        } else {
            $('#cimg_4').attr('src', '<?= validate_image(isset($image_path_4) ? $image_path_4 : "") ?>');
        }
    }

    function displayImg5(input, _this) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#cimg_5').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        } else {
            $('#cimg_5').attr('src', '<?= validate_image(isset($image_path_5) ? $image_path_5 : "") ?>');
        }
    }

	$(document).ready(function(){
		$('#uni_modal').on('shown.bs.modal',function(){
			$('#category_id').select2({
				placeholder:'Please Select Categoty Here.',
				width:"100%",
				dropdownParent:$('#uni_modal')
			})
			$('.select2-selection').addClass('form-border');
			$('.summernote').summernote({
		        height: "40vh",
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
		    })
		})
		$('#uni_modal #product-form').submit(function(e){
			e.preventDefault();
            var _this = $(this)
			 $('.err-msg').remove();
			 if(_this[0].checkValidity() == false){
				 _this[0].reportValidity();
				 return false;
			 }
			var el = $('<div>')
				el.addClass("alert err-msg")
				el.hide()
			start_loader();
			$.ajax({
				url:_base_url_+"classes/Master.php?f=save_product",
				data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
				error:err=>{
					console.error(err)
					el.addClass('alert-danger').text("An error occured");
					_this.prepend(el)
					el.show('.modal')
					end_loader();
				},
				success:function(resp){
					if(typeof resp =='object' && resp.status == 'success'){
						location.reload();
					}else if(resp.status == 'failed' && !!resp.msg){
                        el.addClass('alert-danger').text(resp.msg);
						_this.prepend(el)
						el.show('.modal')
                    }else{
						el.text("An error occured");
                        console.error(resp)
					}
					$("html, body").scrollTop(0);
					end_loader()

				}
			})
		})

        
	})
</script>
<script>
        var map;
        var marker;

        function initMap() {
            // Initialize map centered at Naujan, Oriental Mindoro
            var naujan = { lat: 13.2312895125931, lng: 121.194747 }; // Coordinates for Naujan, Oriental Mindoro
            map = new google.maps.Map(document.getElementById('map'), {
                center: naujan,
                zoom: 13
            });

            // Add custom image marker
            marker = new google.maps.Marker({
                position: naujan,
                map: map,
                draggable: true,
                icon: {
                    url: 'https://maps.google.com/mapfiles/ms/icons/blue-dot.png', // Change the URL to your custom marker image
                    scaledSize: new google.maps.Size(32, 32) // Adjust the size of the image marker as needed
                }
            });

            // Update marker position when dragged
            google.maps.event.addListener(marker, 'dragend', function() {
                updateMarkerPosition(marker.getPosition());
            });

            // Attempt to get user's location
         
        }

        function updateMarkerPosition(latLng) {
            document.getElementById('latitude').value = latLng.lat();
            document.getElementById('longitude').value = latLng.lng();
        }

        function handleLocationError(browserHasGeolocation) {
            var errorMessage = browserHasGeolocation ?
                                'Error: The Geolocation service failed.' :
                                'Error: Your browser doesn\'t support geolocation.';
            alert(errorMessage);
        }
    </script>