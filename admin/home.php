
<h1 class="">Welcome to <?php echo $_settings->info('name') ?> - Admin Side</h1>
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
  <div class="col-12 col-sm-4 col-md-4">
    <div class="info-box">
      <span class="info-box-icon bg-gradient-primary elevation-1"><i class="fas fa-th-list"></i></span>

      <div class="info-box-content">
        <span class="info-box-text">Total Categories</span>
        <span class="iinfo-box-number text-right h4">
        <?php 
                $user_id = $_settings->userdata('id');
                $total_members_query = $conn->query("SELECT count(id) as total FROM vendor_list WHERE user_id = '{$user_id}' AND delete_flag = 0")->fetch_assoc();
                $total_members = $total_members_query['total'];
                echo format_num($total_members);
                ?>
        </span>
      </div>
      <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
  </div>
  <div class="col-12 col-sm-4 col-md-4">
    <div class="info-box">
        <span class="info-box-icon bg-gradient-success elevation-1"><i class="fas fa-seedling"></i></span>

        <div class="info-box-content">
            <span class="info-box-text">Total Crops</span>
            <span class="info-box-number text-right h4">
                <?php 
                $user_id = $_settings->userdata('id');
                $total_crops_query = $conn->query("SELECT count(Id) as total FROM crop WHERE VendorId IN (SELECT id FROM vendor_list WHERE user_id = '{$user_id}' AND delete_flag = 0)")->fetch_assoc();
                $total_crops = $total_crops_query['total'];
                echo format_num($total_crops);
                ?>
            </span>
        </div>
        <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
</div>
<div class="col-12 col-sm-4 col-md-4">
    <div class="info-box">
        <span class="info-box-icon bg-gradient-info elevation-1"><i class="fas fa-tractor"></i></span>

        <div class="info-box-content">
            <span class="info-box-text">Total Farms</span>
            <span class="info-box-number text-right h4">
                <?php 
                $user_id = $_settings->userdata('id');
                $total_farms_query = $conn->query("SELECT count(Id) as total FROM farm WHERE VendorListId IN (SELECT id FROM vendor_list WHERE user_id = '{$user_id}' AND delete_flag = 0)")->fetch_assoc();
                $total_farms = $total_farms_query['total'];
                echo format_num($total_farms);
                ?>
            </span>
        </div>
        <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
</div>

  <div class="col-12 col-sm-4 col-md-4">
    <div class="info-box">
      <span class="info-box-icon bg-gradient-dark elevation-1"><i class="fas fa-th-list"></i></span>

      <div class="info-box-content">
        <span class="info-box-text">Total Shop Type</span>
        <span class="iinfo-box-number text-right h4">
          <?php 
            $total = $conn->query("SELECT count(id) as total FROM shop_type_list where delete_flag = 0 ")->fetch_assoc()['total'];
            echo format_num($total);
          ?>
          <?php ?>
        </span>
      </div>
      <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
  </div>
  <div class="col-12 col-sm-4 col-md-4">
    <div class="info-box">
      <span class="info-box-icon bg-gradient-secondary elevation-1"><i class="fas fa-boxes"></i></span>

      <div class="info-box-content">
        <span class="info-box-text">Total Products</span>
        <span class="iinfo-box-number text-right h4">
          <?php 
            $total = $conn->query("SELECT count(id) as total FROM product_list where delete_flag = 0 ")->fetch_assoc()['total'];
            echo format_num($total);
          ?>
          <?php ?>
        </span>
      </div>
      <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
  </div>
  <div class="col-12 col-sm-4 col-md-4">
    <div class="info-box">
      <span class="info-box-icon bg-gradient-light border elevation-1"><i class="fas fa-users"></i></span>

      <div class="info-box-content">
        <span class="info-box-text">Members</span>
        <span class="iinfo-box-number text-right h4">
          <?php 
            $total = $conn->query("SELECT count(id) as total FROM vendor_list WHERE delete_flag = 0 AND user_id = '$user_id'")->fetch_assoc()['total'];
            echo format_num($total);
          ?>
          <?php ?>
        </span>
      </div>
      <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
  </div><div class="col-12 col-sm-4 col-md-4">
    <div class="info-box">
      <span class="info-box-icon bg-gradient-maroon elevation-1"><i class="fas fa-user-friends"></i></span>

      <div class="info-box-content">
        <span class="info-box-text">Clients</span>
        <span class="iinfo-box-number text-right h4">
          <?php 
            $total = $conn->query("SELECT count(id) as total FROM client_list where delete_flag = 0 ")->fetch_assoc()['total'];
            echo format_num($total);
          ?>
          <?php ?>
        </span>
      </div>
      <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
  </div>
  <div class="col-12 col-sm-4 col-md-4">
    <div class="info-box">
      <span class="info-box-icon bg-gradient-warning elevation-1"><i class="fas fa-list"></i></span>

      <div class="info-box-content">
        <span class="info-box-text">Total Pending Orders</span>
        <span class="iinfo-box-number text-right h4">
          <?php 
            $total = $conn->query("SELECT count(id) as total FROM order_list where `status` = 0 ")->fetch_assoc()['total'];
            echo format_num($total);
          ?>
          <?php ?>
        </span>
      </div>
      <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
  </div>

  <div class="col-12 col-sm-4 col-md-4">
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

  <div class="col-12 col-sm-4 col-md-4">
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
$top_products_data = [];
$top_products_query = $conn->query("SELECT product_id, SUM(quantity) as total_sold FROM order_items GROUP BY product_id ORDER BY total_sold DESC LIMIT 5");
while ($row = $top_products_query->fetch_assoc()) {
    $product_name_query = $conn->query("SELECT name FROM product_list WHERE id = '{$row['product_id']}'");
    $product_name = $product_name_query->fetch_assoc()['name'];
    $top_products_data[$product_name] = $row['total_sold'];
}

$top_product_labels = array_keys($top_products_data);
$top_product_totals = array_values($top_products_data);

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
$top_products_data = [];
$top_products_query = $conn->query("SELECT product_id, SUM(quantity) as total_sold FROM order_items GROUP BY product_id ORDER BY total_sold DESC LIMIT 5");
while ($row = $top_products_query->fetch_assoc()) {
    $product_name_query = $conn->query("SELECT name FROM product_list WHERE id = '{$row['product_id']}'");
    $product_name = $product_name_query->fetch_assoc()['name'];
    $top_products_data[$product_name] = $row['total_sold'];
}

$top_product_labels = array_keys($top_products_data);
$top_product_totals = array_values($top_products_data);
$category_data = [];
$product_count_query = $conn->query("SELECT category_id, COUNT(id) as total FROM product_list GROUP BY category_id");
while ($row = $product_count_query->fetch_assoc()) {
    $category_data[$row['category_id']] = $row['total'];
}

$category_labels = [];
$category_totals = [];
foreach ($category_data as $category_id => $total_products) {
    $category_name_query = $conn->query("SELECT name FROM category_list WHERE id = '{$category_id}'");
    $category_name = $category_name_query->fetch_assoc()['name'];
    $category_labels[] = $category_name;
    $category_totals[] = $total_products;
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

  <!-- Products by Category Chart -->
  <div class="col-md-4">
    <canvas id="productsByCategoryChart"></canvas>
  </div>
</div>

<script>
        // Sales Overview Line Chart
        var salesCtx = document.getElementById('salesOverviewChart').getContext('2d');
        var salesOverviewChart = new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($sales_labels); ?>,
                datasets: [{
                    label: 'Total Sales',
                    data: <?php echo json_encode($sales_totals); ?>,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    fill: true
                }]
            }
        });

        // Top Selling Products Bar Chart
        var topProductsCtx = document.getElementById('topSellingProductsChart').getContext('2d');
        var topSellingProductsChart = new Chart(topProductsCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($top_product_labels); ?>,
                datasets: [{
                    label: 'Total Sold',
                    data: <?php echo json_encode($top_product_totals); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            }
        });

        // Products by Category Pie Chart
        var categoryCtx = document.getElementById('productsByCategoryChart').getContext('2d');
        var productsByCategoryChart = new Chart(categoryCtx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode($category_labels); ?>,
                datasets: [{
                    data: <?php echo json_encode($category_totals); ?>,
                    backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545', '#6c757d'],
                }]
            }
        });
    </script>
<script>
// Pie Chart
var pieChartCanvas = $('#pieChart').get(0).getContext('2d');
var pieChartData = {
  labels: <?php echo json_encode($pie_labels); ?>,
  datasets: [
    {
      data: <?php echo json_encode($pie_data); ?>,
      backgroundColor : [<?php echo "'#007bff', '#28a745', '#ffc107', '#dc3545', '#f8f9fa', '#6c757d', '#17a2b8'"; ?>],
    }
  ]
};
var pieChart = new Chart(pieChartCanvas, {
  type: 'pie', 
  data: pieChartData
});

// Bar Chart
var barChartCanvas = $('#barChart').get(0).getContext('2d');
var barChartData = {
  labels: <?php echo json_encode($bar_labels); ?>,
  datasets: [
    {
      label: 'Number of Products',
      backgroundColor: 'rgba(54, 162, 235, 0.5)',
      borderColor: 'rgba(54, 162, 235, 1)',
      borderWidth: 1,
      data: <?php echo json_encode($bar_data); ?>,
    }
  ]
};
var barChart = new Chart(barChartCanvas, {
  type: 'bar', 
  data: barChartData
});

// Line Chart
var lineChartCanvas = $('#lineChart').get(0).getContext('2d');
var lineChartData = {
  labels: <?php echo json_encode($line_labels); ?>,
  datasets: [
    {
      label: 'Number of Products',
      fill: false,
      borderColor: 'rgba(255, 99, 132, 1)',
      borderWidth: 1,
      data: <?php echo json_encode($line_data); ?>,
    }
  ]
};
var lineChart = new Chart(lineChartCanvas, {
  type: 'line', 
  data: lineChartData
});
</script>
<?php
// Fetch data for top selling products

?>

<script>
var ctx = document.getElementById('topSellingProductsChart').getContext('2d');
var topSellingProductsChart = new Chart(ctx, {
    type: 'horizontalBar',
    data: {
        labels: <?php echo json_encode($top_product_labels); ?>,
        datasets: [{
            label: 'Total Sold',
            data: <?php echo json_encode($top_product_totals); ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.5)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    }
});
</script>

<div class="clear-fix mb-2">
    <div class="text-center w-100">
      <img src="<?= validate_image($_settings->info('cover')) ?>" alt="System Cover image" class="w-100" id="cover-image">
    </div>
  </div>
