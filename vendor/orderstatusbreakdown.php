<?php

// Define status names
$statusNames = [
  0 => 'Pending',
  1 => 'Confirmed',
  2 => 'Packed',
  3 => 'Out for Delivery',
  4 => 'Delivered',
  5 => 'Cancelled'
];

// Fetch Order Status Data
$orderStatusData = array();
$statusQuery = $conn->query("
  SELECT `status`, COUNT(id) AS order_count
  FROM order_list
  WHERE vendor_id = '{$_settings->userdata('id')}'
  GROUP BY `status`
");
while ($row = $statusQuery->fetch_assoc()) {
  $orderStatusData[$statusNames[$row['status']]] = $row['order_count'];
}
?>

  <!-- Include Chart.js library -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <div class="container mt-4">
    <hr>
    <h2 class="text-center mb-4">Order Status Breakdown Chart</h2>
    <div class="row">
      <div class="col-12">
        <canvas id="orderStatusChart"></canvas>
      </div>
    </div>
  </div>

  <!-- JavaScript Code for Order Status Breakdown Chart -->
  <script>
    // JavaScript for Pie Chart Rendering
    const ctxOrderStatus = document.getElementById('orderStatusChart').getContext('2d');
    
    // Prepare data for chart
    const orderStatusData = <?= json_encode($orderStatusData) ?>;
    const labels = Object.keys(orderStatusData);
    const data = Object.values(orderStatusData);
    const backgroundColors = [
      'rgba(255, 99, 132, 0.7)', // Red
      'rgba(54, 162, 235, 0.7)', // Blue
      'rgba(255, 206, 86, 0.7)', // Yellow
      'rgba(75, 192, 192, 0.7)', // Green
      'rgba(153, 102, 255, 0.7)', // Purple
      'rgba(255, 159, 64, 0.7)', // Orange
    ];

    // Customize size based on order count
    const sizes = data.map(val => val * 145); // Increase the multiplier for larger sizes

    const orderStatusChart = new Chart(ctxOrderStatus, {
      type: 'pie', // You can also use 'bar' for a stacked bar graph
      data: {
        labels: labels,
        datasets: [{
          label: 'Order Status',
          data: data,
          backgroundColor: backgroundColors,
          borderWidth: 1,
          // Dynamically set radius based on sizes array
          radius: sizes
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false
      }
      
    });
  </script>
