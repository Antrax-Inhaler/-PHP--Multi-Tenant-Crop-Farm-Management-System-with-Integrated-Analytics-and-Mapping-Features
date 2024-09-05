<?php

$crop_id = $_GET['id'] ?? null;

if (!$crop_id) {
    die("Crop ID is required.");
}
$cropId = $crop_id;
$cropQuery = "SELECT * FROM crop WHERE Id = $cropId";
$cropResult = $conn->query($cropQuery);
$crop = $cropResult->fetch_assoc();

// Fetch pest and disease data
$pestDiseaseQuery = "SELECT * FROM croppestdisease WHERE CropID = $cropId";
$pestDiseaseResult = $conn->query($pestDiseaseQuery);
$pestDiseases = $pestDiseaseResult->fetch_all(MYSQLI_ASSOC);

// Fetch activity data
$activityQuery = "SELECT * FROM crop_activity WHERE crop_id = $cropId";
$activityResult = $conn->query($activityQuery);
$activities = $activityResult->fetch_all(MYSQLI_ASSOC);

// Fetch farm data
$farmQuery = "SELECT * FROM farm WHERE Id = " . $crop['FarmId'];
$farmResult = $conn->query($farmQuery);
$farm = $farmResult->fetch_assoc();

// Fetch harvest data
$harvestQuery = "SELECT * FROM harvest WHERE CropId = $cropId";
$harvestResult = $conn->query($harvestQuery);
$harvests = $harvestResult->fetch_all(MYSQLI_ASSOC);

// Fetch vendor data
$vendorQuery = "SELECT * FROM vendor_list WHERE Id = " . $crop['VendorId'];
$vendorResult = $conn->query($vendorQuery);
$vendor = $vendorResult->fetch_assoc();


// Close connection
?>
<style>
    .unique-container {
        max-width: 900px;
        margin: auto;
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    .unique-header {
        text-align: center;
        padding-bottom: 20px;
    }
    .unique-header h1 {
        color: #333;
    }
    .unique-section {
        margin-bottom: 30px;
    }
    .unique-section-title {
        background: #f8f8f8;
        padding: 10px;
        border-radius: 8px;
        color: #333;
    }
    .unique-info-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }
    .unique-info-table th, .unique-info-table td {
        padding: 10px;
        border: 1px solid #ddd;
    }
    .unique-info-table th {
        background: #f1f1f1;
    }
    .unique-info-table td {
        background: #fafafa;
    }
    .unique-status {
        padding: 5px 10px;
        border-radius: 5px;
        color: #fff;
    }
    .unique-status.Alive {
        background: #4caf50;
    }
    .unique-status.Diseased {
        background: #f44336;
    }
    .unique-status.EndOfLifespan {
        background: #ff9800;
    }
    .unique-status.Unproductive {
        background: #9e9e9e;
    }
    .unique-chart-container {
        position: relative;
        height: 300px;
    }
    .unique-image-container img {
        width: 100%;
        max-height: 200px;
        object-fit: cover;
        border-radius: 8px;
        margin-top: 10px;
    }
    .unique-images {
        display: flex;
        justify-content: space-between;
    }
    .unique-images img {
        max-width: 30%;
    }
    .unique-scrollable {
        width: 100%;
        overflow: scroll;
        overflow-y: auto;
    }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.css">

<body>
<div class="unique-container">
    <div class="unique-header">
        <h5>Crop Information</h5>
        <p>As of <?= date('Y-m-d') ?></p>
    </div>
    <div class="unique-section">
        <div class="unique-section-title">Crop Owner Information</div>
        <table class="unique-info-table">
            <tr>
                <th>Owner</th>
                <td><?= $vendor['shop_owner'] ?></td>
            </tr>
            <tr>
                <th>Contact</th>
                <td><?= $vendor['contact'] ?></td>
            </tr>
        </table>
    </div>
    <div class="unique-section">
        <div class="unique-section-title">Crop Details</div>
        <table class="unique-info-table">
            <tr>
                <th>Name</th>
                <td><?= $crop['Name'] ?></td>
            </tr>
            <tr>
                <th>Type</th>
                <td><?= $crop['Type'] ?></td>
            </tr>
            <tr>
                <th>Planned Planting Date</th>
                <td><?= $crop['PlannedPlantingDate'] ?></td>
            </tr>
            <tr>
                <th>Date Planted</th>
                <td><?= $crop['DatePlanted'] ?></td>
            </tr>
            <tr>
                <th>Size of Plantation</th>
                <td><?= $crop['SizeOfPlantation'] ?> hectares</td>
            </tr>
            <tr>
                <th>Description</th>
                <td><?= $crop['Description'] ?></td>
            </tr>
            <tr>
                <th>Status</th>
                <td><span class="unique-status <?= $crop['Status'] ?>"><?= $crop['Status'] ?></span></td>
            </tr>
        </table>
    </div>
    <div class="unique-section">
        <div class="unique-section-title">Farm Details</div>
        <table class="unique-info-table">
            <tr>
                <th>Farm Name</th>
                <td><?= $farm['Name'] ?></td>
            </tr>
            <tr>
                <th>Location</th>
                <td><?= $farm['Latitude'] ?>, <?= $farm['Longitude'] ?></td>
            </tr>
            <tr>
                <th>Size</th>
                <td><?= $farm['Size'] ?> hectares</td>
            </tr>
            <tr>
                <th>Description</th>
                <td><?= $farm['Description'] ?></td>
            </tr>
        </table>
    </div>
    <div class="unique-section">
        <div class="unique-section-title">Crop Activities</div>
        <table class="unique-info-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Activity Type</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($activities as $activity): ?>
                    <tr>
                        <td><?= $activity['activity_date'] ?></td>
                        <td><?= $activity['activity_type'] ?></td>
                        <td><?= $activity['description'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="unique-section">
        <div class="unique-section-title">Harvest Information</div>
        <div class="unique-chart-container">
            <canvas id="unique-harvestChart"></canvas>
        </div>
        <table class="unique-info-table">
            <thead>
                <tr>
                    <th>Harvested Date</th>
                    <th>Amount of Harvest</th>
                    <th>Paid</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($harvests as $harvest): ?>
                    <tr>
                        <td><?= $harvest['HarvestedDate'] ?></td>
                        <td><?= $harvest['AmountOfHarvest'] ?> kg</td>
                        <td><?= $harvest['Paid'] ? 'Yes' : 'No' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="unique-row">
            <div class="unique-chart-container">
                <canvas id="unique-activityChart"></canvas>
            </div>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-timeline"></script>

<script>
    // Harvest Data
    const harvestData = <?php echo json_encode($harvests); ?>;
    const harvestLabels = harvestData.map(harvest => harvest.HarvestedDate);
    const harvestAmounts = harvestData.map(harvest => harvest.AmountOfHarvest);

    const harvestChart = new Chart(document.getElementById('unique-harvestChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: harvestLabels,
            datasets: [{
                label: 'Amount of Harvest (kg)',
                data: harvestAmounts,
                backgroundColor: 'rgba(75, 192, 192, 0.5)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4, // For a more curved line
                pointStyle: 'circle',
                pointRadius: 5,
                pointHoverRadius: 7,
                shadowOffsetX: 3,
                shadowOffsetY: 3,
                shadowBlur: 10,
                shadowColor: 'rgba(0, 0, 0, 0.5)'
            }]
        },
        options: {
            scales: {
                x: { 
                    title: { display: true, text: 'Date' },
                    grid: {
                        display: false
                    }
                },
                y: { 
                    title: { display: true, text: 'Amount of Harvest (kg)' },
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                datalabels: {
                    color: '#000',
                    font: {
                        weight: 'bold'
                    },
                    formatter: (value, context) => `${value} kg`
                }
            }
        },
        plugins: [ChartDataLabels]
    });

    // Activity Data
    const activityData = <?php echo json_encode($activities); ?>;
    const activityEvents = activityData.map(activity => {
        return {
            label: activity.activity_type,
            data: [{
                time: new Date(activity.activity_date).getTime(),
                description: activity.description
            }]
        };
    });

    const activityChart = new Chart(document.getElementById('unique-activityChart').getContext('2d'), {
        type: 'timeline',
        data: {
            datasets: activityEvents
        },
        options: {
            scales: {
                x: { type: 'time', title: { display: true, text: 'Date' } }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `${context.dataset.label}: ${context.raw.description}`;
                        }
                    }
                }
            }
        }
    });
</script>
