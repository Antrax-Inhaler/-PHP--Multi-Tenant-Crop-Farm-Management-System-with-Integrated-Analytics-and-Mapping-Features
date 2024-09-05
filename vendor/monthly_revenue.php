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
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sales and Product Category Charts</title>
  <!-- Include Chart.js library -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    /* Add some custom styles */
    .chart-container {
      margin-top: 40px;
    }
    .chart-title {
      text-align: center;
      margin-bottom: 20px;
    }
  </style>
</head>
<body>
  <!-- Container for Monthly Sales Chart -->
  <div class="chart-container">
    <h2 class="chart-title">Monthly Sales Chart</h2>
    <div class="row">
      <div class="col-12">
        <canvas id="salesChart"></canvas>
      </div>
    </div>
  </div>

  <!-- JavaScript Code for Monthly Sales Chart -->
  <script>
    // JavaScript for Chart Rendering
    const ctxSales = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(ctxSales, {
      type: 'bar',
      data: {
        labels: <?= json_encode(array_keys($monthlySalesData)) ?>,
        datasets: [{
          label: 'Monthly Sales',
          data: <?= json_encode(array_values($monthlySalesData)) ?>,
          backgroundColor: '#2ddc9a', // Blue color with transparency
          borderColor: '#b49f81', // Blue color
          borderWidth: 1
        }] 
      },
      options: {
        scales: {
          y: {
            beginAtZero: true
          }
        }
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
</body>
</html>
