<?php
$month = isset($_GET['month']) ? $_GET['month'] : '';
$user_id_filter = isset($_GET['user_id']) ? $_GET['user_id'] : '';

// Fetch users for the dropdown
$users_query = "SELECT id, firstname FROM users";
$users_result = $conn->query($users_query);

// Query to fetch products data
$where_clause = "WHERE p.delete_flag = 0";
if (!empty($month)) {
    $where_clause .= " AND DATE_FORMAT(p.date_created, '%Y-%m') = '{$month}'";
}
if (!empty($user_id_filter)) {
    $where_clause .= " AND p.vendor_id IN (SELECT id FROM vendor_list WHERE user_id = '{$user_id_filter}' AND delete_flag = 0)";
}

$qry = $conn->query("SELECT p.*, v.shop_owner AS Owner
                    FROM product_list p
                    LEFT JOIN vendor_list v ON p.vendor_id = v.id
                    $where_clause
                    ORDER BY p.name ASC");
?>

<div class="content py-3">
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">List of Products</h3>
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
                                <col width="10%">
                                <col width="15%">
                                <col width="10%">
                                <col width="20%">
                                <col width="10%">
                                <col width="10%">
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
                                    <th>Price</th>
                                    <th>Stock Amount</th>
                                    <th>Status</th>
                                    <th>Date Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $i = 1;
                                while($row = $qry->fetch_assoc()):
                                ?>
                                    <tr>
                                        <td class="text-center"><?= $i++ ?></td>
                                        <td class="text-center"><img src="<?= validate_image($row['image_path']) ?>" class="img-product img-thumbnail" alt="product_image"></td>
                                        <td><?= ucwords($row['name']) ?></td>
                                        <td><?= ucwords($row['Owner']) ?></td>
                                        <td><?= $row['description'] ?></td>
                                        <td><?= number_format($row['price'], 2) ?></td>
                                        <td><?= $row['stock_amount'] ?></td>
                                        <td><?= $row['status'] == 1 ? 'Active' : 'Inactive' ?></td>
                                        <td><?= $row['date_created'] ?></td>
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
            $month_text = isset($_GET['month']) ? date("F Y", strtotime($month)) : "All Months";

            ?>
            <img src="../<?= $avatar ?>" alt="avatar" id="user_avatar" class="img-circle border border-dark">
        </div>
        <div class="col-auto flex-shrink-1 flex-grow-1 px-4">
            <h4 class="text-center m-0"><?= $first_name ?> Farmers Association</h4>
            <h3 class="text-center m-0"><b>Product Report</b></h3>
            <h5 class="text-center m-0">For <?= $month_text ?></h5>
        </div>
    </div>
    <hr>
</noscript>

<script>
    $(document).ready(function() {
        $('#filter').submit(function(e){
            e.preventDefault();
            location.href = "<?php echo base_url ?>./?page=nafa_report/product_report&"+$(this).serialize();
        });

        $('#clearFilter').click(function() {
            $('#month').val(''); // Clear the month input
            $('#user_id').val(''); // Clear the user selection
            $('#filter').submit(); // Submit the form to clear the filter
        });

        $('#printAll').click(function(){
            printAllProducts();
        });

        function printAllProducts() {
            start_loader();
            var head = $('head').clone();
            var p = $('#outprint').clone();
            var el = $('<div>');
            var header = $($('noscript#print-header').html()).clone();
            head.find('title').text("All Products Report - Print View");
            el.append(head);
            el.append(header);
            el.append(p);

            // Append footer content
            var footerContent = '<div class="text-center">' +
                '<img src="../<?= $esignature ?>" alt="E-signature" class="img-fluid mb-3" style="max-width: 200px; margin-bottom: -10px;">' +
                '<hr style="width: 30%; height: 5px; color: black">' +
                '<p><b><?= $shop_owner_name ?></b></p>' +
                '<p>Shop Owner</p>' +
                '</div>';
            
            el.append(footerContent);

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

    $(document).ready(function(){
        $('.delete_data').click(function(){
            _conf("Are you sure to delete this product permanently?","delete_product",[$(this).attr('data-id')])
        });
    });

    function delete_product($id){
        start_loader();
        $.ajax({
            url: _base_url_+"classes/Master.php?f=delete_product",
            method: "POST",
            data: { id: $id },
            dataType: "json",
            error: err => {
                console.log(err);
                alert_toast("An error occurred.", 'error');
                end_loader();
            },
            success: function(resp){
                if (typeof resp == 'object' && resp.status == 'success') {
                    location.reload();
                } else {
                    alert_toast("An error occurred.", 'error');
                    end_loader();
                }
            }
        });
    }
</script>
