<?php

$crop_id = $_GET['id'] ?? null;

if (!$crop_id) {
    die("Crop ID is required.");
}
$cropId = $crop_id;
$cropQuery = "SELECT * FROM crop WHERE Id = $cropId";
$cropResult = $conn->query($cropQuery);
$crop = $cropResult->fetch_assoc();

// Fetch vendor data
$vendorQuery = "SELECT * FROM vendor_list WHERE Id = " . $crop['VendorId'];
$vendorResult = $conn->query($vendorQuery);
$vendor = $vendorResult->fetch_assoc();

$isPlanted = !empty($crop['DatePlanted']) && strtotime($crop['DatePlanted']) <= time();
$stageLabel = $isPlanted ? "Already Planted" : "Planned to be Planted";
$stageClass = $isPlanted ? "planted" : "planned";

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
        #map {
            height: 300px;
            width: 100%;
            margin-top: 20px;
        }
        /* New styles for crop stage differentiation */
        .stage-label {
            font-size: 18px;
            font-weight: bold;
            margin-top: 10px;
            padding: 10px;
            text-align: center;
            border-radius: 8px;
        }
        .stage-label.planted {
            background-color: #4caf50;
            color: white;
        }
        .stage-label.planned {
            background-color: #ff9800;
            color: white;
        }
        .unique-chart-container {
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="unique-container">
    <div class="unique-header">
        <h5>Crop Information</h5>
        <p>As of <?= date('Y-m-d') ?></p>
    </div>
    <div class="stage-label <?= $stageClass ?>"><?= $stageLabel ?></div> <!-- Display crop stage -->
    
    <!-- Crop Owner Information -->
    <div class="unique-section">
        <div class="unique-section-title">Crop Owner Information</div>
        <table class="unique-info-table">
            <tr>
                <th>Owner</th>
                <td><?= htmlspecialchars($vendor['shop_owner']) ?></td>
            </tr>
            <tr>
                <th>Contact</th>
                <td>
                    <a href="tel:<?= htmlspecialchars($vendor['contact']) ?>" style="color: blue; text-decoration: underline;">
                        <i class="fa fa-phone"></i> <?= htmlspecialchars($vendor['contact']) ?>
                    </a>
                </td>
            </tr>
            <tr>
                <th>Facebook</th>
                <td>
                    <a href="https://www.facebook.com/<?= htmlspecialchars($vendor['facebook']) ?>" class="btn btn-sm btn-info">
                        <i class="fab fa-facebook-f"></i> <?= htmlspecialchars($vendor['facebook']) ?>
                    </a>
                </td>
            </tr>
        </table>
    </div>
    
    <!-- Crop Details -->
    <div class="unique-section">
        <div class="unique-section-title">Crop Details</div>
        <div id="map"></div>
        <div id="directionsPanel"></div>
        <table class="unique-info-table">
            <tr>
                <th>Name</th>
                <td><?= htmlspecialchars($crop['Name']) ?></td>
            </tr>
            <tr>
                <th>Type</th>
                <td><?= htmlspecialchars($crop['Type']) ?></td>
            </tr>
            <tr>
                <th>Planned Planting Date</th>
                <td><?= htmlspecialchars($crop['PlannedPlantingDate']) ?></td>
            </tr>
            <tr>
                <th>Date Planted</th>
                <td><?= htmlspecialchars($crop['DatePlanted']) ?></td>
            </tr>
            <tr>
                <th>Size of Plantation</th>
                <td><?= htmlspecialchars($crop['SizeOfPlantation']) ?> hectares</td>
            </tr>
            <tr>
                <th>Description</th>
                <td><?= htmlspecialchars($crop['Description']) ?></td>
            </tr>
            <tr>
                <th>Status</th>
                <td><span class="unique-status <?= htmlspecialchars($crop['Status']) ?>"><?= htmlspecialchars($crop['Status']) ?></span></td>
            </tr>
        </table>
    </div>
    
    <!-- Farm Details -->
    <div class="unique-section">
        <div class="unique-section-title">Farm Details</div>
        <table class="unique-info-table">
            <tr>
                <th>Farm Name</th>
                <td><?= htmlspecialchars($farm['Name']) ?></td>
            </tr>
            <tr>
                <th>Location</th>
                <td><?= htmlspecialchars($farm['Latitude']) ?>, <?= htmlspecialchars($farm['Longitude']) ?></td>
            </tr>
            <tr>
                <th>Size</th>
                <td><?= htmlspecialchars($farm['Size']) ?> hectares</td>
            </tr>
            <tr>
                <th>Description</th>
                <td><?= htmlspecialchars($farm['Description']) ?></td>
            </tr>
        </table>
    </div>
    
    <!-- Crop Activities -->
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
                        <td><?= htmlspecialchars($activity['activity_date']) ?></td>
                        <td><?= htmlspecialchars($activity['activity_type']) ?></td>
                        <td><?= htmlspecialchars($activity['description']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Harvest Information -->
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
                </tr>
            </thead>
            <tbody>
                <?php foreach ($harvests as $harvest): ?>
                    <tr>
                        <td><?= htmlspecialchars($harvest['HarvestedDate']) ?></td>
                        <td><?= htmlspecialchars($harvest['AmountOfHarvest']) ?> kg</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div style="text-align: center; margin-top: 20px;">
        <form id="interested_client_form">
        
        <input type="hidden" name="crop_id" id="crop_id" value="<?=$crop_id ?>">
 <div class="form-group">
            <label for="interest_message">Leave a message for the vendor:</label>
            <textarea name="message" id="interest_message" class="form-control" rows="3" placeholder="Enter your message here..."></textarea>
        </div>
    <!-- Hidden input for client_id -->
    <input type="hidden" name="client_id" id="client_id" value="<?= $_settings->userdata('id') ?>">
    <button id="interestedButton" class="btn btn-primary">I'm Interested</button>
</form>    </div>
    </div>
</div>

<!-- Chart.js and plugin scripts -->
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
<script>
document.getElementById('interestedButton').addEventListener('click', function(e) {
    e.preventDefault(); // Prevent the default button behavior

    <?php if ($_settings->userdata('id') > 0 && $_settings->userdata('login_type') == 3) : ?>
        // If the client is logged in, submit the form via AJAX
        var formData = $('#interested_client_form').serialize(); // Serialize form data
        start_loader();
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=save_interested_client",
            method: "POST",
            data: formData,
            dataType: "json",
            error: function(err) {
                console.log(err);
                alert_toast("An error occurred.", 'error');
                end_loader();
            },
            success: function(resp) {
                if (typeof resp === 'object' && resp.status === 'success') {
                    alert_toast(resp.msg, 'success');
                    location.reload(); // Reload the page on success
                } else {
                    alert_toast("An error occurred.", 'error');
                    end_loader();
                }
            }
        });
    <?php else: ?>
        // Redirect to login page
        window.location.href = _base_url_ + '/login.php';
    <?php endif; ?>
});
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-timeline"></script>
<script>
    let map, directionsService, directionsRenderer;

    function initMap() {
        const cropLocation = { lat: <?= htmlspecialchars($farm['Latitude']) ?>, lng: <?= htmlspecialchars($farm['Longitude']) ?> };
        
        // Initialize map
        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 10,
            center: cropLocation
        });

        // Add a marker for the crop location
        new google.maps.Marker({
            position: cropLocation,
            map: map,
            title: 'Crop Location'
        });

        // Initialize Directions services
        directionsService = new google.maps.DirectionsService();
        directionsRenderer = new google.maps.DirectionsRenderer({
            map: map,
            panel: document.getElementById('directionsPanel')
        });

        // Get user's current location and display directions
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showDirections, showError);
        } else {
            alert('Geolocation is not supported by this browser.');
        }
    }

    function showDirections(position) {
        const userLocation = {
            lat: position.coords.latitude,
            lng: position.coords.longitude
        };

        const request = {
            origin: userLocation,
            destination: { lat: <?= htmlspecialchars($farm['Latitude']) ?>, lng: <?= htmlspecialchars($farm['Longitude']) ?> },
            travelMode: 'DRIVING'
        };

        directionsService.route(request, (result, status) => {
            if (status === 'OK') {
                directionsRenderer.setDirections(result);
            } else {
                alert('Could not display directions due to: ' + status);
            }
        });
    }

    function showError(error) {
        switch (error.code) {
            case error.PERMISSION_DENIED:
                alert("User denied the request for Geolocation.");
                break;
            case error.POSITION_UNAVAILABLE:
                alert("Location information is unavailable.");
                break;
            case error.TIMEOUT:
                alert("The request to get user location timed out.");
                break;
            case error.UNKNOWN_ERROR:
                alert("An unknown error occurred.");
                break;
        }
    }
</script>
<script async
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBaEr_ZLsaoWcbipd--a1S5EPQe2RaEfio&libraries=places&callback=initMap">
</script>
