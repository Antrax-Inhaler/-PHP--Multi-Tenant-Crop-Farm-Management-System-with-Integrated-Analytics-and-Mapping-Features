<?php

require_once './../config.php';

$cropName = $_POST['cropName'];
$cropType = $_POST['cropType'];
$plantingDateFrom = $_POST['plantingDateFrom'];
$plantingDateTo = $_POST['plantingDateTo'];

// SQL query to fetch filtered crops and join with vendor_list to get shop_owner
$sql = "
    SELECT 
        c.Id AS crop_id, 
        c.Name, 
        c.Type, 
        c.Latitude, 
        c.Longitude, 
        c.SizeOfPlantation, 
        v.shop_owner
    FROM crop c
    LEFT JOIN vendor_list v ON c.VendorId = v.id
    WHERE c.delete_flag = 0
    AND c.is_deleted = 0
    AND c.Name LIKE '%$cropName%'
    AND c.Type LIKE '%$cropType%'
    AND c.PlannedPlantingDate BETWEEN '$plantingDateFrom' AND '$plantingDateTo'
";

$result = $conn->query($sql);
$crops = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $crops[] = $row;
    }
}

// Return the result as a JSON object
echo json_encode($crops);
?>
