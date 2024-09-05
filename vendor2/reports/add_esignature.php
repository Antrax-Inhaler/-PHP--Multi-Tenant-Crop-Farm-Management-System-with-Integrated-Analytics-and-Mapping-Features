<?php 
require_once('./../../config.php');
$user = $conn->query("SELECT * FROM vendor_list where id ='".$_settings->userdata('id')."'");
foreach($user->fetch_array() as $k =>$v){
    $$k = $v;
}
?>
<?php if($_settings->chk_flashdata('success')): ?>
<script>
    alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>

<style>
    #esignature_img {
        width: 200px;
        height: 200px;
        object-fit: scale-down;
        object-position: center center;
    }
</style>

<div class="content py-3"></div>
<div class="card card-outline rounded-0 card-primary shadow">
    <div class="card-body">
        <div class="container-fluid">
            <div id="msg"></div>
            <form action="" id="manage-user" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $_settings->userdata('id') ?>">
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="esignature" class="control-label">E-Signature</label>
                        <input type="file" id="esignature" name="esignature" class="form-control form-control-sm form-control-border" onchange="displaySignature(this,$(this))" accept="image/png, image/jpeg" required>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6 text-center">
                        <img src="<?= validate_image(isset($esignature) ? $esignature : "") ?>" alt="E-Signature" id="esignature_img" class="border border-gray img-thumbnail">
                    </div>
                </div>
                <div class="card-footer">
                    <div class="col-md-12">
                        <div class="row">
                            <button class="btn btn-sm btn-primary" form="manage-user">Update</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function displaySignature(input, _this) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#esignature_img').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        } else {
            $('#esignature_img').attr('src', "<?= validate_image(isset($esignature) ? $esignature : "") ?>");
        }
    }

    $(function(){
        $('#manage-user').submit(function(e){
            e.preventDefault();
            var _this = $(this);
            $('.err-msg').remove();
            var el = $('<div>');
            el.addClass("alert err-msg");
            el.hide();
            if(_this[0].checkValidity() == false){
                _this[0].reportValidity();
                return false;
            }
            start_loader();
            $.ajax({
                url: _base_url_ + "classes/Master.php?f=save_esignature",
                data: new FormData(_this[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                dataType: 'json',
                error: function(err) {
                    console.error(err);
                    el.addClass('alert-danger').text("An error occurred");
                    _this.prepend(el);
                    el.show('.modal');
                    end_loader();
                },
                success: function(resp) {
                    if (typeof resp == 'object' && resp.status == 'success') {
                        location.reload();
                    } else if (resp.status == 'failed' && !!resp.msg) {
                        el.addClass('alert-danger').text(resp.msg);
                        _this.prepend(el);
                        el.show('.modal');
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

<script>
    const canvas = document.getElementById('signature-pad');
const ctx = canvas.getContext('2d');
let isDrawing = false;
let lastX = 0;
let lastY = 0;

function draw(e) {
    if (!isDrawing) return;

    ctx.beginPath();
    ctx.moveTo(lastX, lastY);
    ctx.lineTo(e.offsetX, e.offsetY);
    ctx.stroke();
    [lastX, lastY] = [e.offsetX, e.offsetY];
}

canvas.addEventListener('mousedown', (e) => {
    isDrawing = true;
    [lastX, lastY] = [e.offsetX, e.offsetY];
});

canvas.addEventListener('mousemove', draw);
canvas.addEventListener('mouseup', () => isDrawing = false);
canvas.addEventListener('mouseout', () => isDrawing = false);

document.getElementById('clear').addEventListener('click', () => {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
});

document.getElementById('save').addEventListener('click', () => {
    const dataURL = canvas.toDataURL('image/png');
    const link = document.createElement('a');
    link.href = dataURL;
    link.download = 'signature.png';
    link.click();
});

</script>
