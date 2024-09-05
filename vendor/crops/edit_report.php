<?php
require_once('./../../config.php');

if(isset($_GET['id']) && $_GET['id'] > 0) {
    $qry = $conn->query("SELECT * FROM `pestanddiseasereport` WHERE id = '{$_GET['id']}'");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
    } else {
        ?>
        <center>Unknown Report</center>
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
    ?>
    <center>Report ID is required</center>
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
?>

<div class="container-fluid">
    <form id="edit-report-form">
        <input type="hidden" name="id" value="<?php echo isset($id) ? $id : ''; ?>">
        <input type="hidden" name="pestordisease_id" value="<?php echo isset($pestordisease_id) ? $pestordisease_id : ''; ?>">

        <!-- Visible Description -->
        <div class="form-group">
            <label for="Description" class="control-label">Description</label>
            <textarea name="description" id="Description" rows="4" class="form-control form-control-sm form-control-border" required><?php echo isset($description) ? $description : ''; ?></textarea>
        </div>

        <!-- Status -->
        <div class="form-group">
            <label for="Status" class="control-label">Status</label>
            <select name="status" id="Status" class="custom-select" required>
                <option value="0" <?php echo isset($status) && $status == 0 ? 'selected' : ''; ?>>Pending</option>
                <option value="1" <?php echo isset($status) && $status == 1 ? 'selected' : ''; ?>>Resolved</option>
            </select>
        </div>
    </form>
</div>

<script>
$(document).ready(function(){
    $('#edit-report-form').submit(function(e){
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
            url: _base_url_ + "classes/Master.php?f=save_pest_and_disease_report",
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
