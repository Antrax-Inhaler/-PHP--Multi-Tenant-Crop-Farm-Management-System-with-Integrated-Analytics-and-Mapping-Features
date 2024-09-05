<?php
require_once('./../../config.php');

$crop_id = $_GET['id'] ?? null;
if (!$crop_id) {
    die("Crop ID is required.");
}

// Fetch crop details
$qry = $conn->query("SELECT * FROM `crop` WHERE Id = '{$crop_id}'");
if ($qry->num_rows > 0) {
    $crop = $qry->fetch_assoc();
} else {
    die("Unknown Crop");
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
        <input type="hidden" name="CropID" value="<?= $crop_id ?>">

        <!-- Pest/Disease Name with suggestions -->
        <div class="form-group">
            <label for="Name" class="control-label">Name of the Pest/Disease(Leave blank if unknown)</label>
            <input type="text" name="Name" id="Name" class="form-control form-control-sm form-control-border">
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
            <input type="number" name="SizeOfAreaAffected" id="SizeOfAreaAffected" step="any" class="form-control form-control-sm form-control-border" required>
        </div>

        <!-- Status -->
        <div class="form-group">
            <label for="Status" class="control-label">Status</label>
            <select name="Status" id="Status" class="custom-select" required>
                <option value="Existing" <?= isset($status) && $status == "Existing" ? 'selected' : '' ?>>Existing</option>
                <option value="Fixed" <?= isset($status) && $status == "Fixed" ? 'selected' : '' ?>>Fixed</option>
                <option value="Worsened" <?= isset($status) && $status == "Worsened" ? 'selected' : '' ?>>Worsened</option>
            </select>
        </div>

        <!-- Image Inputs with Preview -->
        <div class="row">
            <div class="form-group col-md-6">
            <label for="logo" class="control-label">Primary Image (Required, Click Here) - Close-up of the pest/disease</label>
            <input type="file" id="logo" name="img" class="form-control form-control-sm form-control-border" onchange="displayImg(this, $(this))" accept="image/png, image/jpeg" <?= !isset($id) ? 'required' : '' ?>>
            </div>
            <div class="form-group col-md-6 text-center">
                <img src="<?= validate_image(isset($image_path) ? $image_path : "") ?>" alt="Product Image" id="cimg" class="border border-gray img-thumbnail w-100" style="max-width: 200px;">
            </div>
            <!-- Additional Image Inputs -->
            <div class="form-group col-md-6">
            <label for="logo_2" class="control-label">Optional Image 2 - Mid-range shot showing the extent of the affected area</label>
            <input type="file" id="logo_2" name="img_2" class="form-control form-control-sm form-control-border" onchange="displayImg2(this, $(this))" accept="image/png, image/jpeg">
            </div>
            <div class="form-group col-md-6 text-center">
                <img src="<?= validate_image(isset($image_path_2) ? $image_path_2 : "") ?>" alt="Product Image 2" id="cimg_2" class="border border-gray img-thumbnail w-100" style="max-width: 200px;">
            </div>
            <div class="form-group col-md-6">
            <label for="logo_3" class="control-label">Optional Image 3 - Wide shot of the overall plant/area</label>
            <input type="file" id="logo_3" name="img_3" class="form-control form-control-sm form-control-border" onchange="displayImg3(this, $(this))" accept="image/png, image/jpeg">
            </div>
            <div class="form-group col-md-6 text-center">
                <img src="<?= validate_image(isset($image_path_3) ? $image_path_3 : "") ?>" alt="Product Image 3" id="cimg_3" class="border border-gray img-thumbnail w-100" style="max-width: 200px;">
            </div>
            <div class="form-group col-md-6">
            <label for="logo_4" class="control-label">Optional Image 4 - Detailed shot of symptoms on leaves/branches</label>
            <input type="file" id="logo_4" name="img_4" class="form-control form-control-sm form-control-border" onchange="displayImg4(this, $(this))" accept="image/png, image/jpeg">
            </div>
            <div class="form-group col-md-6 text-center">
                <img src="<?= validate_image(isset($image_path_4) ? $image_path_4 : "") ?>" alt="Product Image 4" id="cimg_4" class="border border-gray img-thumbnail w-100" style="max-width: 200px;">
            </div>
            <div class="form-group col-md-6">
            <label for="logo_5" class="control-label">Optional Image 5 - Any other relevant image</label>
            <input type="file" id="logo_5" name="img_5" class="form-control form-control-sm form-control-border" onchange="displayImg5(this, $(this))" accept="image/png, image/jpeg">
            </div>
            <div class="form-group col-md-6 text-center">
                <img src="<?= validate_image(isset($image_path_5) ? $image_path_5 : "") ?>" alt="Product Image 5" id="cimg_5" class="border border-gray img-thumbnail w-100" style="max-width: 200px;">
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
                    $('#select-pest-disease').data('name', p.name);
                    details.show();
                });
                suggestions.append(button);
            });
        }

        // Initial population of suggestions
        populateSuggestions(pestDiseases);

        // Input event handler for filtering suggestions
        nameInput.on('input', function() {
            const query = $(this).val().toLowerCase();

            if (query.length === 0) {
                populateSuggestions(pestDiseases); // Show all suggestions if input is empty
                return;
            }

            const filtered = pestDiseases.filter(p => p.name.toLowerCase().includes(query));
            populateSuggestions(filtered);
        });

        $('#select-pest-disease').on('click', function() {
            const selectedName = $(this).data('name');
            nameInput.val(selectedName);
            details.hide();
        });

        $('#pest-disease-form').submit(function(e){
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
                url: _base_url_ + "classes/Master.php?f=save_crop_pd",
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
    });

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
</script>
