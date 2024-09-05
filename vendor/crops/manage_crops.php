
<!-- vendor\crops\manage_crops.php -->
<?php
require_once('./../../config.php');

// Fetch crop types from cropactivityrecommendation table
$crop_types_query = $conn->query("SELECT DISTINCT CropType FROM cropactivityrecommendation");
$crop_types = [];
while ($row = $crop_types_query->fetch_assoc()) {
    $crop_types[] = $row['CropType'];
}

// Fetch farms of the currently logged-in vendor
$farms_query = $conn->query("SELECT * FROM farm WHERE VendorListId = '{$_settings->userdata('id')}'");
$farms = [];
while ($row = $farms_query->fetch_assoc()) {
    $farms[$row['Id']] = $row['Name'];
}

if (isset($_GET['id']) && $_GET['id'] > 0) {
    $qry = $conn->query("SELECT * FROM `crop` WHERE id = '{$_GET['id']}' AND delete_flag = 0");
    if ($qry->num_rows > 0) {
        foreach ($qry->fetch_assoc() as $k => $v) {
            $$k = $v;
        }
    } else {
?>
        <center>Unknown Crop</center>
        <style>
            #uni_modal .modal-footer {
                display: none
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


<div class="container-fluid">
<!-- Your original form with corrections -->
		<input type="hidden" name ="VendorId" value="<?= $_settings->userdata('id') ?>">


<form action="" id="crop-form" method="POST">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="crop_type" class="control-label">Crop Type</label>
                <input name="Name" id="crop_type" type="text" class="form-control form-control-sm form-control-border" value="<?php echo isset($crop_type) ? $crop_type : ''; ?>" required autocomplete="off" list="crop_types">
                <datalist id="crop_types">
                    <?php foreach ($crop_types as $crop_type) : ?>
                        <option value="<?php echo $crop_type; ?>">
                    <?php endforeach; ?>
                </datalist>
            </div>
            <div class="form-group">
                <label for="crop_variety" class="control-label">Crop Variety</label>
                <input name="Type" id="crop_variety" type="text" class="form-control form-control-sm form-control-border" value="<?php echo isset($crop_variety) ? $crop_variety : ''; ?>" required autocomplete="off">
            </div>
            <div class="form-group">
                <label for="planned_planting_date" class="control-label">Planned Planting Date</label>
                <input name="PlannedPlantingDate" id="planned_planting_date" type="date" class="form-control form-control-sm form-control-border" value="<?php echo isset($planned_planting_date) ? $planned_planting_date : ''; ?>">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="date_planted" class="control-label">Date Planted</label>
                <input name="DatePlanted" id="date_planted" type="date" class="form-control form-control-sm form-control-border" value="<?php echo isset($date_planted) ? $date_planted : ''; ?>">
            </div>
            <div class="form-group">
                <label for="size_of_plantation" class="control-label">Size of Plantation (hectares)</label>
                <input name="SizeOfPlantation" id="size_of_plantation" type="number" step="0.01" class="form-control form-control-sm form-control-border" value="<?php echo isset($size_of_plantation) ? $size_of_plantation : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="description" class="control-label">Description</label>
                <textarea name="Description" id="description" rows="4" class="form-control form-control-sm rounded-0 summernote"><?php echo isset($description) ? html_entity_decode($description) : ''; ?></textarea>
            </div>
        </div>
    </div>
    <div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label for="picture1" class="control-label">Picture 1 (Profile Picture)</label>
            <input type="file" id="picture1" name="Picture1" class="form-control form-control-sm form-control-border" accept="image/png, image/jpeg" onchange="displayImg(this, $('#picture1_img'))">
            <div class="text-center mt-2 border border-gray img-thumbnail">
                <img src="<?= validate_image(isset($Picture1) ? $Picture1 : '') ?>" alt="Picture 1" id="picture1_img" class="img-thumbnail">
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="picture2" class="control-label">Picture 2 (Middle Growth)</label>
            <input type="file" id="picture2" name="Picture2" class="form-control form-control-sm form-control-border" accept="image/png, image/jpeg" onchange="displayImg(this, $('#picture2_img'))">
            <div class="text-center mt-2 border border-gray img-thumbnail">
                <img src="<?= validate_image(isset($Picture2) ? $Picture2 : '') ?>" alt="Picture 2" id="picture2_img" class="img-thumbnail">
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="picture3" class="control-label">Picture 3 (Full Growth)</label>
            <input type="file" id="picture3" name="Picture3" class="form-control form-control-sm form-control-border" accept="image/png, image/jpeg" onchange="displayImg(this, $('#picture3_img'))">
            <div class="text-center mt-2 border border-gray img-thumbnail">
                <img src="<?= validate_image(isset($Picture3) ? $Picture3 : '') ?>" alt="Picture 3" id="picture3_img" class="img-thumbnail">
            </div>
        </div>
    </div>
</div>



    <!-- Picture inputs and other fields here -->
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="farm_id" class="control-label">Farm Name</label>
                <select name="farm_id" id="farm_id" class="form-control form-control-sm form-control-border" required>
                    <?php foreach ($farms as $farm_id => $farm_name) : ?>
                        <option value="<?php echo $farm_id; ?>"><?php echo $farm_name; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="status" class="control-label">Crop Status</label>
            <select name="Status" id="status" class="form-control form-control-sm form-control-border" required>
                <option value="Alive">Alive</option>
                <option value="Diseased">Diseased</option>
                <option value="End of Lifespan">End of Lifespan</option>
                <option value="Unproductive">Unproductive</option>
            </select>
        </div>
    </div>
</div>
    <button type="submit" class="btn btn-primary">Submit</button>
</form>

</div>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Typeahead.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.bundle.min.js"></script>

<script>
     // Function for Picture 1 preview
function displayPicture1(input) {
    displayImg(input, $('#picture1_img'));
}

// Function for Picture 2 preview
function displayPicture2(input) {
    displayImg(input, $('#picture2_img'));
}

// Function for Picture 3 preview
function displayPicture3(input) {
    displayImg(input, $('#picture3_img'));
}

// Common displayImg function to handle image preview
function displayImg(input, targetImg) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            targetImg.attr('src', e.target.result);
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        targetImg.attr('src', '<?= validate_image(isset($image_path) ? $image_path : "") ?>');
    }
}

    $(document).ready(function() {
        $('#uni_modal').on('shown.bs.modal', function() {
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
            })
        })
        $('#uni_modal #crop-form').submit(function(e) {
            e.preventDefault();
            var _this = $(this)
            $('.err-msg').remove();
            if (_this[0].checkValidity() == false) {
                _this[0].reportValidity();
                return false;
            }
            var el = $('<div>')
            el.addClass("alert err-msg")
            el.hide()
            start_loader();
            $.ajax({
                url: _base_url_ + "classes/Master.php?f=save_crop",
                data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
                error: err => {
                    console.error(err)
                    el.addClass('alert-danger').text("An error occurred");
                    _this.prepend(el)
                    el.show('.modal')
                    end_loader();
                },
                success: function(resp) {
                    if (typeof resp == 'object' && resp.status == 'success') {
                        location.reload();
                    } else if (resp.status == 'failed' && !!resp.msg) {
                        el.addClass('alert-danger').text(resp.msg);
                        _this.prepend(el)
                        el.show('.modal')
                    } else {
                        el.text("An error occurred");
                        console.error(resp)
                    }
                    $("html, body").scrollTop(0);
                    end_loader()

                }
            })
        })

        // Autocomplete for Crop Type
        $(document).ready(function() {
    // Autocomplete for Crop Type input
    $("#crop_type").autocomplete({
        source: function(request, response) {
            $.ajax({
                url: "vendor/crops/autocomplete.php", // Adjust the path to your autocomplete.php file
                type: "POST",
                dataType: "json",
                data: {
                    term: request.term // Pass the search term to PHP
                },
                success: function(data) {
                    response(data); // Provide the response data as autocomplete suggestions
                }
            });
        },
        minLength: 1 // Minimum characters before autocomplete starts
    });
});


        // Autocomplete for Crop Variety
        $("#crop_variety").autocomplete({
            source: function(request, response) {
                $.ajax({
                    url:"vendor/crops/autocomplete.php",
                    dataType: "json",
                    data: {
                        term: request.term,
                        type: 'crop_variety',
                        crop_type: $("#crop_type").val()
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            minLength: 1
        });
    });
    function showImagePreview(inputId, previewId) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
            // Display the image preview
            preview.innerHTML = `<img src="${e.target.result}" alt="Preview" style="max-width: 100px; max-height: 100px;">`;
        }
        reader.readAsDataURL(input.files[0]);
        // Display the filename
        preview.innerHTML += `<strong>Filename:</strong> ${input.files[0].name}`;
    } else {
        // If no file is selected, clear the preview
        preview.innerHTML = '';
    }
}

</script>
