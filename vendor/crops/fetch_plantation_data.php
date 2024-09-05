<?php
require_once('./../../config.php');

// Query to fetch crop plantation data
$query = "
    SELECT Name, Latitude, Longitude, SizeOfPlantation
    FROM crop
    WHERE PlannedPlantingDate IS NOT NULL
    AND is_deleted = 0
    AND hide = 0
";

// Execute the query
$result = $conn->query($query);

// Initialize an array to hold the crop data
$crops = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $crops[] = $row;
    }
}

// Return the data as JSON
echo json_encode($crops);

?>
