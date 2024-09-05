<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>

<style>
    .img-avatar{
        width:45px;
        height:45px;
        object-fit:cover;
        object-position:center center;
        border-radius:100%;
    }
</style>

<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">List of Crops</h3>
    </div>
    <div class="card-body">
        <div class="container-fluid">
            <div class="container-fluid">
                <table class="table table-bordered table-striped">
                    <colgroup>
                        <col width="10%">
                        <col width="10%">
                        <col width="15%">
                        <col width="20%">
                        <col width="15%">
                        <col width="10%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Vendor</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Planned Planting Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $i = 1;
                        $user_id = $_settings->userdata('id');
                        $crop_query = $conn->query("SELECT * FROM `crop` WHERE VendorId IN (SELECT id FROM `vendor_list` WHERE user_id = '{$user_id}' AND delete_flag = 0) ORDER BY `Name` ASC");
                        while($row = $crop_query->fetch_assoc()):
                        ?>
                        <tr>
                            <td class="text-center"><?php echo $i++; ?></td>
                            <td><?php echo $row['VendorId']; ?></td>
                            <td><?php echo ucwords($row['Name']); ?></td>
                            <td><?php echo ucwords($row['Type']); ?></td>
                            <td><?php echo date("Y-m-d", strtotime($row['PlannedPlantingDate'])); ?></td>
                            <td align="center">
                                <button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
                                    Action
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <div class="dropdown-menu" role="menu">
                                    <a class="dropdown-item" href="?page=crops/manage_crop&id=<?php echo $row['Id']; ?>"><span class="fa fa-edit text-primary"></span> Edit</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?php echo $row['Id']; ?>"><span class="fa fa-trash text-danger"></span> Delete</a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        $('.delete_data').click(function(){
            _conf("Are you sure to delete this crop permanently?","delete_crop",[$(this).attr('data-id')])
        })
        $('.table').dataTable();
    })

    function delete_crop($id){
        start_loader();
        $.ajax({
            url:_base_url_+"classes/Crop.php?f=delete_crop",
            method:"POST",
            data:{id: $id},
            dataType:"json",
            error:err=>{
                console.log(err)
                alert_toast("An error occurred.",'error');
                end_loader();
            },
            success:function(resp){
                if(typeof resp == 'object' && resp.status == 'success'){
                    location.reload();
                }else{
                    alert_toast("An error occurred.",'error');
                    end_loader();
                }
            }
        })
    }
</script>
