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
$pest_disease_archive = $conn->query("SELECT pda.id, pda.name, pda.management, pda.symptoms, pda.preventive_measures, pda.curative_measures, pdi.image_path 
                                      FROM pest_disease_archive pda
                                      LEFT JOIN pest_disease_images pdi ON pda.id = pdi.pest_disease_id");
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
            <label for="Name" class="control-label">Name of the Pest/Disease</label>
            <input type="text" name="Name" id="Name" class="form-control form-control-sm form-control-border" required>
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

        <div class="form-group">
            <label for="images" class="control-label">Upload Images</label>
            <input type="file" id="images" name="images[]" class="form-control form-control-sm form-control-border" onchange="displayImages(this)" accept="image/png, image/jpeg" multiple required>
        </div>

        <!-- Image Previews Container -->
        <div id="image-previews" class="row"></div>

        <!-- Size of Area Affected -->
        <div class="form-group">
            <label for="SizeOfAreaAffected" class="control-label">Size of Area Affected</label>
            <input type="number" name="SizeOfAreaAffected" id="SizeOfAreaAffected" step="any" class="form-control form-control-sm form-control-border" required>
        </div>

        <!-- Status -->
        <div class="form-group">
            <label for="Status" class="control-label">Status</label>
            <select name="Status" id="Status" class="custom-select" required>
                <option  <?php echo isset($status) && $status == "Existing" ? 'selected' : '' ?>>Existing</option>
                <option  <?php echo isset($status) && $status == "Fixed" ? 'selected' : '' ?>>Fixed</option>
                <option  <?php echo isset($status) && $status == "Worsened" ? 'selected' : '' ?>>Worsened</option>
            </select>
        </div>
    </form>
</div>

<script>
    function displayImages(input) {
        // Clear previous previews
        $('#image-previews').empty();

        if (input.files && input.files.length > 0) {
            // Loop through each selected file
            for (let i = 0; i < input.files.length; i++) {
                const file = input.files[i];
                const reader = new FileReader();

                reader.onload = function(e) {
                    // Create a new image preview element
                    const imgPreview = `
                        <div class="col-md-3 mb-3">
                            <img src="${e.target.result}" class="img-thumbnail" alt="Image Preview">
                            <p>${file.name}</p>
                        </div>
                    `;
                    $('#image-previews').append(imgPreview);
                }

                // Read the file as a data URL
                reader.readAsDataURL(file);
            }
        }
    }

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
                    $('#pest-disease-image').attr('src', p.image_path);
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

        $(document).ready(function(){
    $('#pest-disease-form').submit(function(e){
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
            url: _base_url_+"classes/Master.php?f=save_crop_disease",
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
                    var cropPestDiseaseId = resp.cropPestDiseaseId;
                    savePestDiseaseImages(cropPestDiseaseId);
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

    function savePestDiseaseImages(cropPestDiseaseId) {
        var formData = new FormData();
        formData.append('cropPestDiseaseId', cropPestDiseaseId);
        formData.append('VendorID', $('input[name="VendorID"]').val());
        formData.append('CropID', $('input[name="CropID"]').val());
        formData.append('pestDiseaseId', $('input[name="pestDiseaseId"]').val());
        $.each($('input[type=file]')[0].files, function(i, file) {
            formData.append('images[]', file);
        });

        $.ajax({
            url: _base_url_ + "classes/Master.php?f=save_pest_disease_images",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            dataType: 'json',
            error: function(xhr, status, error) {
                console.error(error);
                alert("An error occurred while saving images");
            },
            success: function(resp){
                if(typeof resp == 'object' && resp.status == 'success'){
                    location.reload();
                } else {
                    alert("An error occurred while saving images: " + resp.msg);
                    console.error(resp);
                }
            }
        });
    }
});

    });
</script>
