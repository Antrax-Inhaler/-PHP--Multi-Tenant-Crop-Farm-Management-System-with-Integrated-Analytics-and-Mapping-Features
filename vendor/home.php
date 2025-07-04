
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
      
        @media (max-width: 1000px) {
    /* Hide desktop menu on mobile screens */

    .info-header {
            display: inline-block;
            white-space: nowrap;
            animation: moveLeft 10s linear infinite;
        }

        @keyframes moveLeft {
            0% { transform: translateX(100%); }
            100% { transform: translateX(-120%); }
        }

        .move{
            overflow: hidden;
            width: 200px;
        }

        .info-box-content {
            white-space: nowrap;
            overflow: hidden;

        }

        .info-box-number {
            white-space: nowrap;
        }
    /* Add a new element for the mobile menu icon */
    .move {
     width: 80%;
    }
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
      $farm_count_query = "SELECT COUNT(*) as farm_count FROM farm WHERE VendorListId = '{$_settings->userdata('id')}'";
      $farm_count_result = $conn->query($farm_count_query);
      $farm_count_row = $farm_count_result->fetch_assoc();
      $farm_count = format_num($farm_count_row['farm_count']);
      
      $crop_count_query = "SELECT COUNT(*) as crop_count FROM crop WHERE VendorId = '{$_settings->userdata('id')}'";
      $crop_count_result = $conn->query($crop_count_query);
      $crop_count_row = $crop_count_result->fetch_assoc();
      $crop_count = format_num($crop_count_row['crop_count']);
      
      $pest_disease_count_query = "SELECT COUNT(DISTINCT crop.Id) as pest_disease_count FROM crop JOIN croppestdisease ON crop.Id = croppestdisease.CropID WHERE crop.VendorId = '{$_settings->userdata('id')}'";
      $pest_disease_count_result = $conn->query($pest_disease_count_query);
      $pest_disease_count_row = $pest_disease_count_result->fetch_assoc();
      $pest_disease_count = format_num($pest_disease_count_row['pest_disease_count']);
      
      // Assuming you have the values for the other metrics stored in variables like $totalCategories, $totalProducts, $commission, $totalSales, $totalPendingOrders, $numberOfHarvest, etc.
      $totalCategories = 10; // Replace with actual value
      $totalProducts = 20; // Replace with actual value
      $commission = 1000; // Replace with actual value
      $totalSales = 5000; // Replace with actual value
      $totalPendingOrders = 30; // Replace with actual value
      $numberOfHarvest = 0; // Replace with actual value
    ?>

    <!-- Total Categories Info Box -->
    <div class="col-6 col-sm-4 col-md-4">
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
        <div class="col-6 col-sm-4 col-md-4">
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
        <div class="col-6 col-sm-4 col-md-4">
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
        <div class="col-6 col-sm-4 col-md-4">
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
        <div class="col-6 col-sm-4 col-md-4">
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
        <div class="col-6 col-sm-4 col-md-4">
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

        <!-- Number of Farms Info Box -->
        <div class="col-6 col-sm-4 col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-gradient-primary elevation-1"><i class="fas fa-th-list"></i></span>
                <div class="info-box-content">
                    <div class="move">
                        <div class="info-header">Number of Farms</div>
                    </div>
                    <span id="numberOfFarms" class="info-box-number text-right h4"><?= $farm_count ?></span>
                </div>
            </div>
        </div>

        <!-- Total Crops Info Box -->
        <div class="col-6 col-sm-4 col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-gradient-secondary elevation-1"><i class="fas fa-boxes"></i></span>
                <div class="info-box-content">
                    <div class="move">
                        <div class="info-header">Total Crops</div>
                    </div>
                    <span id="totalCrops" class="info-box-number text-right h4"><?= $crop_count ?></span>
                </div>
            </div>
        </div>

        <!-- Crop with Pest and Disease Info Box -->
        <div class="col-6 col-sm-4 col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-gradient-success elevation-1"><i class="fas fa-dollar-sign"></i></span>
                <div class="info-box-content">
                    <div class="move">
                        <div class="info-header">Crop with Pest and Disease</div>
                    </div>
                    <span id="cropWithPestDisease" class="info-box-number text-right h4"><?= $pest_disease_count ?></span>
                </div>
            </div>
        </div>

        <!-- Number of Harvest Info Box -->
        <div class="col-6 col-sm-4 col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-gradient-info elevation-1"><i class="fas fa-money-bill-wave"></i></span>
                <div class="info-box-content">
                    <div class="move">
                        <div class="info-header">Number of Harvest</div>
                    </div>
                    <span id="numberOfHarvest" class="info-box-number text-right h4">₱<?= format_num($numberOfHarvest) ?></span>
                </div>
            </div>
        </div>

        <!-- Placeholder Info Box for example -->
        <div class="col-6 col-sm-4 col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-gradient-warning elevation-1"><i class="fas fa-chart-line"></i></span>
                <div class="info-box-content">
                    <div class="move">
                        <div class="info-header">Placeholder Header</div>
                    </div>
                    <span id="placeholderId" class="info-box-number text-right h4">₱<?= format_num(0) ?></span>
                </div>
            </div>
        </div>
    </div>

    <script>
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

        document.addEventListener('DOMContentLoaded', function() {
            // Adjust the end values as needed
            animateValue('totalCategories', 0, <?= format_num($totalCategories) ?>, 2000);
            animateValue('totalProducts', 0, <?= format_num($totalProducts) ?>, 2000);
            animateValue('commission', 0, <?= format_num($commission) ?>, 2000);
            animateValue('totalCommission', 0, <?= format_num($commission * $totalSales) ?>, 2000);
            animateValue('totalSales', 0, <?= format_num($totalSales) ?>, 2000);
            animateValue('totalPendingOrders', 0, <?= format_num($totalPendingOrders) ?>, 2000);
            animateValue('numberOfFarms', 0, <?= format_num($farm_count) ?>, 2000);
            animateValue('totalCrops', 0, <?= format_num($crop_count) ?>, 2000);
            animateValue('cropWithPestDisease', 0, <?= format_num($pest_disease_count) ?>, 2000);
            animateValue('numberOfHarvest', 0, <?= format_num($numberOfHarvest) ?>, 2000);
            animateValue('placeholderId', 0, <?= format_num(0) ?>, 2000);
            // Add other info boxes with their respective IDs and end values
        });
    </script>
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
$vendor_id = $_settings->userdata('id');

// Get current year and month if not selected
$selected_year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
$selected_month = isset($_GET['month']) ? (int)$_GET['month'] : date('m');

// Fetch available years for filtering (from order_list)
$yearsQuery = $conn->query("SELECT DISTINCT YEAR(date_updated) AS year FROM order_list WHERE vendor_id = '{$vendor_id}' ORDER BY year DESC");
$availableYears = [];
while ($row = $yearsQuery->fetch_assoc()) {
    $availableYears[] = $row['year'];
}

// Fetch Order Status Data (Filtered by Year and Month)
$orderStatusData = array();
$statusNames = [
    0 => 'Pending',
    1 => 'Confirmed',
    2 => 'Packed',
    3 => 'Out for Delivery',
    4 => 'Delivered',
    5 => 'Cancelled'
];

$statusQuery = $conn->query("SELECT `status`, COUNT(id) AS order_count FROM order_list 
    WHERE vendor_id = '{$vendor_id}' 
    AND YEAR(date_updated) = '{$selected_year}' 
    AND MONTH(date_updated) = '{$selected_month}' 
    GROUP BY `status`");
while ($row = $statusQuery->fetch_assoc()) {
    $orderStatusData[$statusNames[$row['status']]] = $row['order_count'];
}

// Fetch Monthly Sales Data (Filtered by Year and Month)
$salesQuery = $conn->query("SELECT SUM(total_amount) AS sales FROM order_list 
    WHERE vendor_id = '{$vendor_id}' 
    AND YEAR(date_updated) = '{$selected_year}' 
    AND MONTH(date_updated) = '{$selected_month}'");
$monthlySalesRow = $salesQuery->fetch_assoc();
$monthlySales = $monthlySalesRow['sales'] ?? 0;
?>
  <!-- Include Chart.js library -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <style>
    /* General body and container styles */

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    .charts-wrapper {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: space-between;
    }

    /* Uniform card design for all charts */
    .chart-card {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        padding: 20px;
        flex: 1;
        min-width: 300px; /* Ensure the card doesn't get too small */
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .chart-title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 15px;
        text-align: center;
        color: #333;
    }

    /* Responsive behavior: stack cards vertically on smaller screens */
    @media (max-width: 768px) {
        .charts-wrapper {
            flex-direction: column;
            align-items: center;
        }

        .chart-card {
            width: 100%;
        }
    }

    /* Card header styles for each chart */
    .chart-card canvas {
        width: 100% !important;
        height: 300px !important; /* Adjust height to ensure charts are visually balanced */
    }
</style>

<div class="container mt-4">
    <hr>
    <h2 class="text-center mb-4">Dashboard Charts</h2>
    <div>
            <label for="yearFilter">Year:</label>
            <select id="yearFilter" class="form-control">
                <?php foreach ($availableYears as $year): ?>
                    <option value="<?= $year ?>" <?= ($selected_year == $year) ? 'selected' : '' ?>>
                        <?= $year ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="monthFilter">Month:</label>
            <select id="monthFilter" class="form-control">
                <?php for ($i = 1; $i <= 12; $i++): ?>
                    <option value="<?= $i ?>" <?= ($selected_month == $i) ? 'selected' : '' ?>>
                        <?= date('F', mktime(0, 0, 0, $i, 1)) ?>
                    </option>
                <?php endfor; ?>
            </select>
        </div>
    <div class="charts-wrapper">
        <!-- Order Status Chart -->
        <div class="chart-card">
            <h2 class="chart-title">Order Status Breakdown</h2>
            <canvas id="orderStatusChart"></canvas>
        </div>

        <!-- Monthly Sales Chart -->
        <div class="chart-card">
            <h2 class="chart-title">Monthly Sales</h2>
            <canvas id="salesChart"></canvas>
        </div>

        <!-- Top Products Chart -->
        <div class="chart-card">
            <h2 class="chart-title">Top Products</h2>
            <canvas id="topProductsChart"></canvas>
        </div>
    </div>
</div>
<script>
document.getElementById('yearFilter').addEventListener('change', updateFilter);
document.getElementById('monthFilter').addEventListener('change', updateFilter);

function updateFilter() {
    let selectedYear = document.getElementById('yearFilter').value;
    let selectedMonth = document.getElementById('monthFilter').value;
    window.location.href = "?year=" + selectedYear + "&month=" + selectedMonth;
}
</script>

  <!-- JavaScript Code for Order Status Breakdown Chart -->
  <script>
    // JavaScript for Pie Chart Rendering
    const ctxOrderStatus = document.getElementById('orderStatusChart').getContext('2d');
const orderStatusData = <?= json_encode($orderStatusData) ?>;
const labels = Object.keys(orderStatusData);
const data = Object.values(orderStatusData);
const backgroundColors = [
    'rgba(255, 99, 132, 0.7)', 'rgba(54, 162, 235, 0.7)',
    'rgba(255, 206, 86, 0.7)', 'rgba(75, 192, 192, 0.7)',
    'rgba(153, 102, 255, 0.7)', 'rgba(255, 159, 64, 0.7)'
];

const orderStatusChart = new Chart(ctxOrderStatus, {
    type: 'pie',
    data: {
        labels: labels,
        datasets: [{
            label: 'Order Status',
            data: data,
            backgroundColor: backgroundColors,
            borderWidth: 1
        }]
    },
    options: { responsive: true, maintainAspectRatio: false }
});
  </script>
<?php
// Sample PHP code for database connection

// Fetch Monthly Sales Data
$monthlySalesData = array();
$salesQuery = $conn->query("
  SELECT 
    MONTH(date_updated) AS month,
    DATE_FORMAT(date_updated, '%M') AS month_name,
    SUM(total_amount) AS sales
  FROM order_list
  WHERE 
    vendor_id = '{$_settings->userdata('id')}' AND
    YEAR(date_updated) = YEAR(CURDATE())
  GROUP BY MONTH(date_updated)
  ORDER BY MONTH(date_updated)
");
while ($row = $salesQuery->fetch_assoc()) {
  $monthlySalesData[$row['month_name']] = $row['sales'];
}
$categoryData = array();
$categoryQuery = $conn->query("
  SELECT name
  FROM category_list
  WHERE vendor_id = '{$_settings->userdata('id')}' AND delete_flag = 0
");
while ($row = $categoryQuery->fetch_assoc()) {
  $categoryData[$row['name']] = 0; // Initialize product count for each category
}

// Count Products in Each Category
$productQuery = $conn->query("
  SELECT category_id, COUNT(id) AS total_products
  FROM product_list
  WHERE vendor_id = '{$_settings->userdata('id')}' AND delete_flag = 0
  GROUP BY category_id
");
while ($row = $productQuery->fetch_assoc()) {
  // Update product count for the corresponding category
  if (isset($categoryData[$row['category_id']])) {
    $categoryData[$row['category_id']] = $row['total_products'];
  }
}
?>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    /* Add some custom styles */
    .chart-container {
      margin-top: 40px;
      width: 200px;
    }
    .chart-title {
      text-align: center;
      margin-bottom: 20px;
    }
  </style>

 
  <!-- JavaScript Code for Monthly Sales Chart -->
  <script>
    // JavaScript for Chart Rendering
    const ctxSales = document.getElementById('salesChart').getContext('2d');
new Chart(ctxSales, {
    type: 'bar',
    data: {
        labels: ['Sales'],
        datasets: [{
            label: 'Monthly Sales',
            data: [<?= $monthlySales ?>],
            backgroundColor: '#2ddc9a',
            borderColor: '#b49f81',
            borderWidth: 1
        }]
    },
    options: {
        scales: { y: { beginAtZero: true } }
    }
});
  </script>

 
  <!-- JavaScript Code for Product Category Distribution Chart -->
  <script>
    // Define custom colors for categories
    const categoryColors = ['#2ddc9a', '#ff6384', '#36a2eb', '#ffce56', '#9966ff', '#ff9f40', '#4bc0c0', '#ffcd56', '#37d8e4', '#e95c5c'];

    // JavaScript for Bar Chart Rendering
    const ctxCategoryBar = document.getElementById('categoryBarChart').getContext('2d');
    const categoryBarChart = new Chart(ctxCategoryBar, {
      type: 'horizontalBar',
      data: {
        labels: <?= json_encode(array_keys($categoryData)) ?>,
        datasets: [{
          label: 'Product Categories',
          data: <?= json_encode(array_values($categoryData)) ?>,
          backgroundColor: categoryColors,
          borderWidth: 1
        }]
      },
      options: {
        scales: {
          x: {
            beginAtZero: true
          }
        }
      }
    });
  </script>
