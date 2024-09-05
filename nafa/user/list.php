<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>

<style>
    /* Custom Dropdown Styles */
    .dropdown-unique {
        position: relative;
        display: inline-block;
    }
    .dropdown-content-unique {
        display: none;
        position: absolute;
        background-color: #f9f9f9;
        min-width: 160px;
        box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
        z-index: 1;
        border-radius: 10px;
    }
    .dropdown-content-unique a {
        color: black;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
        border-radius: 10px;
    }
    .dropdown-content-unique a:hover {
        background-color: #f1f1f1;
    }
    .dropdown-unique:hover .dropdown-content-unique {
        display: block;
    }
    .dropdown-unique:hover .dropdown-button-unique {
        background-color: #3e8e41;
    }

    /* Minimalistic Page Layout and Design */
    h3.title-unique {
        color: #333;
        text-align: center;
        margin-bottom: 20px;
    }

    .table-unique {
        width: 100%;
        border-collapse: collapse;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .table-unique th, .table-unique td {
        padding: 15px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    .table-unique th {
        background-color: #9CDC78;
        color: white;
    }

    .table-unique td:last-child {
        text-align: center;
    }

    .avatar-unique img {
        width: 45px;
        height: 45px;
        object-fit: cover;
        object-position: center center;
        border-radius: 50%;
    }

    .dropdown-button-unique {
        padding: 5px 10px;
        background-color: #00bfa5;
        color: #fff;
        border: none;
        border-radius: 10px;
        cursor: pointer;
    }

    .dropdown-button-unique:hover {
        background-color: #00796b;
    }

    @media (max-width: 600px) {
        .table-unique th, .table-unique td {
            padding: 10px;
        }
    }

    /* Scrollable Table Wrapper */
    .table-wrapper {
        overflow-y: auto;
        max-height: 400px; /* Adjust based on your layout needs */
    }
</style>
<h3 class="title-unique">List of Barangay Farmers Associations</h3>
<div>
    <a href="?page=user/manage_user" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span>  Create New</a>
</div>
<div class="table-wrapper">
    <table class="table-unique">
        <thead>
            <tr>
                <th>#</th>
                <th>Logo</th>
                <th>Association</th>
                <th>Username</th>
                <th>Members Count</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $i = 1;
            $qry = $conn->query("SELECT u.*, CONCAT(u.firstname, ' ', u.lastname) as name, (SELECT COUNT(*) FROM vendor_list v WHERE v.user_id = u.id) as member_count FROM users u WHERE u.type != 3 ORDER BY CONCAT(u.firstname, ' ', u.lastname) ASC");
            while($row = $qry->fetch_assoc()):
            ?>
                <tr>
                    <td><?php echo $i++; ?></td>
                    <td class="avatar-unique"><img src="<?php echo validate_image($row['avatar']) ?>" alt="user_avatar"></td>
                    <td><?php echo ucwords($row['name']) ?></td>
                    <td><?php echo $row['username'] ?></td>
                    <td><?php echo $row['member_count'] ?></td>
                    <td>
                        <div class="dropdown-unique">
                            <button class="dropdown-button-unique">Action</button>
                            <div class="dropdown-content-unique">
                            <a href="javascript:void(0)" class="view_data" data-id="<?php echo $row['id'] ?>"><span class="fa fa-eye text-info"></span>View</a>
                            <a href="javascript:void(0)" class="delete_data" data-id="<?php echo $row['id'] ?>"><span class="fa fa-trash text-danger"></span>Delete</a>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<script>
    $(document).ready(function(){
        $('.edit_data').click(function(){
            uni_modal('Update Association', "user/manage_user2.php?id=" + $(this).attr('data-id'), 'large');
        });
        $('.view_data').click(function(){
            uni_modal('View User Details', "user/view_user.php?id=" + $(this).attr('data-id'), 'large');
        });

        $('.delete_data').click(function(){
            _conf("Are you sure to delete this User permanently?","delete_user",[$(this).attr('data-id')])
        });
        $('.table-unique').dataTable();
    });

    function delete_user($id){
        start_loader();
        $.ajax({
            url:_base_url_+"classes/Users.php?f=delete",
            method:"POST",
            data:{id: $id},
            dataType:"json",
            error:err=>{
                console.log(err);
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
        });
    }
</script>
