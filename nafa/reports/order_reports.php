<?php require_once('./ecommerce/topbar.php') ?>

<?php $month = isset($_GET['month']) ? $_GET['month'] : date("Y-m"); ?>
<div class="content py-3">
    <div class="card card-outline card-navy shadow rounded-0">
        <div class="card-header">
            <h5 class="card-title">Monthly Order Reports</h5>
        </div>
        <div class="card-body">
            <div class="container-fluid">
                <div class="callout callout-primary shadow rounded-0">
                    <form action="" id="filter">
                        <div class="row align-items-end">
                            <div class="col-lg-3 col-md-4 col-sm-12">
                                <div class="form-group">
                                    <label for="month" class="control-label">Month</label>
                                    <input type="month" name="month" id="month" value="<?= $month ?>" class="form-control rounded-0" required>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-4 col-sm-12">
                                <div class="form-group">
                                    <button class="btn btn-primary btn-flat btn-sm"><i class="fa fa-filter"></i> Filter</button>
                                    <button class="btn btn-light border btn-flat btn-sm" type="button" id="print"><i class="fa fa-print"></i> Print</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="clear-fix mb-3"></div>
                    <div id="outprint">
                        <table class="table table-bordered table-stripped">
                            <colgroup>
                                <col width="3%">
                                <col width="12%">
                                <col width="20%">
                                <col width="20%">
                                <col width="20%">
                                <col width="15%">
                                <col width="15%">
                            </colgroup>
                            <thead>
                                <tr class="">
                                    <th class="text-center align-middle py-1">#</th>
                                    <th class="text-center align-middle py-1">Date Created</th>
                                    <th class="text-center align-middle py-1">Ref. Code</th>
                                    <th class="text-center align-middle py-1">Client</th>
                                    <th class="text-center align-middle py-1">Vendor</th>
                                    <th class="text-center align-middle py-1">Status</th>
                                    <th class="text-center align-middle py-1">Total Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $i = 1;
                                $total = 0;
                                $orders = $conn->query("SELECT o.*,c.code as ccode, CONCAT(c.lastname, ', ',c.firstname,' ',COALESCE(c.middlename,'')) as client,concat(v.code, '-',v.shop_name) as vendor from `order_list` o inner join client_list c on o.client_id = c.id inner join vendor_list v on o.vendor_id = v.id where date_format(o.date_created,'%Y-%m') = '{$month}' order by unix_timestamp(o.date_created) desc ");
                                while($row = $orders->fetch_assoc()):
                                    $total += $row['total_amount'];
                                ?>
                                    <tr>
                                        <td class="text-center align-middle px-2 py-1"><?php echo $i++; ?></td>
                                        <td class="align-middle px-2 py-1"><?php echo date("Y-m-d H:i",strtotime($row['date_created'])) ?></td>
                                        <td class="align-middle px-2 py-1"><?= $row['code'] ?></td>
                                        <td class="align-middle px-2 py-1"><?php echo ucwords($row['ccode'].' - '.$row['client']) ?></td>
                                        <td class="align-middle px-2 py-1"><?php echo ucwords($row['vendor']) ?></td>
                                        <td class="text-center align-middle px-2 py-1">
                                            <?php 
                                                switch($row['status']){
                                                    case 0:
                                                        echo '<span class="badge badge-secondary bg-gradient-secondary px-3 rounded-pill">Pending</span>';
                                                        break;
                                                    case 1:
                                                        echo '<span class="badge badge-primary bg-gradient-primary px-3 rounded-pill">Confirmed</span>';
                                                        break;
                                                    case 2:
                                                        echo '<span class="badge badge-info bg-gradient-info px-3 rounded-pill">Packed</span>';
                                                        break;
                                                    case 3:
                                                        echo '<span class="badge badge-warning bg-gradient-warning px-3 rounded-pill">Out for Delivery</span>';
                                                        break;
                                                    case 4:
                                                        echo '<span class="badge badge-success bg-gradient-success px-3 rounded-pill">Delivered</span>';
                                                        break;
                                                    case 5:
                                                        echo '<span class="badge badge-danger bg-gradient-danger px-3 rounded-pill">Cancelled</span>';
                                                        break;
                                                    default:
                                                        echo '<span class="badge badge-light bg-gradient-light border px-3 rounded-pill">N/A</span>';
                                                        break;
                                                }
                                            ?>
                                        </td>
                                        <td class="text-right align-middle px-2 py-1"><?php echo format_num($row['total_amount']) ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th class="text-center px-1 py-1 align-middle" colspan="6">Total</th>
                                    <th class="text-right px-1 py-1 align-middle"><?= format_num($total) ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<noscript id="print-header">
    <style>
        #sys_logo{
            width:5em !important;
            height:5em !important;
            object-fit:scale-down !important;
            object-position:center center !important;
        }
    </style>
    <div class="d-flex align-items-center">
        <div class="col-auto text-center pl-4">
            <img src="<?= validate_image($_settings->info('logo')) ?>" alt="System Logo" id="sys_logo" class="img-circle border border-dark">
        </div>
        <div class="col-auto flex-shrink-1 flex-grow-1 px-4">
            <h4 class="text-center m-0"><?= $_settings->info('name') ?></h4>
            <h3 class="text-center m-0"><b>Order Report</b></h3>
            <h5 class="text-center m-0">For the Month of</h5>
            <h5 class="text-center m-0"><?= date("F Y", strtotime($month)) ?></h5>
        </div>
    </div>
    <hr>
</noscript>
<script>
    $(function(){
        $('#filter').submit(function(e){
            e.preventDefault();
            location.href = "./?page=reports/order_reports&"+$(this).serialize();
        });

        $('#print').click(function(){
            // Clone content to be printed
            var contentToPrint = $('#outprint').clone();
            var printHeader = $($('noscript#print-header').html()).clone();

            // Create a hidden div to hold the content
            var printArea = $('<div>').append(printHeader).append(contentToPrint);
            
            // Append to the body
            $('body').append(printArea);

            // Use CSS to hide all elements except the printArea
            $('body > *').not(printArea).addClass('d-none');

            // Print the content
            window.print();

            // Revert the visibility of the original content
            $('body > *').not(printArea).removeClass('d-none');
            
            // Remove the temporary print area after printing
            printArea.remove();
        });
    });
</script>
