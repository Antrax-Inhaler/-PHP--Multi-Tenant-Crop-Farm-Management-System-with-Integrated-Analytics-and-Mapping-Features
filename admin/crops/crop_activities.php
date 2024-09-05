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
        overflow: hidden;
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
</style>

<h3 class="title-unique">List of Crop Activities</h3>
<div>
    <a href="?page=activities/manage_activity" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span> Create New</a>
</div>

<form id="filter" class="mb-3">
    <div class="row">
        <div class="col-lg-3 col-md-4 col-sm-12">
            <div class="form-group">
                <label for="vendor_id" class="control-label">Vendor</label>
                <select name="vendor_id" id="vendor_id" class="form-control rounded-0">
                    <option value="">All Vendors</option>
                    <?php
                    // Fetch vendors for the dropdown
                    $vendors_query = "SELECT id, shop_owner FROM vendor_list";
                    $vendors_result = $conn->query($vendors_query);
                    while ($vendor_row = $vendors_result->fetch_assoc()):
                    ?>
                        <option value="<?= $vendor_row['id'] ?>" <?= isset($_GET['vendor_id']) && $_GET['vendor_id'] == $vendor_row['id'] ? 'selected' : '' ?>>
                            <?= $vendor_row['shop_owner'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-12">
            <div class="form-group">
                <label for="month" class="control-label">Month</label>
                <input type="month" name="month" id="month" value="<?= isset($_GET['month']) ? $_GET['month'] : '' ?>" class="form-control rounded-0">
            </div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-12">
            <div class="form-group">
                <button class="btn btn-primary btn-flat btn-sm"><i class="fa fa-filter"></i> Filter</button>
                <button class="btn btn-light border btn-flat btn-sm" type="button" id="clearFilter"><i class="fa fa-times"></i> Clear Filter</button>
                <button class="btn btn-light border btn-flat btn-sm" type="button" id="printAll"><i class="fa fa-print"></i> Print</button>
            </div>
        </div>
    </div>
</form>

<table class="table-unique">
    <colgroup>
        <col width="5%">
        <col width="15%">
        <col width="15%">
        <col width="15%">
        <col width="20%">
        <col width="20%">
        <col width="10%">
    </colgroup>
    <thead>
        <tr>
            <th>#</th>
            <th>Vendor Name</th>
            <th>Crop Name</th>
            <th>Crop Type</th>
            <th>Activity Type</th>
            <th>Description</th>
            <th>Activity Date</th>
            <th class="action_display">Action</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $i = 1;
        $user_id = $_settings->userdata('id');
        
        $where_clause = "WHERE v.user_id = '{$user_id}'";
        if (!empty($_GET['vendor_id'])) {
            $where_clause .= " AND c.VendorId = '{$_GET['vendor_id']}'";
        }
        if (!empty($_GET['month'])) {
            $where_clause .= " AND DATE_FORMAT(ca.activity_date, '%Y-%m') = '{$_GET['month']}'";
        }

        $qry = $conn->query("
            SELECT 
                ca.id,
                v.shop_name AS vendor_name,
                c.Name AS crop_name,
                c.Type AS crop_type,
                ca.activity_type,
                ca.description AS activity_description,
                ca.activity_date
            FROM 
                crop_activity ca
            JOIN 
                crop c ON ca.crop_id = c.Id
            JOIN 
                vendor_list v ON c.VendorId = v.id
            $where_clause
            ORDER BY 
                ca.activity_date DESC
        ");
        while($row = $qry->fetch_assoc()):
        ?>
            <tr>
                <td class="text-center"><?php echo $i++; ?></td>
                <td><?php echo htmlspecialchars($row['vendor_name']); ?></td>
                <td><?php echo htmlspecialchars($row['crop_name']); ?></td>
                <td><?php echo htmlspecialchars($row['crop_type']); ?></td>
                <td><?php echo htmlspecialchars($row['activity_type']); ?></td>
                <td><?php echo htmlspecialchars($row['activity_description']); ?></td>
                <td><?php echo htmlspecialchars($row['activity_date']); ?></td>
                <td  class="action_display" align="center">
                    <div class="dropdown-unique">
                        <button class="dropdown-button-unique">Action</button>
                        <div class="dropdown-content-unique">
                            <a href="?page=activities/view_activity&id=<?php echo $row['id']; ?>"><span class="fa fa-eye text-primary"></span> View</a>
                            <a href="?page=activities/manage_activity&id=<?php echo $row['id']; ?>"><span class="fa fa-edit text-primary"></span> Edit</a>
                            <a href="javascript:void(0)" class="delete_data" data-id="<?php echo $row['id']; ?>"><span class="fa fa-trash text-danger"></span> Delete</a>
                        </div>
                    </div>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
<noscript id="print-header">
    <style>
        #user_avatar {
            width: 5em !important;
            height: 5em !important;
            object-fit: scale-down !important;
            object-position: center center !important;
        }
        .action_display{
            display: none !important;
        }
        @media print {
    .table-unique {
        width: 100%;
        border-collapse: collapse;
        background: #fff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: none; /* Example: Remove box-shadow in print view */
    }

    .table-unique th, .table-unique td {
        padding: 10px;
        border: 1px solid #ddd; /* Example: Ensure borders are visible in print */
    }

    .table-unique th {
        background-color: #9CDC78;
        color: white;
    }

    .table-unique td:last-child {
        text-align: center;
    }
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

            // Determine the text for the vendor and month section
            $vendor_text = 'All Vendors';
            if (!empty($_GET['vendor_id'])) {
                // Fetch vendor name based on selected vendor_id
                $vendor_query = "SELECT shop_owner FROM vendor_list WHERE id = '{$_GET['vendor_id']}'";
                $vendor_result = $conn->query($vendor_query);
                if ($vendor_result && $vendor_result->num_rows > 0) {
                    $vendor_row = $vendor_result->fetch_assoc();
                    $vendor_text = $vendor_row['shop_owner'];
                }
            }

            $month_text = 'All Months';
            if (!empty($_GET['month'])) {
                $month_text = date("F Y", strtotime($_GET['month']));
            }

            ?>
            <img src="../<?= $avatar ?>" alt="avatar" id="user_avatar" class="img-circle border border-dark">
        </div>
        <div class="col-auto flex-shrink-1 flex-grow-1 px-4">
            <h4 class="text-center m-0"><?= $first_name ?> Farmers Association</h4>
            <h3 class="text-center m-0"><b>Activity Report</b></h3>
            <?php if (!empty($_GET['vendor_id'])): ?>
                <h5 class="text-center m-0"><?= "Crop Activity of {$vendor_text}" ?></h5>
            <?php endif; ?>
            <?php if (!empty($_GET['month'])): ?>
                <h5 class="text-center m-0"><?= $month_text ?></h5>
            <?php endif; ?>
        </div>
    </div>
    <hr>
</noscript>


<script>
    $(document).ready(function(){
        $('.delete_data').click(function(){
            _conf("Are you sure to delete this activity permanently?", "delete_activity", [$(this).attr('data-id')]);
        });
        $('.table-unique').dataTable();

        $('#filter').submit(function(e){
            e.preventDefault();
            location.href = "./?page=crops/crop_activities&" + $(this).serialize();
        });

        $('#clearFilter').click(function() {
            $('#month').val(''); // Clear the month input
            $('#vendor_id').val(''); // Clear the vendor selection
            $('#filter').submit(); // Submit the form to clear the filter
        });

        $('#printAll').click(function() {
            var el = $('<div>');
            var printHeader = $($('noscript#print-header').html()).clone();
            el.append(printHeader);
            el.append($('.table-unique').clone());
            var nw = window.open("","_blank","width=1000,height=900,top=50,left=75");
            nw.document.write(el.html());
            nw.document.close();
            nw.print();
            setTimeout(() => {
                nw.close();
            }, 500);
        });
    });

    function delete_activity(id){
        start_loader();
        $.ajax({
            url: _base_url_ + "classes/Activities.php?f=delete_activity",
            method: "POST",
            data: {id: id},
            dataType: "json",
            error: function(err){
                console.log(err);
                alert_toast("An error occurred.", 'error');
                end_loader();
            },
            success: function(resp){
                if(typeof resp == 'object' && resp.status == 'success'){
                    location.reload();
                } else {
                    alert_toast("An error occurred.", 'error');
                    end_loader();
                }
            }
        });
    }
</script>
