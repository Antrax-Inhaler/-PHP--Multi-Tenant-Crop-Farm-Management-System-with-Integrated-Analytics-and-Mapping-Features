<?php
require_once('./../../config.php');
if(isset($_GET['id']) && $_GET['id'] > 0)
{
    $qry = $conn->query("SELECT * from `harvest` where Id = '{$_GET['id']}'");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
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
<div class="container-fluid">
    <form action="" id="farm-form">
        <input type="hidden" name="Id" value="<?php echo isset($Id) ? $Id : '' ?>">
        <div class="form-group">
            <label for="Name" class="control-label">Farm Name</label>
            <input name="Name" id="Name" type="text" class="form-control form-control-sm form-control-border" value="<?php echo isset($Name) ? $Name : ''; ?>" required>
        </div>
        <div class="form-group">
            <label for="Latitude" class="control-label">Latitude</label>
            <input name="Latitude" id="Latitude" type="number" step="0.00000001" class="form-control form-control-sm form-control-border" value="<?php echo isset($Latitude) ? $Latitude : ''; ?>" required>
        </div>
        <div class="form-group">
            <label for="Longitude" class="control-label">Longitude</label>
            <input name="Longitude" id="Longitude" type="number" step="0.00000001" class="form-control form-control-sm form-control-border" value="<?php echo isset($Longitude) ? $Longitude : ''; ?>" required>
        </div>
        <div class="form-group">
            <label for="Size" class="control-label">Size (hectares)</label>
            <input name="Size" id="Size" type="number" step="0.01" class="form-control form-control-sm form-control-border" value="<?php echo isset($Size) ? $Size : ''; ?>" required>
        </div>
        <div class="form-group">
            <label for="Description" class="control-label">Description</label>
            <textarea name="Description" id="Description" rows="4" class="form-control form-control-sm rounded-0" required><?php echo isset($Description) ? $Description : ''; ?></textarea>
        </div>
        <div class="form-group">
            <label for="Image" class="control-label">Image</label>
            <input name="Image" id="Image" type="file" class="form-control form-control-sm form-control-border">
            <?php if(isset($Image) && !empty($Image)): ?>
                <img src="<?php echo $Image; ?>" alt="Farm Image" class="img-thumbnail mt-2" width="150">
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="Status" class="control-label">Status</label>
            <select name="Status" id="Status" class="custom-select select" required>
                <option value="1" <?php echo isset($Status) && $Status == 1 ? 'selected' : '' ?>>Active</option>
                <option value="0" <?php echo isset($Status) && $Status == 0 ? 'selected' : '' ?>>Inactive</option>
            </select>
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
