<?php
require_once('./../../config.php');

if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from croppestdisease where id = '{$_GET['id']}'  ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
    } else {
?>
		<center>Unknown Pest or Disease</center>
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

// Fetch pest and disease archive for suggestions
$pest_disease_archive = $conn->query("SELECT pda.id, pda.name, pda.management, pda.symptoms, pda.preventive_measures, pda.curative_measures, cpd.Image1 
                                      FROM pest_disease_archive pda
                                      LEFT JOIN croppestdisease cpd ON pda.name = cpd.name");
$pest_diseases = [];
while ($row = $pest_disease_archive->fetch_assoc()) {
    $pest_diseases[] = $row;
}
?>

<div class="container-fluid">
    <form action="" id="pest-disease-form" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="Id" value="<?php echo isset($Id) ? $Id : ''; ?>" required>

    <input type="hidden" name="CropID" value="<?php echo isset($CropID) ? $CropID : ''; ?>" required>

        <!-- Pest/Disease Name with suggestions -->
        <div class="form-group">
            <label for="Name" class="control-label">Name of the Pest/Disease (Leave blank if unknown)</label>
            <input type="text" name="Name" id="Name" class="form-control form-control-sm form-control-border" value="<?php echo isset($Name) ? $Name : ''; ?>">
            <div id="suggestions"></div>
        </div>

        <!-- Container for selected pest/disease details -->
        <div id="pest-disease-details" style="display: none;">
            <div class="card">
                <div class="card-body">
                    <h5 id="pest-disease-name"></h5>
                    <p><strong>Management:</strong> <span id="pest-disease-management"></span></p>
                    <p><strong>Symptoms:</strong> <span id="pest-disease-symptoms"></span></p>
                    <p><strong>Preventive Measures:</strong> <span id="pest-disease-preventive"></span></p>
                    <p><strong>Curative Measures:</strong> <span id="pest-disease-curative"></span></p>
                    <img id="pest-disease-image" src="" alt="Pest/Disease Image" class="img-thumbnail">
                    <button type="button" id="select-pest-disease" class="btn btn-primary">Select</button>
                </div>
            </div>
        </div>
    
        <!-- Size of Area Affected -->
        <div class="form-group">
            <label for="SizeOfAreaAffected" class="control-label">Size of Area Affected</label>
            <input type="number" name="SizeOfAreaAffected" id="SizeOfAreaAffected" step="any" class="form-control form-control-sm form-control-border" value="<?php echo isset($SizeOfAreaAffected) ? $SizeOfAreaAffected : ''; ?>" required>
        </div>

        <!-- Status -->
        <div class="form-group">
            <label for="Status" class="control-label">Status</label>
            <select name="Status" id="Status" class="custom-select" required>
                <option value="Existing" <?= isset($Status) && $Status == "Existing" ? 'selected' : '' ?>>Existing</option>
                <option value="Fixed" <?= isset($Status) && $Status == "Fixed" ? 'selected' : '' ?>>Fixed</option>
                <option value="Worsened" <?= isset($Status) && $Status == "Worsened" ? 'selected' : '' ?>>Worsened</option>
            </select>
        </div>

        <!-- Image Inputs with Preview -->
        <div class="row">
        <div class="form-group col-md-6">
        <label for="logo" class="control-label">Primary Image (Required, Click Here) - Close-up of the pest/disease</label>
        <input type="file" id="logo" name="img" class="form-control form-control-sm form-control-border" onchange="displayImg(this, $(this))" accept="image/png, image/jpeg" <?= !isset($id) ? 'required' : '' ?>>
    </div>
    <div class="form-group col-md-6 text-center">
        <img src="<?= validate_image(isset($Image1) ? $Image1 : "") ?>" alt="Primary Image" id="cimg" class="border border-gray img-thumbnail w-100" style="max-width: 200px;">
    </div>
    <div class="form-group col-md-6">
        <label for="logo_2" class="control-label">Optional Image 2 - Mid-range shot showing the extent of the affected area</label>
        <input type="file" id="logo_2" name="img_2" class="form-control form-control-sm form-control-border" onchange="displayImg2(this, $(this))" accept="image/png, image/jpeg">
    </div>
    <div class="form-group col-md-6 text-center">
        <img src="<?= validate_image(isset($Image2) ? $Image2 : "") ?>" alt="Optional Image 2" id="cimg_2" class="border border-gray img-thumbnail w-100" style="max-width: 200px;">
    </div>
    <div class="form-group col-md-6">
        <label for="logo_3" class="control-label">Optional Image 3 - Wide shot of the overall plant/area</label>
        <input type="file" id="logo_3" name="img_3" class="form-control form-control-sm form-control-border" onchange="displayImg3(this, $(this))" accept="image/png, image/jpeg">
    </div>
    <div class="form-group col-md-6 text-center">
        <img src="<?= validate_image(isset($Image3) ? $Image3 : "") ?>" alt="Optional Image 3" id="cimg_3" class="border border-gray img-thumbnail w-100" style="max-width: 200px;">
    </div>
    <div class="form-group col-md-6">
        <label for="logo_4" class="control-label">Optional Image 4 - Detailed shot of symptoms on leaves/branches</label>
        <input type="file" id="logo_4" name="img_4" class="form-control form-control-sm form-control-border" onchange="displayImg4(this, $(this))" accept="image/png, image/jpeg">
    </div>
    <div class="form-group col-md-6 text-center">
        <img src="<?= validate_image(isset($Image4) ? $Image4 : "") ?>" alt="Optional Image 4" id="cimg_4" class="border border-gray img-thumbnail w-100" style="max-width: 200px;">
    </div>
    <div class="form-group col-md-6">
        <label for="logo_5" class="control-label">Optional Image 5 - Any other relevant image</label>
        <input type="file" id="logo_5" name="img_5" class="form-control form-control-sm form-control-border" onchange="displayImg5(this, $(this))" accept="image/png, image/jpeg">
    </div>
    <div class="form-group col-md-6 text-center">
        <img src="<?= validate_image(isset($Image5) ? $Image5 : "") ?>" alt="Optional Image 5" id="cimg_5" class="border border-gray img-thumbnail w-100" style="max-width: 200px;">
    </div>
        </div>
    </form>
</div>

<script>
    $(document).ready(function(){
        const pestDiseases = <?= json_encode($pest_diseases) ?>;
        const suggestions = $('#suggestions');
        const details = $('#pest-disease-details');
        const nameInput = $('#Name');

        // Function to populate suggestions
        function populateSuggestions(data) {
            suggestions.empty();
            details.hide();

            data.forEach(p => {
                const button = $('<button>').text(p.name).addClass('list-group-item list-group-item-action').attr('type', 'button');
                button.on('click', function() {
                    $('#pest-disease-name').text(p.name);
                    $('#pest-disease-management').text(p.management);
                    $('#pest-disease-symptoms').text(p.symptoms);
                    $('#pest-disease-preventive').text(p.preventive_measures);
                    $('#pest-disease-curative').text(p.curative_measures);
                    $('#pest-disease-image').attr('src', p.Image1);
                    details.show();
                });
                suggestions.append(button);
            });
        }

        // Handle input changes for suggestions
        nameInput.on('input', function() {
            const value = $(this).val().toLowerCase();
            const filtered = pestDiseases.filter(p => p.name.toLowerCase().includes(value));
            populateSuggestions(filtered);
        });

        // Handle select button click
        $('#select-pest-disease').on('click', function() {
            const selectedName = $('#pest-disease-name').text();
            nameInput.val(selectedName);
            details.hide();
        });
    });

    // Image preview functions
    function displayImg(input, _this) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#cimg').attr('src', e.target.result);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
    function displayImg2(input, _this) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#cimg_2').attr('src', e.target.result);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
    function displayImg3(input, _this) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#cimg_3').attr('src', e.target.result);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
    function displayImg4(input, _this) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#cimg_4').attr('src', e.target.result);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
    function displayImg5(input, _this) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#cimg_5').attr('src', e.target.result);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    $('#pest-disease-form').submit(function(e){
        e.preventDefault();
        var _this = $(this)
        var el = $('<div>')
        el.addClass('alert alert-danger')
        el.hide()
        if($('[name="img"]').val() != ''){
            var file = $('[name="img"]').val().split('.');
            if(file[1] != 'jpg' && file[1] != 'jpeg' && file[1] != 'png'){
                el.text('Please choose a valid image file.')
                _this.prepend(el)
                el.show('slow')
                $('html,body').animate({scrollTop:0},'fast')
                return false;
            }
        }
        start_loader();
        $.ajax({
            url:_base_url_+'classes/Master.php?f=save_crop_pd',
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            dataType: 'json',
            error: err=>{
                console.log(err)
                alert_toast("An error occurred",'error')
                end_loader();
            },
            success:function(resp){
                if(resp.status == 'success'){
                    alert_toast("Data successfully saved",'success')
                    end_loader();
                    $('.modal').modal('hide')
                    location.reload()
                }else{
                    el.text(resp.msg)
                    _this.prepend(el)
                    el.show('slow')
                    $('html,body').animate({scrollTop:0},'fast')
                    end_loader()
                }
            }
        })
    })

</script>
