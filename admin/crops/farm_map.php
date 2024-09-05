<?php
// Assuming you have established the database connection $conn
$user_id = $_settings->userdata('id');

$sql = "SELECT c.Name as CropName, c.Latitude as CropLat, c.Longitude as CropLng, f.Name as FarmName, f.Latitude as FarmLat, f.Longitude as FarmLng 
        FROM crop c
        INNER JOIN farm f ON c.FarmId = f.Id
        WHERE c.VendorId IN (SELECT id FROM vendor_list WHERE user_id = '{$user_id}' AND delete_flag = 0)
        ORDER BY c.Name ASC";

$result = $conn->query($sql);

$cropLocations = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $cropLocations[] = array(
            "cropName" => $row["CropName"],
            "lat" => $row["CropLat"],
            "lng" => $row["CropLng"],
            "farmName" => $row["FarmName"],
            "farmLat" => $row["FarmLat"],
            "farmLng" => $row["FarmLng"]
        );
    }
}

// Close the database connection
$conn->close();

// Return JSON response
header('Content-Type: application/json');
echo json_encode($cropLocations);
?>
