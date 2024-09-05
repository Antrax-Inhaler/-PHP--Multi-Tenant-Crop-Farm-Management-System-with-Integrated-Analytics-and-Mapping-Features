<?php
// Sample PHP code for database connection

// Fetch Category Names for the Vendor
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
  <title>Product Category Distribution Chart</title>
  <!-- Include Chart.js library -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
  <!-- Container for Product Category Distribution Chart -->
  <div class="container mt-4">
    <hr>
    <h2 class="text-center mb-4">Product Category Distribution Chart</h2>
    <div class="row">
      <div class="col-12">
        <canvas id="categoryPieChart"></canvas>
      </div>
    </div>
  </div>

  <!-- JavaScript Code for Product Category Distribution Chart -->
  <script>
    // JavaScript for Pie Chart Rendering
    const ctxCategory = document.getElementById('categoryPieChart').getContext('2d');
    const categoryData = <?= json_encode(array_values($categoryData)) ?>;
    const categoryLabels = <?= json_encode(array_keys($categoryData)) ?>;
    const categoryColors = [
      'rgba(255, 99, 132, 0.7)', // Red
      'rgba(54, 162, 235, 0.7)', // Blue
      'rgba(255, 206, 86, 0.7)', // Yellow
      'rgba(75, 192, 192, 0.7)', // Green
      'rgba(153, 102, 255, 0.7)', // Purple
      'rgba(255, 159, 64, 0.7)', // Orange
      // Add more colors as needed
    ];

    const categoryPieChart = new Chart(ctxCategory, {
      type: 'pie',
      data: {
        labels: categoryLabels,
        datasets: [{
          label: 'Product Categories',
          data: categoryData,
          backgroundColor: categoryColors,
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false
      }
    });
  </script>
</body>
</html>
