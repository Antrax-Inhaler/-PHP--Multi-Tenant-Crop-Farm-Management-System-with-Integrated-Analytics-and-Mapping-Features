<?php require_once('./inc/reportTopBar.php') ?>

<?php
// Initialize variables
$month = isset($_GET['month']) ? $_GET['month'] : '';

// Fetch current user's ID
$user_id = $_settings->userdata('id');

// Fetch current user's first name for filtering
$users_query = "SELECT id, firstname FROM users";
$users_result = $conn->query($users_query);

// Query to fetch farms data
$where_clause = "";
if (!empty($month)) {
    $where_clause = "AND DATE_FORMAT(f.CreatedAt, '%Y-%m') = '{$month}'";
}

$qry = $conn->query("SELECT f.*, v.shop_owner AS Owner, GROUP_CONCAT(c.Name SEPARATOR ', ') AS Crops, COUNT(h.Id) AS HarvestCount
                    FROM farm f
                    LEFT JOIN crop c ON f.Id = c.FarmId
                    LEFT JOIN harvest h ON c.Id = h.CropId
                    LEFT JOIN vendor_list v ON f.VendorListId = v.id
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
                                <label for="user_id" class="control-label">Filter by User</label>
                                <select name="user_id" id="user_id" class="form-control rounded-0">
                                    <option value="">All Users</option>
                                    <?php while($user_row = $users_result->fetch_assoc()): ?>
                                        <option value="<?= $user_row['id'] ?>" <?= ($user_row['id'] == $user_id) ? 'selected' : '' ?>>
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
            // Query to fetch user details
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
            location.href = "./?page=nafa_report/member_report&"+$(this).serialize();
        });

        $('#clearFilter').click(function() {
            $('#month').val(''); // Clear the month input
            $('#user_id').val(''); // Clear the user_id input
            $('#filter').submit(); // Submit the form to clear the filter
        });

        $('#printAll').click(function(){
            printAllFarms();
        });

        function printAllFarms() {
            start_loader();
            var head = $('head').clone();
            var p = $('#outprint').clone();
            var el = $('<div>');
            var header = $($('noscript#print-header').html()).clone();
            head.find('title').text("All Farms Report - Print View");
            el.append(head);
            el.append(header);
            el.append(p);

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
        }
    });
</script>
