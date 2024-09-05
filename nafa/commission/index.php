<h1 class="">Welcome to <?php echo $_settings->info('name') ?> - Admin Side</h1>
<style>
  #cover-image {
    width: calc(100%);
    height: 50vh;
    object-fit: cover;
    object-position: center center;
  }
</style>
<hr>

<?php 
// Fetch current user ID
$user_id = $_settings->userdata('id');

// Fetch vendors related to the current user
$vendors = $conn->query("SELECT * FROM vendor_list WHERE user_id = '{$user_id}' AND delete_flag = 0");

?>

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Vendor Monthly Commissions</h3>
      </div>
      <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
          <thead>
            <tr>
              <th>Vendor Name</th>
              <th>Total Sales</th>
              <th>Total Commission</th>
              <th>Month</th>
              <th>Paid</th>
            </tr>
          </thead>
          <tbody>
            <?php while($vendor = $vendors->fetch_assoc()): ?>
              <?php
                $vendor_id = $vendor['id'];
                $commission_query = $conn->query("SELECT * FROM vendor_commissions WHERE vendor_id = '{$vendor_id}'");
                while($row = $commission_query->fetch_assoc()):
                  $total_sales = $row['total_sales'];
                  $total_commission = $row['total_commission'];
                  $month = $row['month'];
                  $paid = $row['paid'];
              ?>
              <tr>
                <td><?php echo $vendor['shop_name']; ?></td>
                <td><?php echo number_format($total_sales, 2); ?></td>
                <td><?php echo number_format($total_commission, 2); ?></td>
                <td><?php echo date('F Y', strtotime($month . '-01')); ?></td>
                <td><input type="checkbox" class="paid-checkbox" data-vendor-id="<?php echo $vendor_id; ?>" data-month="<?php echo $month; ?>" <?php echo $paid ? 'checked' : ''; ?>></td>
              </tr>
              <?php endwhile; ?>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<div class="clear-fix mb-2">
  <div class="text-center w-100">
    <img src="<?= validate_image($_settings->info('cover')) ?>" alt="System Cover image" class="w-100" id="cover-image">
  </div>
</div>
<script>
$(document).ready(function(){
    $('.paid-checkbox').change(function(){
        let checkbox = $(this);
        let vendor_id = checkbox.data('vendor-id');
        let month = checkbox.data('month');
        let paid = checkbox.is(':checked') ? 1 : 0;

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
