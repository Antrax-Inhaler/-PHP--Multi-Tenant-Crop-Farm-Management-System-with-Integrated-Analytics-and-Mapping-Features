<?php require_once('../inc/topBarNav.php') ?>

<style>
  #cover-image {
    width: calc(100%);
    height: 50vh;
    object-fit: cover;
    object-position: center center;
  }

  /* Additional CSS for formatting */
  .info-header {
    font-size: 1.25rem;
    margin-bottom: 10px;
  }
</style>


<hr>
  <h2 class="text-center mt-4 mb-4">Quick Information</h2>
  <div class="row">
    <?php 
      // Get total categories count
      $totalCategories = $conn->query("SELECT COUNT(id) AS total FROM category_list WHERE delete_flag = 0 AND vendor_id = '{$_settings->userdata('id')}'")->fetch_assoc()['total'];

      // Get total products count
      $totalProducts = $conn->query("SELECT COUNT(id) AS total FROM product_list WHERE delete_flag = 0 AND vendor_id = '{$_settings->userdata('id')}'")->fetch_assoc()['total'];

      // Get commission of the vendor
      $commission = $conn->query("SELECT u.commission 
      FROM vendor_list v
      INNER JOIN users u ON v.user_id = u.id
      WHERE v.id = '{$_settings->userdata('id')}'")
->fetch_assoc()['commission'];

      // Get total sales for the current month
      $totalSales = $conn->query("
      SELECT IFNULL(SUM(total_amount), 0) AS total_sales
      FROM order_list
      WHERE vendor_id = '{$_settings->userdata('id')}'
      AND MONTH(date_updated) = MONTH(CURDATE())
      AND YEAR(date_updated) = YEAR(CURDATE())
      AND status = 4
    ")->fetch_assoc()['total_sales'];

      // Get total pending orders count
      $totalPendingOrders = $conn->query("SELECT COUNT(id) AS total FROM order_list WHERE `status` = 0 AND vendor_id = '{$_settings->userdata('id')}'")->fetch_assoc()['total'];
    ?>

    <!-- Total Categories Info Box -->
    <div class="col-12 col-sm-4 col-md-4">
    <div class="info-box">
        <span class="info-box-icon bg-gradient-primary elevation-1"><i class="fas fa-th-list"></i></span>
        <div class="info-box-content">
            <div class="move">
                <div class="info-header">Total Categories</div>
            </div>
            <span id="totalCategories" class="info-box-number text-right h4">0</span>
        </div>
    </div>
</div>

<!-- Total Products Info Box -->
<div class="col-12 col-sm-4 col-md-4">
    <div class="info-box">
        <span class="info-box-icon bg-gradient-secondary elevation-1"><i class="fas fa-boxes"></i></span>
        <div class="info-box-content">
            <div class="move">
                <div class="info-header">Total Products</div>
            </div>
            <span id="totalProducts" class="info-box-number text-right h4">0</span>
        </div>
    </div>
</div>

<!-- Commission Info Box -->
<div class="col-12 col-sm-4 col-md-4">
    <div class="info-box">
        <span class="info-box-icon bg-gradient-success elevation-1"><i class="fas fa-dollar-sign"></i></span>
        <div class="info-box-content">
            <div class="move">
                <div class="info-header">Commission</div>
            </div>
            <span id="commission" class="info-box-number text-right h4">0.00</span>
        </div>
    </div>
</div>

<!-- Total Commission to Pay Info Box -->
<div class="col-12 col-sm-4 col-md-4">
    <div class="info-box">
        <span class="info-box-icon bg-gradient-info elevation-1"><i class="fas fa-money-bill-wave"></i></span>
        <div class="info-box-content">
            <div class="move">
                <div class="info-header">Total Commission to Pay</div>
            </div>
            <span id="totalCommission" class="info-box-number text-right h4">0.00</span>
        </div>
    </div>
</div>

<!-- Total Sales Info Box -->
<div class="col-12 col-sm-4 col-md-4">
    <div class="info-box">
        <span class="info-box-icon bg-gradient-warning elevation-1"><i class="fas fa-chart-line"></i></span>
        <div class="info-box-content">
            <div class="move">
                <div class="info-header">Total Sales (This Month)</div>
            </div>
            <span id="totalSales" class="info-box-number text-right h4">0.00</span>
        </div>
    </div>
</div>

<!-- Total Pending Orders Info Box -->
<div class="col-12 col-sm-4 col-md-4">
    <div class="info-box">
        <span class="info-box-icon bg-gradient-warning elevation-1"><i class="fas fa-list"></i></span>
        <div class="info-box-content">
            <div class="move">
                <div class="info-header">Total Pending Orders</div>
            </div>
            <span id="totalPendingOrders" class="info-box-number text-right h4">0</span>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animate values for each info box
        animateValue('totalCategories', 0, <?= format_num($totalCategories) ?>, 2000);
        animateValue('totalProducts', 0, <?= format_num($totalProducts) ?>, 2000);
        animateValue('commission', 0, <?= format_num($commission) ?>, 2000);
        animateValue('totalCommission', 0, <?= format_num($commission * $totalSales) ?>, 2000);
        animateValue('totalSales', 0, <?= format_num($totalSales) ?>, 2000);
        animateValue('totalPendingOrders', 0, <?= format_num($totalPendingOrders) ?>, 2000);
        // Add more animateValue calls for additional info boxes as needed
    });

    function animateValue(id, start, end, duration) {
        let range = end - start;
        let current = start;
        let increment = end > start ? 1 : -1;
        let stepTime = Math.abs(Math.floor(duration / range));
        let obj = document.getElementById(id);

        if (Math.abs(end - start) < 1) {
            increment = end > start ? 0.01 : -0.01;
            stepTime = duration / (Math.abs(end - start) * 100);
        }

        let timer = setInterval(function() {
            current += increment;
            obj.innerHTML = current.toFixed(2); // Adjust toFixed value as needed
            if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
                clearInterval(timer);
                obj.innerHTML = end.toFixed(2); // Ensure the final value is correct
            }
        }, stepTime);
    }
</script>

    
  </div>

    <?php
    // Fetch the top products data
    $vendor_id = $_settings->userdata('id');
    $top_products_data = [];
    $top_products_query = $conn->query("
        SELECT oi.product_id, SUM(oi.quantity) AS total_sold 
        FROM order_items oi 
        INNER JOIN product_list pl ON oi.product_id = pl.id 
        WHERE pl.vendor_id = '{$vendor_id}'
        GROUP BY oi.product_id 
        ORDER BY total_sold DESC 
        LIMIT 5
    ");

    while ($row = $top_products_query->fetch_assoc()) {
        $product_name_query = $conn->query("SELECT name FROM product_list WHERE id = '{$row['product_id']}'");
        $product_name = $product_name_query->fetch_assoc()['name'];
        $top_products_data[$product_name] = $row['total_sold'];
    }
    ?>

    <!-- Top Products Chart -->
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Top Products</h3>
          </div>
          <div class="card-body">
            <canvas id="topProductsChart" width="100%" height="200"></canvas>
          </div>
        </div>
      </div>
    </div>

    <script>
      document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById('topProductsChart').getContext('2d');
        var topProductsData = <?php echo json_encode($top_products_data); ?>;
        
        var labels = Object.keys(topProductsData);
        var data = Object.values(topProductsData);

        new Chart(ctx, {
          type: 'horizontalBar',
          data: {
            labels: labels,
            datasets: [{
              label: 'Total Sold',
              data: data,
              backgroundColor: 'rgba(75, 192, 192, 0.2)',
              borderColor: 'rgba(75, 192, 192, 1)',
              borderWidth: 1
            }]
          },
          options: {
            scales: {
              xAxes: [{
                ticks: {
                  beginAtZero: true
                }
              }]
            }
          }
        });
      });
    </script>
<?php
include_once 'monthly_revenue.php';
?>

<?php require_once('orderstatusbreakdown.php') ?>
