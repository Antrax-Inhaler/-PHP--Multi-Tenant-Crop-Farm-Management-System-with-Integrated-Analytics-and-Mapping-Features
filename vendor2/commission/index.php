<style>
  #cover-image{
    width:calc(100%);
    height:50vh;
    object-fit:cover;
    object-position:center center;
  }
</style>
<hr>

<?php 
// Fetch current vendor ID
$vendor_id = $_settings->userdata('id');

// Fetch the vendor details
$vendor_query = $conn->query("SELECT * FROM vendor_list WHERE id = '{$vendor_id}' AND delete_flag = 0");
$vendor = $vendor_query->fetch_assoc();

// Fetch commission data for the current vendor
$commission_query = $conn->query("SELECT * FROM vendor_commissions WHERE vendor_id = '{$vendor_id}'");
?>

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title"><?php echo $vendor['shop_name']; ?> - Monthly Commissions</h3>
      </div>
      <!-- /.card-header -->
      <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
          <thead>
            <tr>
              <th>Total Sales</th>
              <th>Total Commission</th>
              <th>Month</th>
              <th>Paid</th>
            </tr>
          </thead>
          <tbody>
            <?php while($row = $commission_query->fetch_assoc()): ?>
              <?php
                $total_sales = $row['total_sales'];
                $total_commission = $row['total_commission'];
                $month = $row['month'];
                $paid = $row['paid'];
              ?>
              <tr>
                <td><?php echo number_format($total_sales, 2); ?></td>
                <td><?php echo number_format($total_commission, 2); ?></td>
                <td><?php echo date('F Y', strtotime($month . '-01')); ?></td>
                <td><?php echo $paid ? 'Yes' : 'No'; ?></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
      <!-- /.card-body -->
    </div>
    <!-- /.card -->
  </div>
</div>

<div class="clear-fix mb-2">
  <div class="text-center w-100">
    <img src="<?= validate_image($_settings->info('cover')) ?>" alt="System Cover image" class="w-100" id="cover-image">
  </div>
</div>
