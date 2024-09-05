<?php
require_once('./../../config.php');

if(isset($_GET['id']) && $_GET['id'] > 0) {
    $qry = $conn->query("SELECT * from `crop_activity` where id = '{$_GET['id']}'");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
    } else {
        ?>
        <center>Unknown Activity</center>
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
    <form id="edit-activity-form">
        <input type="hidden" name="id" value="<?php echo isset($id) ? $id : ''; ?>">

        <div class="form-group">
            <label for="activity_date" class="control-label">Activity Date</label>
            <input type="date" name="activity_date" id="activity_date" class="form-control form-control-sm form-control-border" value="<?php echo isset($activity_date) ? $activity_date : ''; ?>" required>
        </div>

        <div class="form-group">
            <label for="activity_type" class="control-label">Activity Type</label>
            <input type="text" name="activity_type" id="activity_type" class="form-control form-control-sm form-control-border" value="<?php echo isset($activity_type) ? $activity_type : ''; ?>" required>
        </div>

        <div class="form-group">
            <label for="description" class="control-label">Description</label>
            <textarea name="description" id="description" class="form-control form-control-sm form-control-border" required><?php echo isset($description) ? $description : ''; ?></textarea>
        </div>
    </form>
</div>

<script>
$(document).ready(function(){
    // Form submission
    $('#edit-activity-form').submit(function(e){
        e.preventDefault();
        var _this = $(this);
        $('.err-msg').remove();
        if (_this[0].checkValidity() === false) {
            _this[0].reportValidity();
            return false;
        }
        var el = $('<div>');
        el.addClass("alert err-msg alert-danger");
        el.hide();
        start_loader();
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=save_activity",
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
            success: function(resp) {
                if (typeof resp === 'object' && resp.status === 'success') {
                    location.reload();
                } else if (resp.status === 'failed' && !!resp.msg) {
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
