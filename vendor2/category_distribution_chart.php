<?php
// Sample PHP code for database connection and fetching data
// Assuming $conn is already established

$categoryData = array();
$categoryLabels = array(); // Labels for the chart
$categoryCounts = array(); // Counts for the chart

// Fetch category data and initialize counts
$categoryQuery = $conn->query("
  SELECT name
  FROM category_list
  WHERE vendor_id = '{$_settings->userdata('id')}' AND delete_flag = 0
");
while ($row = $categoryQuery->fetch_assoc()) {
  $categoryData[$row['name']] = 0; // Initialize product count for each category
  $categoryLabels[] = $row['name']; // Collect labels for the chart
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

// Prepare data for the chart
foreach ($categoryData as $category => $count) {
  $categoryCounts[] = $count; // Collect counts for the chart
}

// Convert data to JSON for JavaScript use
$categoryLabelsJSON = json_encode($categoryLabels);
$categoryCountsJSON = json_encode($categoryCounts);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Category Distribution Chart</title>
<!-- Include Chart.js from CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
</head>
<body>
<div style="width: 80%; margin: 20px auto;">
    <canvas id="categoryChart"></canvas>
</div>

<script>
// Data for the chart from PHP
var categoryLabels = <?php echo $categoryLabelsJSON; ?>;
var categoryCounts = <?php echo $categoryCountsJSON; ?>;

// Configuration options
var options = {
    indexAxis: 'y',
    scales: {
        x: {
            beginAtZero: true
        }
    }
};

// Get the context of the canvas element we want to select
var ctx = document.getElementById('categoryChart').getContext('2d');

// Create the chart
var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: categoryLabels,
        datasets: [{
            label: 'Product Count',
            data: categoryCounts,
            backgroundColor: 'rgba(54, 162, 235, 0.6)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: options
});
</script>
</body>
</html>
