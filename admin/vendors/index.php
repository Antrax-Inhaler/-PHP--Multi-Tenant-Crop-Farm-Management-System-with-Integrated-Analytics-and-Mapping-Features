<?php 
if($_settings->chk_flashdata('success')): ?>
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

<?php 
$user_id = $_settings->userdata('id');
$status_filter = isset($_GET['status']) ? intval($_GET['status']) : ''; // Fetch status filter
$status_clause = ($status_filter !== '') ? "AND v.status = {$status_filter}" : "";

$qry = $conn->query("
    SELECT 
        v.*, 
        (SELECT COUNT(*) FROM crop WHERE VendorId = v.id) AS crop_count,
        (SELECT COUNT(*) FROM farm WHERE VendorListId = v.id) AS farm_count,
        (SELECT COUNT(*) FROM harvest WHERE CropId IN (SELECT Id FROM crop WHERE VendorId = v.id)) AS harvest_count
    FROM vendor_list v 
    WHERE v.user_id = '{$user_id}' AND v.delete_flag = 0 
    {$status_clause}
    ORDER BY v.shop_name ASC
");
?>

<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">List of Members</h3>
	</div>
	<div class="card-body">
		<div class="container-fluid">
			<form action="" id="filter">
				<div class="row align-items-end mb-3">
					<div class="col-lg-2 col-md-4 col-sm-12">
						<div class="form-group">
							<label for="status" class="control-label">Status</label>
							<select name="status" id="status" class="form-control">
								<option value="">All</option>
								<option value="1">Active</option>
								<option value="0">Inactive</option>
							</select>
						</div>
					</div>
					<div class="col-lg-3 col-md-4 col-sm-12">
						<button class="btn btn-primary btn-flat btn-sm"><i class="fa fa-filter"></i> Filter</button>
						<button class="btn btn-light border btn-flat btn-sm" type="button" id="print"><i class="fa fa-print"></i> Print</button>
					</div>
				</div>
			</form>

			<table class="table table-bordered table-stripped">
				<colgroup>
					<col width="5%">
					<col width="10%">
					<col width="15%">
					<col width="20%">
					<col width="20%">
					<col width="20%">
					<col width="5%">
					<col width="5%">
					<col width="5%">
					<col width="10%">
					<col width="10%">
				</colgroup>
				<thead>
				    <tr>
				        <th>#</th>
				        <th>Avatar</th>
				        <th>Code</th>
				        <th>Full Name</th>
						<th>Contact</th>
				        <th>Shop Name</th>
				        <th>Crops</th>
				        <th>Farms</th>
				        <th>Harvests</th>
				        <th>Status</th>
				        <th>Action</th>
				    </tr>
				</thead>

				<tbody>
				    <?php 
				    $i = 1;
				    while($row = $qry->fetch_assoc()):
				    ?>
				        <tr>
				            <td class="text-center"><?php echo $i++; ?></td>
				            <td class="text-center"><img src="<?php echo validate_image($row['avatar']) ?>" class="img-avatar img-thumbnail p-0 border-2" alt="vendor_avatar"></td>
				            <td><?php echo ($row['code']) ?></td>
				            <td><?php echo ucwords($row['shop_owner']) ?></td>
							<td><?php echo ($row['contact']) ?></td>
				            <td><?php echo ucwords($row['shop_name']) ?></td>
				            <td><?php echo ($row['crop_count']) ?></td>
				            <td><?php echo ($row['farm_count']) ?></td>
				            <td><?php echo ($row['harvest_count']) ?></td>
				            <td class="text-center">
				                <?php if($row['status'] == 1): ?>
				                    <span class="badge badge-primary px-3 rounded-pill">Active</span>
				                <?php else: ?>
				                    <span class="badge badge-danger px-3 rounded-pill">Inactive</span>
				                <?php endif; ?>
				            </td>
				            <td class="action-row" align="center">
				                <button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
				                  Action
				                  <span class="sr-only">Toggle Dropdown</span>
				                </button>
				                <div class="dropdown-menu" role="menu">
				                  <a class="dropdown-item" href="?page=vendors/manage_vendor&id=<?php echo $row['id'] ?>"><span class="fa fa-edit text-primary"></span> Edit</a>
				                  <div class="dropdown-divider"></div>
				                  <a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-trash text-danger"></span> Delete</a>
				                </div>
				            </td>
				        </tr>
				    <?php endwhile; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<noscript id="print-header">
    <style>
        #user_avatar {
            width: 5em !important;
            height: 5em !important;
            object-fit: scale-down !important;
            object-position: center center !important;
        }
    </style>
    <div class="d-flex align-items-center">
        <div class="col-auto text-center pl-4">
            <?php
            // Query to fetch user details
            $user_id = $_settings->userdata('id');
            $query = "SELECT * FROM users WHERE id = '{$user_id}'";
            $result = $conn->query($query);
            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $avatar = $row['avatar'] ? $row['avatar'] : 'uploads/logo-1644367441.png'; // Replace with your default avatar path
                $first_name = $row['firstname']; // Fetching the first name from the database
            } else {
                // Handle case where user data is not found
                $avatar = 'uploads/logo-1644367441.png'; // Default avatar path
                $first_name = 'User'; // Default first name
            }

            // Determine the text for the month section
            ?>
            <img src="../<?= $avatar ?>" alt="avatar" id="user_avatar" class="img-circle border border-dark">
        </div>
        <div class="col-auto flex-shrink-1 flex-grow-1 px-4">
            <h4 class="text-center m-0"><?= $first_name ?> Farmers Association</h4>
            <h3 class="text-center m-0"><b>Member List</b></h3>
        </div>
    </div>
    <hr>
</noscript>
<script>
	$(document).ready(function(){
		$('#filter').submit(function(e){0
			e.preventDefault();
			var status = $('#status').val();
			location.href = "./?page=vendors/index&status=" + status;
		});

		$('#print').click(function(){
			start_loader();
			var head = $('head').clone();
			var p = $('.card-body').clone();
			var el = $('<div>');
			var header = $($('noscript#print-header').html()).clone();
			head.find('title').text("Vendor List - Print View");
			el.append(head);
			el.append(header);
			el.append(p);

	// Remove non-printable elements
	el.find('.btn').remove();
			el.find('.dropdown-toggle').remove();
			el.find('.dropdown-menu').remove();
			el.find('#status').parent().parent().remove(); // Remove status filter select
			el.find('th:last-child, td:last-child').remove(); // Remove Action column


			var nw = window.open("","_blank","width=1000,height=900,top=50,left=75");
			nw.document.write(el.html());
			nw.document.close();

			// Maximize the window before printing
			nw.moveTo(0, 0);
			nw.resizeTo(screen.width, screen.height);

			setTimeout(() => {
				nw.print();
				setTimeout(() => {
					nw.close();
					end_loader();
				}, 200);
			}, 500);
		});

		$('.delete_data').click(function(){
			_conf("Are you sure to delete this vendor permanently?","delete_vendor",[$(this).attr('data-id')])
		});
	});
	
	function delete_vendor($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Users.php?f=delete_vendor",
			method:"POST",
			data:{id: $id},
			dataType:"json",
			error:err=>{
				console.log(err)
				alert_toast("An error occured.",'error');
				end_loader();
			},
			success:function(resp){
				if(typeof resp== 'object' && resp.status == 'success'){
					location.reload();
				}else{
					alert_toast("An error occured.",'error');
					end_loader();
				}
			}
		})
	}
</script>
