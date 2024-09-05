<?php require_once('inc/farmTopBar.php') ?>
<h1 class="welcome">Welcome to <?php echo $_settings->info('name') ?> - NAFA Side</h1>
<style>
  #cover-image {
    width: calc(100%);
    height: 50vh;
    object-fit: cover;
    object-position: center center;
  }
  @media (max-width: 1000px) {
    .welcome {
      font-size: 10px;
    }
    .info-box-text {
      font-size: 15px;
    }
  }
</style>
<hr>

<div class="row">
  <div class="col-6 col-sm-4 col-md-4">
    <div class="info-box">
      <span class="info-box-icon bg-gradient-info elevation-1"><i class="fas fa-tractor"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Total Farms</span>
        <span class="info-box-number text-right h4">
          <?php 
            $total_farms_query = $conn->query("SELECT COUNT(Id) as total FROM farm WHERE VendorListId IN (SELECT id FROM vendor_list WHERE delete_flag = 0)")->fetch_assoc();
            $total_farms = $total_farms_query['total'];
            echo format_num($total_farms);
          ?>
        </span>
      </div>
    </div>
  </div>
  <div class="col-6 col-sm-4 col-md-4">
    <div class="info-box">
      <span class="info-box-icon bg-gradient-success elevation-1"><i class="fas fa-seedling"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Total Crops</span>
        <span class="info-box-number text-right h4">
          <?php 
            $total_crops_query = $conn->query("SELECT COUNT(Id) as total FROM crop WHERE VendorId IN (SELECT id FROM vendor_list WHERE delete_flag = 0)")->fetch_assoc();
            $total_crops = $total_crops_query['total'];
            echo format_num($total_crops);
          ?>
        </span>
      </div>
    </div>
  </div>
  <div class="col-6 col-sm-4 col-md-4">
    <div class="info-box">
      <span class="info-box-icon bg-gradient-warning elevation-1"><i class="fas fa-tasks"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Total Crop Activities</span>
        <span class="info-box-number text-right h4">
          <?php 
            $total_activities_query = $conn->query("SELECT COUNT(id) as total FROM crop_activity")->fetch_assoc();
            $total_activities = $total_activities_query['total'];
            echo format_num($total_activities);
          ?>
        </span>
      </div>
    </div>
  </div>
  <div class="col-6 col-sm-4 col-md-4">
    <div class="info-box">
      <span class="info-box-icon bg-gradient-danger elevation-1"><i class="fas fa-bug"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Total Crop Pests/Diseases Reported</span>
        <span class="info-box-number text-right h4">
          <?php 
            $total_pests_query = $conn->query("SELECT COUNT(Id) as total FROM croppestdisease")->fetch_assoc();
            $total_pests = $total_pests_query['total'];
            echo format_num($total_pests);
          ?>
        </span>
      </div>
    </div>
  </div>
  <div class="col-6 col-sm-4 col-md-4">
    <div class="info-box">
      <span class="info-box-icon bg-gradient-info elevation-1"><i class="fas fa-harvest"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Total Harvest Records</span>
        <span class="info-box-number text-right h4">
          <?php 
            $total_harvests_query = $conn->query("SELECT COUNT(Id) as total FROM harvest")->fetch_assoc();
            $total_harvests = $total_harvests_query['total'];
            echo format_num($total_harvests);
          ?>
        </span>
      </div>
    </div>
  </div>
  <div class="col-6 col-sm-4 col-md-4">
    <div class="info-box">
      <span class="info-box-icon bg-gradient-primary elevation-1"><i class="fas fa-splotch"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Most Common Pests/Diseases</span>
        <span class="info-box-number text-right h4">
          <?php 
            $common_pests_query = $conn->query("SELECT Name, COUNT(*) as occurrence FROM croppestdisease GROUP BY Name ORDER BY occurrence DESC LIMIT 1")->fetch_assoc();
            echo $common_pests_query['Name'] . ' (' . $common_pests_query['occurrence'] . ')';
          ?>
        </span>
      </div>
    </div>
  </div>
  <div class="col-6 col-sm-4 col-md-4">
    <div class="info-box">
      <span class="info-box-icon bg-gradient-secondary elevation-1"><i class="fas fa-ruler"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Average Farm Size</span>
        <span class="info-box-number text-right h4">
          <?php 
            $average_farm_size_query = $conn->query("SELECT AVG(Size) as average_size FROM farm")->fetch_assoc();
            echo format_num($average_farm_size_query['average_size']) . ' hectares';
          ?>
        </span>
      </div>
    </div>
  </div>
  <div class="col-6 col-sm-4 col-md-4">
    <div class="info-box">
      <span class="info-box-icon bg-gradient-warning elevation-1"><i class="fas fa-calendar"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Recently Planted Crops</span>
        <span class="info-box-number text-right h4">
          <?php 
            $recent_crops_query = $conn->query("SELECT Name, PlannedPlantingDate FROM crop ORDER BY PlannedPlantingDate DESC LIMIT 1")->fetch_assoc();
            echo $recent_crops_query['Name'] . ' (' . $recent_crops_query['PlannedPlantingDate'] . ')';
          ?>
        </span>
      </div>
    </div>
  </div>
  <div class="col-6 col-sm-4 col-md-4">
    <div class="info-box">
      <span class="info-box-icon bg-gradient-success elevation-1"><i class="fas fa-seedling"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Total Harvested Amount</span>
        <span class="info-box-number text-right h4">
          <?php 
            $total_harvest_amount_query = $conn->query("SELECT SUM(AmountOfHarvest) as total_amount FROM harvest")->fetch_assoc();
            echo format_num($total_harvest_amount_query['total_amount']) . ' kg';
          ?>
        </span>
      </div>
    </div>
  </div>
  <div class="col-6 col-sm-4 col-md-4">
    <div class="info-box">
      <span class="info-box-icon bg-gradient-danger elevation-1"><i class="fas fa-bug"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Total Pests/Diseases Reports</span>
        <span class="info-box-number text-right h4">
          <?php 
            $total_reports_query = $conn->query("SELECT COUNT(id) as total FROM pestanddiseasereport")->fetch_assoc();
            $total_reports = $total_reports_query['total'];
            echo format_num($total_reports);
          ?>
        </span>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Total Harvested Amount Chart -->
<div class="col-md-6">
  <canvas id="harvestAmountChart" style="min-height: 450px; max-height: 450px; width: 100%;"></canvas>
</div>

<script>
  // Total Harvested Amount Chart
  var harvestAmountCtx = document.getElementById('harvestAmountChart').getContext('2d');
  var harvestAmountChart = new Chart(harvestAmountCtx, {
    type: 'bar',
    data: {
      labels: ['Total Harvested Amount'],
      datasets: [{
        label: 'Harvested Amount (tons)',
        backgroundColor: 'rgba(75, 192, 192, 0.2)',
        borderColor: 'rgba(75, 192, 192, 1)',
        borderWidth: 1,
        data: [
          <?php 
            $total_harvest_amount_query = $conn->query("SELECT SUM(AmountOfHarvest) as total_amount FROM harvest")->fetch_assoc();
            echo $total_harvest_amount_query['total_amount'] ?? 0;
          ?>
        ],
      }]
    },
    options: {
      scales: {
        yAxes: [{
          ticks: {
            beginAtZero: true,
            callback: function(value) { if (Number.isInteger(value)) { return value; } }
          }
        }]
      }
    }
  });
</script>
<?php
// Fetch total counts for each status category
$total_pending_query = $conn->query("SELECT COUNT(id) as total FROM pestanddiseasereport WHERE status = 0")->fetch_assoc();
$total_processing_query = $conn->query("SELECT COUNT(id) as total FROM pestanddiseasereport WHERE status = 1")->fetch_assoc();
$total_visited_query = $conn->query("SELECT COUNT(id) as total FROM pestanddiseasereport WHERE status = 2")->fetch_assoc();
$total_resolved_query = $conn->query("SELECT COUNT(id) as total FROM pestanddiseasereport WHERE status = 3")->fetch_assoc();

// Assign counts to variables
$total_pending = $total_pending_query['total'] ?? 0;
$total_processing = $total_processing_query['total'] ?? 0;
$total_visited = $total_visited_query['total'] ?? 0;
$total_resolved = $total_resolved_query['total'] ?? 0;
?>
<!-- Pests/Diseases Reports Chart -->
<div class="col-md-6">
  <canvas id="pestsReportsChart" style="min-height: 450px; max-height: 450px; width: 100%;"></canvas>
</div>

<script>
  // Pests/Diseases Reports Chart
  var pestsReportsCtx = document.getElementById('pestsReportsChart').getContext('2d');
  var pestsReportsChart = new Chart(pestsReportsCtx, {
    type: 'pie',
    data: {
      labels: ['Pending', 'Processing', 'Visited', 'Resolved'],
      datasets: [{
        label: 'Total Reports',
        backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545'],
        data: [
          <?php echo "$total_pending, $total_processing, $total_visited, $total_resolved"; ?>
        ],
      }]
    }
  });
</script>
<?php
// Fetch data for Distribution of Crop Types
$crop_type_data = [];
$crop_type_query = $conn->query("SELECT type, COUNT(id) as total FROM crop GROUP BY type");
while ($row = $crop_type_query->fetch_assoc()) {
    $crop_type_data[$row['type']] = $row['total'];
}

$crop_type_labels = array_keys($crop_type_data);
$crop_type_totals = array_values($crop_type_data);

// Fetch data for Area Chart - Pest and Disease Incidences (assuming monthly data)
$pest_disease_data = [];
$pest_disease_query = $conn->query("SELECT MONTH(created_at) as month, COUNT(id) as total FROM pestanddiseasereport GROUP BY MONTH(created_at)");
while ($row = $pest_disease_query->fetch_assoc()) {
    $pest_disease_data[$row['month']] = $row['total'];
}

$pest_disease_labels = [];
$pest_disease_totals = [];
for ($i = 1; $i <= 12; $i++) {
    $pest_disease_labels[] = date('F', mktime(0, 0, 0, $i, 1));
    $pest_disease_totals[] = $pest_disease_data[$i] ?? 0;
}

// Fetch data for Crop Growth Trends Line Chart (assuming monthly data)
$crop_growth_data = [];
$crop_growth_query = $conn->query("SELECT MONTH(DatePlanted) as month, COUNT(id) as total FROM crop GROUP BY MONTH(DatePlanted)");
while ($row = $crop_growth_query->fetch_assoc()) {
    $crop_growth_data[$row['month']] = $row['total'];
}

$crop_growth_labels = [];
$crop_growth_totals = [];
for ($i = 1; $i <= 12; $i++) {
    $crop_growth_labels[] = date('F', mktime(0, 0, 0, $i, 1));
    $crop_growth_totals[] = $crop_growth_data[$i] ?? 0;
}

// Fetch data for Bar Chart - Farm Sizes Distribution
$farm_size_data = [];
$farm_size_query = $conn->query("SELECT Size, COUNT(id) as total FROM farm GROUP BY Size");
while ($row = $farm_size_query->fetch_assoc()) {
    $farm_size_data[$row['Size']] = $row['total'];
}

$farm_size_labels = array_keys($farm_size_data);
$farm_size_totals = array_values($farm_size_data);
?>
<!-- Additional Charts -->
<div class="row">
  <!-- Distribution of Crop Types Pie Chart -->
  <div class="col-md-6">
    <canvas id="cropTypesChart" style="min-height: 450px; max-height: 450px; width: 100%;"></canvas>
  </div>

  <!-- Area Chart - Pest and Disease Incidences -->
  <div class="col-md-6">
    <canvas id="pestDiseaseChart" style="min-height: 450px; max-height: 450px; width: 100%;"></canvas>
  </div>
</div>

<div class="row">
  <!-- Crop Growth Trends Line Chart -->
  <div class="col-md-6">
    <canvas id="cropGrowthChart" style="min-height: 450px; max-height: 450px; width: 100%;"></canvas>
  </div>

  <!-- Farm Sizes Distribution Bar Chart -->
  <div class="col-md-6">
    <canvas id="farmSizeDistributionChart" style="min-height: 450px; max-height: 450px; width: 100%;"></canvas>
  </div>
</div>

<script>
  // Distribution of Crop Types Pie Chart
  var cropTypesCtx = document.getElementById('cropTypesChart').getContext('2d');
  var cropTypesChart = new Chart(cropTypesCtx, {
    type: 'pie',
    data: {
      labels: <?php echo json_encode($crop_type_labels); ?>,
      datasets: [{
        label: 'Crop Types',
        data: <?php echo json_encode($crop_type_totals); ?>,
        backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545', '#6c757d'],
      }]
    }
  });

  // Area Chart - Pest and Disease Incidences
  var pestDiseaseCtx = document.getElementById('pestDiseaseChart').getContext('2d');
  var pestDiseaseChart = new Chart(pestDiseaseCtx, {
    type: 'line',
    data: {
      labels: <?php echo json_encode($pest_disease_labels); ?>,
      datasets: [{
        label: 'Pest and Disease Incidences',
        data: <?php echo json_encode($pest_disease_totals); ?>,
        borderColor: 'rgba(255, 99, 132, 1)',
        backgroundColor: 'rgba(255, 99, 132, 0.2)',
        fill: true
      }]
    }
  });

  // Crop Growth Trends Line Chart
  var cropGrowthCtx = document.getElementById('cropGrowthChart').getContext('2d');
  var cropGrowthChart = new Chart(cropGrowthCtx, {
    type: 'line',
    data: {
      labels: <?php echo json_encode($crop_growth_labels); ?>,
      datasets: [{
        label: 'Crop Growth Trends',
        data: <?php echo json_encode($crop_growth_totals); ?>,
        borderColor: 'rgba(54, 162, 235, 1)',
        backgroundColor: 'rgba(54, 162, 235, 0.2)',
        fill: true
      }]
    }
  });

  // Farm Sizes Distribution Bar Chart
  var farmSizeDistributionCtx = document.getElementById('farmSizeDistributionChart').getContext('2d');
  var farmSizeDistributionChart = new Chart(farmSizeDistributionCtx, {
    type: 'bar',
    data: {
      labels: <?php echo json_encode($farm_size_labels); ?>,
      datasets: [{
        label: 'Farm Sizes Distribution',
        data: <?php echo json_encode($farm_size_totals); ?>,
        backgroundColor: 'rgba(75, 192, 192, 0.5)',
        borderColor: 'rgba(75, 192, 192, 1)',
        borderWidth: 1
      }]
    }
  });
</script>
