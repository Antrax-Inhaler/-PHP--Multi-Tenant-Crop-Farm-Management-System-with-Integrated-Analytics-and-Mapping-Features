<?php require_once('topbar.php') ?>

<h1 class="">Manage Ecommerce</h1>
<style>
  #cover-image{
    width:calc(100%);
    height:50vh;
    object-fit:cover;
    object-position:center center;
  }
</style>
<hr>
<div class="row">
  <div class="col-6 col-sm-4 col-md-4">
    <div class="info-box">
      <span class="info-box-icon bg-gradient-primary elevation-1"><i class="fas fa-th-list"></i></span>

      <div class="info-box-content">
        <span class="info-box-text">Total Categories</span>
        <span class="iinfo-box-number text-right h4">
        <?php 
                $total_categories_query = $conn->query("SELECT count(id) as total FROM category_list WHERE delete_flag = 0")->fetch_assoc();
                $total_categories = $total_categories_query['total'];
                echo format_num($total_categories);
                ?>
        </span>
      </div>
      <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
  </div>

  <div class="col-6 col-sm-4 col-md-4">
    <div class="info-box">
      <span class="info-box-icon bg-gradient-dark elevation-1"><i class="fas fa-th-list"></i></span>

      <div class="info-box-content">
        <span class="info-box-text">Total Shop Type</span>
        <span class="iinfo-box-number text-right h4">
          <?php 
            $total_shop_type = $conn->query("SELECT count(id) as total FROM shop_type_list WHERE delete_flag = 0")->fetch_assoc()['total'];
            echo format_num($total_shop_type);
          ?>
        </span>
      </div>
      <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
  </div>

  <div class="col-6 col-sm-4 col-md-4">
    <div class="info-box">
      <span class="info-box-icon bg-gradient-secondary elevation-1"><i class="fas fa-boxes"></i></span>

      <div class="info-box-content">
        <span class="info-box-text">Total Products</span>
        <span class="iinfo-box-number text-right h4">
          <?php 
            $total_products = $conn->query("SELECT count(id) as total FROM product_list WHERE delete_flag = 0")->fetch_assoc()['total'];
            echo format_num($total_products);
          ?>
        </span>
      </div>
      <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
  </div>

  <div class="col-6 col-sm-4 col-md-4">
    <div class="info-box">
      <span class="info-box-icon bg-gradient-danger elevation-1"><i class="fas fa-shopping-bag"></i></span>

      <div class="info-box-content">
        <span class="info-box-text">Total Orders</span>
        <span class="info-box-number text-right h4">
          <?php 
            $total_orders = $conn->query("SELECT count(id) as total FROM order_list")->fetch_assoc()['total'];
            echo format_num($total_orders);
          ?>
        </span>
      </div>
      <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
  </div>

  <div class="col-6 col-sm-4 col-md-4">
    <div class="info-box">
      <span class="info-box-icon bg-gradient-warning elevation-1"><i class="fas fa-list"></i></span>

      <div class="info-box-content">
        <span class="info-box-text">Total Pending Orders</span>
        <span class="info-box-number text-right h4">
          <?php 
            $total_pending_orders = $conn->query("SELECT count(id) as total FROM order_list WHERE `status` = 0")->fetch_assoc()['total'];
            echo format_num($total_pending_orders);
          ?>
        </span>
      </div>
      <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
  </div>

  <div class="col-6 col-sm-4 col-md-4">
    <div class="info-box">
      <span class="info-box-icon bg-gradient-info elevation-1"><i class="fas fa-user-friends"></i></span>

      <div class="info-box-content">
        <span class="info-box-text">Total Clients</span>
        <span class="info-box-number text-right h4">
          <?php 
            $total_clients = $conn->query("SELECT count(id) as total FROM client_list WHERE delete_flag = 0")->fetch_assoc()['total'];
            echo format_num($total_clients);
          ?>
        </span>
      </div>
      <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
  </div>

  <div class="col-6 col-sm-4 col-md-4">
    <div class="info-box">
      <span class="info-box-icon bg-gradient-info elevation-1"><i class="fas fa-user-friends"></i></span>

      <div class="info-box-content">
        <span class="info-box-text">Total Reviews</span>
        <span class="info-box-number text-right h4">
          <?php 
            $total_reviews = $conn->query("SELECT count(id) as total FROM review")->fetch_assoc()['total'];
            echo format_num($total_reviews);
          ?>
        </span>
      </div>
      <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
  </div>
</div>
<?php
// Fetch data from the database
$category_data = [];
$product_count_query = $conn->query("SELECT category_id, COUNT(id) as total FROM product_list GROUP BY category_id");
while ($row = $product_count_query->fetch_assoc()) {
    $category_data[$row['category_id']] = $row['total'];
}

// Prepare data for pie chart
$pie_labels = [];
$pie_data = [];
foreach ($category_data as $category_id => $total_products) {
    $category_name_query = $conn->query("SELECT name FROM category_list WHERE id = '{$category_id}'");
    $category_name = $category_name_query->fetch_assoc()['name'];
    $pie_labels[] = $category_name;
    $pie_data[] = $total_products;
}

// Prepare data for bar chart
$bar_labels = $pie_labels;
$bar_data = $pie_data;

// Prepare data for line chart
$line_labels = [];
$line_data = [];
for ($i = 1; $i <= 12; $i++) {
    $month = date('F', mktime(0, 0, 0, $i, 1));
    $line_labels[] = $month;
    $total_products_month = $conn->query("SELECT COUNT(id) as total FROM product_list WHERE MONTH(date_created) = '{$i}'")->fetch_assoc()['total'];
    $line_data[] = $total_products_month;
}

// Fetch data for top selling products
$top_products_data = [];
$top_products_query = $conn->query("SELECT product_id, SUM(quantity) as total_sold FROM order_items GROUP BY product_id ORDER BY total_sold DESC LIMIT 5");
while ($row = $top_products_query->fetch_assoc()) {
    $product_name_query = $conn->query("SELECT name FROM product_list WHERE id = '{$row['product_id']}'");
    $product_name = $product_name_query->fetch_assoc()['name'];
    $top_products_data[$product_name] = $row['total_sold'];
}

$top_product_labels = array_keys($top_products_data);
$top_product_totals = array_values($top_products_data);

// Fetch data for sales overview
$sales_data = [];
$sales_query = $conn->query("SELECT MONTH(date_created) as month, SUM(total_amount) as total_sales FROM order_list GROUP BY MONTH(date_created)");
while ($row = $sales_query->fetch_assoc()) {
    $sales_data[$row['month']] = $row['total_sales'];
}

$sales_labels = [];
$sales_totals = [];
for ($i = 1; $i <= 12; $i++) {
    $sales_labels[] = date('F', mktime(0, 0, 0, $i, 1));
    $sales_totals[] = $sales_data[$i] ?? 0;
}
?>

<!-- Include Chart.js library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="row">
  <!-- Bar Chart -->
  <div class="col-md-6">
    <canvas id="barChart" style="min-height: 450px; max-height: 450px; width: 100%;"></canvas>
  </div>

  <!-- Pie Chart -->
  <div class="col-md-3">
    <canvas id="pieChart" style="min-height: 225px; max-height: 225px; width: 100%;"></canvas>
  </div>

  <!-- Line Chart -->
  <div class="col-md-3">
    <canvas id="lineChart" style="min-height: 225px; max-height: 225px; width: 100%;"></canvas>
  </div>
</div>
<div class="row">
  <!-- Sales Overview Chart -->
  <div class="col-md-4">
    <canvas id="salesOverviewChart"></canvas>
  </div>

  <!-- Top Selling Products Chart -->
  <div class="col-md-4">
    <canvas id="topSellingProductsChart"></canvas>
  </div>
</div>

<!-- Initialize the charts -->
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Bar Chart
    var barCtx = document.getElementById('barChart').getContext('2d');
    new Chart(barCtx, {
      type: 'bar',
      data: {
        labels: <?php echo json_encode($bar_labels); ?>,
        datasets: [{
          label: 'Total Products',
          data: <?php echo json_encode($bar_data); ?>,
          backgroundColor: 'rgba(75, 192, 192, 0.2)',
          borderColor: 'rgba(75, 192, 192, 1)',
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });

    // Pie Chart
    var pieCtx = document.getElementById('pieChart').getContext('2d');
    new Chart(pieCtx, {
      type: 'pie',
      data: {
        labels: <?php echo json_encode($pie_labels); ?>,
        datasets: [{
          data: <?php echo json_encode($pie_data); ?>,
          backgroundColor: [
            'rgba(255, 99, 132, 0.2)',
            'rgba(54, 162, 235, 0.2)',
            'rgba(255, 206, 86, 0.2)',
            'rgba(75, 192, 192, 0.2)',
            'rgba(153, 102, 255, 0.2)',
            'rgba(255, 159, 64, 0.2)'
          ],
          borderColor: [
            'rgba(255, 99, 132, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(75, 192, 192, 1)',
            'rgba(153, 102, 255, 1)',
            'rgba(255, 159, 64, 1)'
          ],
          borderWidth: 1
        }]
      },
      options: {
        responsive: true
      }
    });

    // Line Chart
    var lineCtx = document.getElementById('lineChart').getContext('2d');
    new Chart(lineCtx, {
      type: 'line',
      data: {
        labels: <?php echo json_encode($line_labels); ?>,
        datasets: [{
          label: 'Total Products',
          data: <?php echo json_encode($line_data); ?>,
          backgroundColor: 'rgba(153, 102, 255, 0.2)',
          borderColor: 'rgba(153, 102, 255, 1)',
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });

    // Sales Overview Chart
    var salesCtx = document.getElementById('salesOverviewChart').getContext('2d');
    new Chart(salesCtx, {
      type: 'line',
      data: {
        labels: <?php echo json_encode($sales_labels); ?>,
        datasets: [{
          label: 'Total Sales',
          data: <?php echo json_encode($sales_totals); ?>,
          backgroundColor: 'rgba(255, 206, 86, 0.2)',
          borderColor: 'rgba(255, 206, 86, 1)',
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });

    // Top Selling Products Chart
    var topProductsCtx = document.getElementById('topSellingProductsChart').getContext('2d');
    new Chart(topProductsCtx, {
      type: 'bar',
      data: {
        labels: <?php echo json_encode($top_product_labels); ?>,
        datasets: [{
          label: 'Total Sold',
          data: <?php echo json_encode($top_product_totals); ?>,
          backgroundColor: 'rgba(75, 192, 192, 0.2)',
          borderColor: 'rgba(75, 192, 192, 1)',
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });
  });
</script>
