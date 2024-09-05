<?php
require_once('./../../config.php');
if(isset($_GET['id']) && $_GET['id'] > 0)
{
    $qry = $conn->query("SELECT * from `pest_disease_archive` where id = '{$_GET['id']}'");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
    } else {
?>

<center>Unknown Pest/Disease Archive</center>
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
    <form action="" id="pest-disease-archive-form">
        <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
        <div class="form-group">
            <label for="name" class="control-label">Name</label>
            <input name="name" id="name" type="text" class="form-control form-control-sm form-control-border" value="<?php echo isset($name) ? $name : ''; ?>" required>
        </div>
        <div class="form-group">
            <label for="management" class="control-label">Management</label>
            <textarea name="management" id="management" rows="4" class="form-control form-control-sm rounded-0" required><?php echo isset($management) ? $management : ''; ?></textarea>
        </div>
        <div class="form-group">
            <label for="symptoms" class="control-label">Symptoms</label>
            <textarea name="symptoms" id="symptoms" rows="4" class="form-control form-control-sm rounded-0" required><?php echo isset($symptoms) ? $symptoms : ''; ?></textarea>
        </div>
        <div class="form-group">
            <label for="preventive_measures" class="control-label">Preventive Measures</label>
            <textarea name="preventive_measures" id="preventive_measures" rows="4" class="form-control form-control-sm rounded-0" required><?php echo isset($preventive_measures) ? $preventive_measures : ''; ?></textarea>
        </div>
        <div class="form-group">
            <label for="curative_measures" class="control-label">Curative Measures</label>
            <textarea name="curative_measures" id="curative_measures" rows="4" class="form-control form-control-sm rounded-0" required><?php echo isset($curative_measures) ? $curative_measures : ''; ?></textarea>
        </div>
    </form>
</div>

<script>
    $(document).ready(function(){
        $('#pest-disease-archive-form').submit(function(e){
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
                url: _base_url_+"classes/Master.php?f=save_pest_disease_archive",
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
