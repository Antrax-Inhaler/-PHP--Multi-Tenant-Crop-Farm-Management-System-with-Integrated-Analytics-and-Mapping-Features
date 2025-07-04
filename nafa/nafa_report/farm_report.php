<?php
$month = isset($_GET['month']) ? $_GET['month'] : '';
$user_id_filter = isset($_GET['user_id']) ? $_GET['user_id'] : '';

// Fetch the vendor's details including shop owner's name and e-signature
$vendor_id = $_settings->userdata('id');
$query = "SELECT esignature, shop_owner FROM vendor_list WHERE id = $vendor_id";
$result = $conn->query($query);
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $shop_owner_name = $row['shop_owner'];
    $esignature = $row['esignature'];
} else {
    // Handle error if no data is found
    $shop_owner_name = "Shop Owner Name Not Found";
    $esignature = "E-signature Not Found";
}

// Fetch users for the dropdown
$users_query = "SELECT id, firstname FROM users";
$users_result = $conn->query($users_query);

// Query to fetch farms data
$where_clause = "";
if (!empty($month)) {
    $where_clause .= " AND DATE_FORMAT(f.CreatedAt, '%Y-%m') = '{$month}'";
}
if (!empty($user_id_filter)) {
    $where_clause .= " AND f.VendorListId IN (SELECT id FROM vendor_list WHERE user_id = '{$user_id_filter}' AND delete_flag = 0)";
}

$qry = $conn->query("SELECT f.*, v.shop_owner AS Owner, GROUP_CONCAT(c.Name SEPARATOR ', ') AS Crops, COUNT(h.Id) AS HarvestCount
                    FROM farm f
                    LEFT JOIN crop c ON f.Id = c.FarmId
                    LEFT JOIN harvest h ON c.Id = h.CropId
                    LEFT JOIN vendor_list v ON f.VendorListId = v.id
                    WHERE 1=1 $where_clause
                    GROUP BY f.Id
                    ORDER BY f.Name ASC");
?>

<div class="content py-3">
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">List of Farms</h3>
            <div class="card-tools">
                <form action="" id="filter">
                    <div class="row align-items-end">
                        <div class="col-lg-3 col-md-4 col-sm-12">
                            <div class="form-group">
                                <label for="month" class="control-label">Month</label>
                                <input type="month" name="month" id="month" value="<?= $month ?>" class="form-control rounded-0">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-4 col-sm-12">
                            <div class="form-group">
                                <label for="user_id" class="control-label">User</label>
                                <select name="user_id" id="user_id" class="form-control rounded-0">
                                    <option value="">All Users</option>
                                    <?php while ($user_row = $users_result->fetch_assoc()): ?>
                                        <option value="<?= $user_row['id'] ?>" <?= $user_id_filter == $user_row['id'] ? 'selected' : '' ?>>
                                            <?= $user_row['firstname'] ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
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
            </div>
        </div>
        <div class="card-body">
            <div class="container-fluid">
                <div class="callout callout-primary shadow rounded-0">
                    <div class="clear-fix mb-3"></div>
                    <div id="outprint">
                        <table class="table table-bordered table-striped">
                            <colgroup>
                                <col width="5%">
                                <col width="15%">
                                <col width="10%">
                                <col width="15%">
                                <col width="25%">
                                <col width="25%">
                                <col width="10%">
                                <col width="10%">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Owner</th>
                                    <th>Description</th>
                                    <th>Crops</th>
                                    <th>Harvest Count</th>
                                    <th>Direction</th>
                                    <th>Created At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $i = 1;
                                while($row = $qry->fetch_assoc()):
                                ?>
                                    <tr>
                                        <td class="text-center"><?= $i++ ?></td>
                                        <td class="text-center"><img src="<?= validate_image($row['Image']) ?>" class="img-farm img-thumbnail" alt="farm_image"></td>
                                        <td><?= ucwords($row['Name']) ?></td>
                                        <td><?= ucwords($row['Owner']) ?></td>
                                        <td><?= $row['Description'] ?></td>
                                        <td><?= $row['Crops'] ?></td>
                                        <td><?= $row['HarvestCount'] ?></td>
                                        <td>
                                            <a href="https://www.google.com/maps/dir/?api=1&destination=<?= $row['Latitude'] ?>,<?= $row['Longitude'] ?>" target="_blank">Get Directions</a>
                                        </td>
                                        <td><?= $row['CreatedAt'] ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <footer class="mt-4">
                        <div class="row">
                            <div class="col-md-6 offset-md-3 text-center">
                                <!-- E-signature -->
                                <img src="../<?= $esignature ?>" alt="E-signature" class="img-fluid mb-3" style="max-width: 200px; margin-bottom: -10px;">
                                <hr style="width: 30%; height: 5px; color: black">
                                <b><?= $shop_owner_name ?></b>
                                <p>Shop Owner</p>
                            </div>
                        </div>
                    </footer>
                </div>
            </div>
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
            $user_id = $_settings->userdata('id');
            $query = "SELECT * FROM users WHERE id = '{$user_id}'";
            $result = $conn->query($query);
            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $avatar = $row['avatar'] ? $row['avatar'] : 'uploads/logo-1644367441.png';
                $first_name = $row['firstname'];
            } else {
                $avatar = 'uploads/logo-1644367441.png';
                $first_name = 'User';
            }
            $month_text = isset($_GET['month']) ? date("F Y", strtotime($month)) : "All Months";
            ?>
            <img src="../<?= $avatar ?>" alt="avatar" id="user_avatar" class="img-circle border border-dark">
        </div>
        <div class="col-auto flex-shrink-1 flex-grow-1 px-4">
            <h4 class="text-center m-0"><?= $first_name ?> Farmers Association</h4>
            <h3 class="text-center m-0"><b>Farm Report</b></h3>
            <h5 class="text-center m-0">For <?= $month_text ?></h5>
        </div>
    </div>
    <hr>
</noscript>

<script>
    $(document).ready(function() {
        $('#filter').submit(function(e){
            e.preventDefault();
            location.href = "./?page=nafa_report/farm_report&"+$(this).serialize();
        });

        $('#clearFilter').click(function() {
            $('#month').val('');
            $('#user_id').val('');
            $('#filter').submit();
        });

        $('#printAll').click(function() {
            // Clone content to be printed
            var contentToPrint = $('#outprint').clone();
            var printHeader = $($('noscript#print-header').html()).clone();

            // Create a hidden div to hold the content
            var printArea = $('<div>').append(printHeader).append(contentToPrint);
            
            // Append footer content
            var footerContent = $('<div class="text-center">')
                .append('<img src="../<?= $esignature ?>" alt="E-signature" class="img-fluid mb-3" style="max-width: 200px; margin-bottom: -10px;">')
                .append('<hr style="width: 30%; height: 5px; color: black">')
                .append('<b><?= $shop_owner_name ?></b>')
                .append('<p>Shop Owner</p>');
            printArea.append(footerContent);

            // Print the content using the built-in window.print() method
            var printWindow = window.open('', '_blank');
            printWindow.document.write('<html><head><title>Print Farm Report</title><style>body {font-family: Arial, sans-serif;}</style></head><body>' + printArea.html() + '</body></html>');
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
            printWindow.close();
        });
    });
</script>
