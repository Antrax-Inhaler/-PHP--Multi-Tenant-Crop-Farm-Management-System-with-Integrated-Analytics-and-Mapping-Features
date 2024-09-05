<?php
require_once('./../../config.php');

$farmId = isset($_GET['farmId']) ? intval($_GET['farmId']) : 0;

if ($farmId > 0) {
    // Fetch latitude and longitude of the selected farm
    $sqlFarmLocation = "SELECT Latitude, Longitude FROM farm WHERE Id = $farmId";
    $resultFarmLocation = $conn->query($sqlFarmLocation);

    if ($resultFarmLocation->num_rows > 0) {
        $farmLocation = $resultFarmLocation->fetch_assoc();
        echo json_encode([
            'latitude' => $farmLocation['Latitude'],
            'longitude' => $farmLocation['Longitude']
        ]);
    } else {
        echo json_encode(['error' => 'No farm location found']);
    }
} else {
    echo json_encode(['error' => 'Invalid farm ID']);
}
?>
