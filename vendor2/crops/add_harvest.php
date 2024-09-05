<?php
require_once('./../../config.php');

$crop_id = $_GET['id'] ?? null;
if (!$crop_id) {
    die("Crop ID is required.");
}

?>
<div class="container-fluid">
    <form action="" id="harvest-form">
        <input type="hidden" name="CropId" value="<?= $crop_id ?>">

        <div class="form-group">
            <label for="HarvestedDate" class="control-label">Harvested Date</label>
            <input type="date" name="HarvestedDate" id="HarvestedDate" class="form-control form-control-sm form-control-border" required>
        </div>

        <div class="form-group">
            <label for="AmountOfHarvest" class="control-label">Amount of Harvest (kg)</label>
            <input type="number" name="AmountOfHarvest" id="AmountOfHarvest" step="any" class="form-control form-control-sm form-control-border" required>
        </div>
    </form>
</div>

<script>
    $(document).ready(function(){
        $('#harvest-form').submit(function(e){
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
                url: _base_url_+"classes/Master.php?f=save_harvest",
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
