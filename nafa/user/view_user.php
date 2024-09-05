<?php
require_once('./../../config.php');
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * FROM `users` WHERE id = '{$_GET['id']}'");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k = $v;
        }
    } else {
?>
    <center>Unknown User</center>
    <style>
        #uni_modal .modal-footer{
            display: none;
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
<style>
    #uni_modal .modal-footer{
        display: none;
    }
    .user-img{
        width: 100%;
        height: auto;
        max-height: 10em;
        object-fit: cover;
        object-position: center center;
    }
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-3 border bg-gradient-primary"><span class="">User ID</span></div>
        <div class="col-9 border"><span class="font-weight-bolder"><?= isset($id) ? $id : '' ?></span></div>
        <div class="col-3 border bg-gradient-primary"><span class="">Name</span></div>
        <div class="col-9 border"><span class="font-weight-bolder"><?= isset($name) ? $name : '' ?></span></div>
        <div class="col-3 border bg-gradient-primary"><span class="">Username</span></div>
        <div class="col-9 border"><span class="font-weight-bolder"><?= isset($username) ? $username : '' ?></span></div>
        <div class="col-3 border bg-gradient-primary"><span class="">Email</span></div>
        <div class="col-9 border"><span class="font-weight-bolder"><?= isset($email) ? $email : '' ?></span></div>
        <div class="col-3 border bg-gradient-primary"><span class="">Avatar</span></div>
        <div class="col-9 border text-center">
            <img src="<?= validate_image(isset($avatar) ? $avatar : '') ?>" alt="User Avatar" class="border border-gray img-thumbnail user-img">
        </div>
        <div class="col-3 border bg-gradient-primary"><span class="">Status</span></div>
        <div class="col-9 border"><span class="font-weight-bolder">
            <?php
            $status = isset($status) ? $status : '';
            switch($status){
                case 1:
                    echo '<span class="badge badge-success bg-gradient-success px-3 rounded-pill">Active</span>';
                    break;
                case 0:
                    echo '<span class="badge badge-danger bg-gradient-danger px-3 rounded-pill">Inactive</span>';
                    break;
                default:
                    echo '<span class="badge badge-light bg-gradient-light border px-3 rounded-pill">N/A</span>';
                    break;
            }
            ?>
        </span></div>
    </div>
    <div class="clear-fix mb-2"></div>
    <div class="text-right">
        <button class="btn btn-default bg-gradient-dark text-light btn-sm btn-flat" type="button" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
    </div>
</div>
<script>
    $(function(){
        $('#update_status').click(function(){
            uni_modal("Update User Status - <b><?= isset($name) ? $name : '' ?></b>","user/update_status.php?id=<?= isset($id) ? $id : '' ?>")
        })
    })
</script>
