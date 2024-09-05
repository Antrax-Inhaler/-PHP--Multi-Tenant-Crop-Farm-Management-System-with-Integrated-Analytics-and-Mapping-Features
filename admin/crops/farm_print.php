<?php if($_settings->chk_flashdata('success')): ?>
<script>
    alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>

<style>
    .img-farm {
        width: 100px;
        height: 100px;
        object-fit: cover;
        object-position: center center;
    }
</style>

<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">List of Farms</h3>
        <div class="card-tools">
            <a href="?page=farm/manage_farm" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span> Add New</a>
            
        </div>
        <button class="btn btn-success btn-sm btn-block col-md-2 float-right" type="button" id="print_btn">
                    <span class="fa fa-print"></span> Print
                </button>
    </div>
    <div class="card-body">
        <div class="container-fluid">
            <table class="table table-bordered table-striped">
                <colgroup>
                    <col width="5%">
                    <col width="15%">
                    <col width="20%">
                    <col width="25%">
                    <col width="15%">
                    <col width="10%">
                    <col width="10%">
                    <col width="10%">

                </colgroup>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Crops</th>
                        <th>Harvest Count</th>
                        <th>Direction</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i = 1;
                    $qry = $conn->query("SELECT f.*, GROUP_CONCAT(c.Name SEPARATOR ', ') AS Crops, COUNT(h.Id) AS HarvestCount
                                        FROM farm f
                                        LEFT JOIN crop c ON f.Id = c.FarmId
                                        LEFT JOIN harvest h ON c.Id = h.CropId
                                        WHERE f.VendorListId IN (SELECT id FROM vendor_list WHERE user_id = '{$user_id}' AND delete_flag = 0)
                                        GROUP BY f.Id
                                        ORDER BY f.Name ASC");
                    while($row = $qry->fetch_assoc()):
                    ?>
                        <tr>
                            <td class="text-center"><?php echo $i++; ?></td>
                            <td class="text-center"><img src="<?php echo validate_image($row['Image']) ?>" class="img-farm img-thumbnail" alt="farm_image"></td>
                            <td><?php echo ucwords($row['Name']) ?></td>
                            <td><?php echo $row['Description'] ?></td>
                            <td><?php echo $row['Crops'] ?></td>
                            <td><?php echo $row['HarvestCount'] ?></td>
                            <td>
                                <a href="https://www.google.com/maps/dir/?api=1&destination=<?php echo $row['Latitude'] ?>,<?php echo $row['Longitude'] ?>" target="_blank">Get Directions</a>
                            </td>
                            <td align="center">
                                <button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
                                    Action
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <div class="dropdown-menu" role="menu">
                                    <a class="dropdown-item" href="?page=farm/manage_farm&id=<?php echo $row['Id'] ?>"><span class="fa fa-edit text-primary"></span> Edit</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?php echo $row['Id'] ?>"><span class="fa fa-trash text-danger"></span> Delete</a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        $('.delete_data').click(function(){
            _conf("Are you sure to delete this farm permanently?","delete_farm",[$(this).attr('data-id')])
        })
        $('.table').dataTable();
    })
    function delete_farm($id){
        start_loader();
        $.ajax({
            url:_base_url_+"classes/Master.php?f=delete_farm",
            method:"POST",
            data:{id: $id},
            dataType:"json",
            error:err=>{
                console.log(err)
                alert_toast("An error occurred.",'error');
                end_loader();
            },
            success:function(resp){
                if(typeof resp== 'object' && resp.status == 'success'){
                    location.reload();
                }else{
                    alert_toast("An error occurred.",'error');
                    end_loader();
                }
            }
        })
    }
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#print_btn').click(function() {
            var month = $('#farm_data').val();
            var url = "print_farm.php";
            if (month) {
                url += "?farm_data=" + month;
            }
            var nw = window.open(url, "_blank", "fullscreen=yes");
            setTimeout(function() {
                nw.print();
                setTimeout(function() {
                    nw.close();
                }, 500);
            }, 1000);
        });
    });
</script>