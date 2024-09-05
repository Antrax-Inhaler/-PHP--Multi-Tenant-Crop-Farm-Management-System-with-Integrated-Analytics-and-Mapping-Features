<?php
$user_id = $_settings->userdata('id');

// Get filter parameters
$month_filter = isset($_GET['month']) ? $_GET['month'] : '';
$paid_filter = isset($_GET['paid']) ? $_GET['paid'] : '';

// SQL query to fetch vendor commissions
$commission_query = "SELECT v.shop_name, v.shop_owner, vc.vendor_id, vc.total_sales, vc.total_commission, vc.month, vc.paid 
                     FROM vendor_commissions vc
                     JOIN vendor_list v ON vc.vendor_id = v.id
                     WHERE v.user_id = '{$user_id}' AND v.delete_flag = 0";

if ($month_filter) {
    $commission_query .= " AND vc.month = '{$month_filter}'";
}

if ($paid_filter !== '') {
    $commission_query .= " AND vc.paid = '{$paid_filter}'";
}

$commission_query .= " ORDER BY vc.month DESC, v.shop_name ASC";

$commissions = $conn->query($commission_query);

// Calculate totals
$total_sales_sum = 0;
$total_commission_sum = 0;
while($row = $commissions->fetch_assoc()) {
    $total_sales_sum += $row['total_sales'];
    $total_commission_sum += $row['total_commission'];
}
$commissions->data_seek(0); // Reset the pointer to the start

?>

<style>
    .img-avatar{
        width:45px;
        height:45px;
        object-fit:cover;
        object-position:center center;
        border-radius:100%;
    }
    @media print {
        .no-print {
            display: none;
        }
    }
</style>

<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">Vendor Monthly Commissions</h3>
        <div class="card-tools">
            <a href="javascript:void(0)" class="btn btn-flat btn-primary" id="print"><i class="fa fa-print"></i> Print</a>
        </div>
    </div>
    <div class="card-body">
        <div class="container-fluid">
            <form action="" id="filter">
                <div class="row align-items-end">
                    <div class="col-lg-3 col-md-4 col-sm-12">
                        <div class="form-group">
                            <label for="month" class="control-label">Month</label>
                            <input type="month" name="month" id="month" value="<?= $month_filter ?>" class="form-control rounded-0">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-12">
                        <div class="form-group">
                            <label for="paid" class="control-label">Paid Status</label>
                            <select name="paid" id="paid" class="form-control rounded-0">
                                <option value="" <?= $paid_filter === '' ? 'selected' : '' ?>>All</option>
                                <option value="1" <?= $paid_filter === '1' ? 'selected' : '' ?>>Paid</option>
                                <option value="0" <?= $paid_filter === '0' ? 'selected' : '' ?>>Unpaid</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-12">
                        <div class="form-group">
                            <button class="btn btn-primary btn-flat btn-sm"><i class="fa fa-filter"></i> Filter</button>
                            <a href="./?page=commission" class="btn btn-light border btn-flat btn-sm"><i class="fa fa-times"></i> Clear Filter</a>
                        </div>
                    </div>
                </div>
            </form>
            <div class="clear-fix mb-3"></div>
            <div id="outprint">
                <table class="table table-bordered table-striped">
                    <colgroup>
                        <col width="16%">
                        <col width="16%">
                        <col width="16%">
                        <col width="16%">
                        <col width="16%">
                        <col width="10%">
                        <col width="10%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>Shop Name</th>
                            <th>Owner Name</th>
                            <th>Total Sales</th>
                            <th>Total Commission</th>
                            <th>Month</th>
                            <th>Paid</th>
                            <th class="no-print">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $commissions->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['shop_name'] ?></td>
                            <td><?= $row['shop_owner'] ?></td>
                            <td><?= number_format($row['total_sales'], 2) ?></td>
                            <td><?= number_format($row['total_commission'], 2) ?></td>
                            <td><?= date('F Y', strtotime($row['month'] . '-01')) ?></td>
                            <td class="text-center">
                                <?php if($row['paid'] == 1): ?>
                                    <span class="badge badge-primary px-3 rounded-pill">Paid</span>
                                <?php else: ?>
                                    <span class="badge badge-danger px-3 rounded-pill">Unpaid</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center no-print">
                                <input type="checkbox" class="toggle-paid" data-vendor_id="<?= $row['vendor_id'] ?>" data-month="<?= $row['month'] ?>" <?= $row['paid'] == 1 ? 'checked' : '' ?>>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2">Total</th>
                            <th><?= number_format($total_sales_sum, 2) ?></th>
                            <th><?= number_format($total_commission_sum, 2) ?></th>
                            <th colspan="3"></th>
                        </tr>
                    </tfoot>
                </table>
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
        .print-header-container {
            display: flex;
            align-items: center;
        }
        .print-header-details {
            flex: 1;
            text-align: center;
            padding: 0 2em;
        }
        .print-header-details h4, .print-header-details h3, .print-header-details h5 {
            margin: 0;
        }
    </style>
    <div class="print-header-container">
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
            $month_text = isset($_GET['month']) ? date("F Y", strtotime($_GET['month'])) : "All Months";
            $paid_status_text = isset($_GET['paid']) ? ($_GET['paid'] == '1' ? 'Paid' : 'Unpaid') : "All";

            ?>
            <img src="../<?= $avatar ?>" alt="avatar" id="user_avatar" class="img-circle border border-dark">
        </div>
        <div class="print-header-details">
            <h4><?= $first_name ?> Farmers Association</h4>
            <h3><b>Commission Report</b></h3>
            <h5>For <?= $month_text ?></h5>
            <h5>Where <?= $paid_status_text ?></h5>
        </div>
    </div>
    <hr>
</noscript>


<script>
$(document).ready(function() {
    $('#filter').submit(function(e){
        e.preventDefault();
        location.href = "./?page=commission&"+$(this).serialize();
    });

    $('#print').click(function(){
        start_loader();
        var head = $('head').clone();
        var p = $('#outprint').clone();
        var el = $('<div>');
        var header = $($('noscript#print-header').html()).clone();
        head.find('title').text("Vendor Monthly Commission - Print View");
        el.append(head);
        el.append(header);
        el.append(p);

        var nw = window.open("","_blank","width=1000,height=900,top=50,left=75");
        nw.document.write(el.html());
        nw.document.close();

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

    $('.toggle-paid').change(function(){
        var checkbox = $(this);
        var vendor_id = checkbox.data('vendor_id');
        var month = checkbox.data('month');
        var paid = checkbox.prop('checked') ? 1 : 0;

        if (confirm('Are you sure you want to update the paid status?')) {
            $.ajax({
                url: _base_url_ + "classes/Master.php?f=update_vendor_commission_paid_status",
                method: 'POST',
                data: { vendor_id: vendor_id, month: month, paid: paid },
                dataType: 'json',
                success: function(resp){
                    if (resp.status == 'success') {
                        alert('Paid status updated successfully.');
                    } else {
                        alert('Failed to update paid status. ' + resp.message);
                        // Revert the checkbox state if update fails
                        checkbox.prop('checked', !paid);
                    }
                },
                error: function(){
                    alert('An error occurred while updating the paid status.');
                    // Revert the checkbox state if an error occurs
                    checkbox.prop('checked', !paid);
                }
            });
        } else {
            // Revert the checkbox state if the user cancels the update
            checkbox.prop('checked', !paid);
        }
    });
});
</script>
