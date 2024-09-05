<?php
require_once('./../../config.php');
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `product_list` where id = '{$_GET['id']}' and delete_flag = 0 ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
    }else{
?>
		<center>Unknown Shop Type</center>
		<style>
			#uni_modal .modal-footer{
				display:none
			}
		</style>
		<div class="text-right">
			<button class="btn btndefault bg-gradient-dark btn-flat" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
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
        <input type="hidden" name ="id" value="<?php echo isset($id) ? $id : '' ?>">
        <input type="hidden" name ="vendor_id" value="<?= $_settings->userdata('id') ?>">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="name" class="control-label">Name</label>
                    <input name="name" id="name" type="text" class="form-control form-control-sm form-control-border" value="<?php echo isset($name) ? $name : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="category_id" class="control-label">Category</label>
                    <select type="text" id="category_id" name="category_id" class="form-control form-control-sm form-control-border select2" required>
                        <option value="" disabled <?= !isset($category_id) ? 'selected' : "" ?>></option>
                        <?php 
                        $categories = $conn->query("SELECT * FROM `category_list` where delete_flag = 0 and `status` = 1 and vendor_id= '{$_settings->userdata('id')}' ".(isset($category_id) ? " or id = '{$category_id}' " : '')." order by `name` asc ");
                        while($row = $categories->fetch_assoc()):
                        ?>
                        <option value="<?= $row['id'] ?>" <?= isset($category_id) && $category_id == $row['id'] ? 'selected': '' ?>><?= $row['name'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="description" class="control-label">Description</label>
                    <textarea name="description" id="description" rows="4" class="form-control form-control-sm rounded-0 summernote" required><?php echo isset($description) ? html_entity_decode($description) : ''; ?></textarea>
                </div>
                <div class="form-group">
                    <label for="logo" class="control-label">Thumbnail Image (Click Here)</label>
                    <input type="file" id="logo" name="img" class="form-control form-control-sm form-control-border" onchange="displayImg(this,$(this))" accept="image/png, image/jpeg" <?= !isset($id) ? 'required' : '' ?>>
                </div>
                <div class="form-group col-md-6 text-center">
                    <img src="<?= validate_image(isset($image_path) ? $image_path : "") ?>" alt="Product Image" id="cimg" class="border border-gray img-thumbnail">
                </div>
                <!-- Additional Image Inputs -->
                <div class="form-group">
                    <label for="logo_2" class="control-label">Additional Product Image 2 (Click Here)</label>
                    <input type="file" id="logo_2" name="img_2" class="form-control form-control-sm form-control-border" onchange="displayImg2(this,$(this))" accept="image/png, image/jpeg">
                </div>
                <div class="form-group col-md-6 text-center">
                    <img src="<?= validate_image(isset($image_path_2) ? $image_path_2 : "") ?>" alt="Product Image 2" id="cimg_2" class="border border-gray img-thumbnail">
                </div>
                <div class="form-group">
                    <label for="logo_3" class="control-label">Additional Product Image 3 (Click Here)</label>
                    <input type="file" id="logo_3" name="img_3" class="form-control form-control-sm form-control-border" onchange="displayImg3(this,$(this))" accept="image/png, image/jpeg">
                </div>
                <div class="form-group col-md-6 text-center">
                    <img src="<?= validate_image(isset($image_path_3) ? $image_path_3 : "") ?>" alt="Product Image 3" id="cimg_3" class="border border-gray img-thumbnail">
                </div>
                <div class="form-group">
                    <label for="logo_4" class="control-label">Additional Product Image 4 (Click Here)</label>
                    <input type="file" id="logo_4" name="img_4" class="form-control form-control-sm form-control-border" onchange="displayImg4(this,$(this))" accept="image/png, image/jpeg">
                </div>
                <div class="form-group col-md-6 text-center">
                    <img src="<?= validate_image(isset($image_path_4) ? $image_path_4 : "") ?>" alt="Product Image 4" id="cimg_4" class="border border-gray img-thumbnail">
                </div>
                <div class="form-group">
                    <label for="logo_5" class="control-label">Additional Product Image 5 (Click Here)</label>
                    <input type="file" id="logo_5" name="img_5" class="form-control form-control-sm form-control-border" onchange="displayImg5(this,$(this))" accept="image/png, image/jpeg">
                </div>
                <div class="form-group col-md-6 text-center">
                    <img src="<?= validate_image(isset($image_path_5) ? $image_path_5 : "") ?>" alt="Product Image 5" id="cimg_5" class="border border-gray img-thumbnail">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="price" class="control-label">Select Your Product Location</label>
                    <div id="map"></div>
                    <input type="number" id="latitude" name="latitude" value="<?php echo isset($latitude) ? $latitude : ''; ?>">
                    <input type="number" id="longitude" name="longitude" value="<?php echo isset($longitude) ? $longitude : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="price" class="control-label">Cost</label>
                    <input name="price" id="price" type="number" step="any" class="form-control form-control-sm form-control-border" value="<?php echo isset($price) ? $price : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="stock_amount" class="control-label">Amount Stock</label>
                    <input name="stock_amount" id="stock_amount" type="number" step="any" class="form-control form-control-sm form-control-border" value="<?php echo isset($stock_amount) ? $stock_amount : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="status" class="control-label">Status</label>
                    <select name="status" id="status" class="custom-select select" required>
                        <option value="1" <?php echo isset($status) && $status == 1 ? 'selected' : '' ?>>Active</option>
                        <option value="0" <?php echo isset($status) && $status == 0 ? 'selected' : '' ?>>Inactive</option>
                    </select>
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