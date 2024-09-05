<?php
require_once('./../../config.php');

// Get the parameters from the request
$latitude = isset($_GET['latitude']) ? (float)$_GET['latitude'] : null;
$longitude = isset($_GET['longitude']) ? (float)$_GET['longitude'] : null;
$cropName = isset($_GET['crop']) ? $_GET['crop'] : null;
$radius = 100; // Define the radius in kilometers

// Function to calculate the distance between two points using the Haversine formula
function haversine($lat1, $lon1, $lat2, $lon2) {
    $earth_radius = 6371; // Earth radius in kilometers

    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);

    $a = sin($dLat / 2) * sin($dLat / 2) +
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
         sin($dLon / 2) * sin($dLon / 2);

    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    return $earth_radius * $c;
}

// Query to fetch crops with the specified name that have a PlannedPlantingDate but no DatePlanted
$query = "
    SELECT Id, Name, PlannedPlantingDate, Latitude, Longitude
    FROM crop
    WHERE Name = '$cropName'
    AND is_deleted = 0
    AND hide = 0
    AND DatePlanted IS NULL
";

$result = $conn->query($query);

// Initialize an array to hold the neighboring crops
$neighboringCrops = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Calculate the distance to each crop
        $distance = haversine($latitude, $longitude, $row['Latitude'], $row['Longitude']);
        if ($distance <= $radius) {
            $neighboringCrops[] = $row;
        }
    }
}

// Prepare the response
$response = [
    'success' => true,
    'neighboringCrops' => $neighboringCrops
];

// Return the data as JSON
echo json_encode($response);

?>
