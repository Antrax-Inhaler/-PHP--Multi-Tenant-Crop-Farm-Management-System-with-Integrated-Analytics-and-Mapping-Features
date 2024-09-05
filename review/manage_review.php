<?php
require_once('./../config.php');
if(isset($_GET['id']) && $_GET['id'] > 0)
{
    $qry = $conn->query("SELECT * from `review` where id = '{$_GET['id']}'");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
    } else {
?>

<center>Unknown Review</center>
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
    $order_id = isset($_GET['order_id']) ? $_GET['order_id'] : '';
    $product_id = isset($_GET['product_id']) ? $_GET['product_id'] : '';
}
?>
<div class="container-fluid">
    <form action="" id="review-form">
        <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
        <input type="hidden" name="order_id" value="<?php echo isset($order_id) ? $order_id : '' ?>">
        <input type="hidden" name="product_id" value="<?php echo isset($product_id) ? $product_id : '' ?>">
        <input type="hidden" name="client_id" value="<?php echo isset($client_id) ? $client_id : $_settings->userdata('id'); ?>">
        <div class="form-group">
            <label for="rating" class="control-label">Rating</label>
            <select name="rating" id="rating" class="form-control form-control-sm form-control-border" required>
                <option value="1" <?php echo isset($rating) && $rating == 1 ? 'selected' : ''; ?>>1⭐ - Poor</option>
                <option value="2" <?php echo isset($rating) && $rating == 2 ? 'selected' : ''; ?>>2⭐ - Fair</option>
                <option value="3" <?php echo isset($rating) && $rating == 3 ? 'selected' : ''; ?>>3⭐ - Good</option>
                <option value="4" <?php echo isset($rating) && $rating == 4 ? 'selected' : ''; ?>>4⭐ - Very Good</option>
                <option value="5" <?php echo isset($rating) && $rating == 5 ? 'selected' : ''; ?>>5⭐ - Excellent</option>
            </select>
        </div>
        <div class="form-group">
            <label for="comment" class="control-label">Comment</label>
            <textarea name="comment" id="comment" rows="4" class="form-control form-control-sm rounded-0"><?php echo isset($comment) ? $comment : ''; ?></textarea>
        </div>
    </form>
</div>

<script>
    $(document).ready(function(){
        $('#review-form').submit(function(e){
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
                url: _base_url_+"classes/Master.php?f=save_review",
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

<style>
    .rating {
        display: flex;
        flex-direction: row-reverse;
        justify-content: center;
    }
    .rating input {
        display: none;
    }
    .rating label {
        position: relative;
        width: 1em;
        font-size: 2rem;
        color: #FFD700;
        cursor: pointer;
    }
    .rating label::before {
        content: "\2605";
        position: absolute;
        opacity: 0;
    }
    .rating label:hover:before,
    .rating label:hover ~ label:before,
    .rating input:checked ~ label:before {
        opacity: 1;
    }
</style>
