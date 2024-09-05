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
} else {
    $FarmId = $farm_id; // Assign $farm_id to $FarmId if it exists
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

        <div class="form-group">
            <label for="Paid" class="control-label">Paid</label>
            <select name="Paid" id="Paid" class="custom-select" required>
                <option value="1">Yes</option>
                <option value="0">No</option>
            </select>
        </div>

        <div class="text-right">
            <button class="btn btn-primary">Save</button>
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
