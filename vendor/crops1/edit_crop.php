<?php
require_once('./../../config.php');
if(isset($_GET['id']) && $_GET['id'] > 0)
{    $qry = $conn->query("SELECT * from `crop` where Id = '{$_GET['id']}'");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
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
}
?>
<div class="container-fluid">
    <form action="" id="crop-form">
    <input type="hidden" name ="Id" value="<?php echo isset($Id) ? $Id : '' ?>">
    <input type="hidden" name ="VendorId" value="<?= $_settings->userdata('id') ?>">
    <input type="hidden" name ="FarmId" value="<?php echo isset($FarmId) ? $FarmId : ''; ?>">
    <!-- Display the Farm ID -->
            <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="Name" class="control-label">Crop Name</label>
                    <input name="Name" id="Name" type="text" class="form-control form-control-sm form-control-border" value="<?php echo isset($Name) ? $Name : ''; ?>" required>
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
