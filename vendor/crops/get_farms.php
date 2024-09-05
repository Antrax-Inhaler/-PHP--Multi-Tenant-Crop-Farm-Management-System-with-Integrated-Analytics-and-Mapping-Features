<?php
require_once('./../../config.php');
$vendorId = $_settings->userdata('id'); // Current vendor ID

// Query to fetch farms associated with the current vendor
$sqlFarms = "SELECT Id, Name FROM farm WHERE VendorListId = $vendorId AND delete_flag = 0";
$resultFarms = $conn->query($sqlFarms);

$farms = [];
$defaultFarmId = 0;

if ($resultFarms->num_rows > 0) {
    while($row = $resultFarms->fetch_assoc()) {
        $farms[] = $row;
        if ($defaultFarmId == 0) {
            $defaultFarmId = $row['Id']; // Set the first farm as the default if no farm is selected
        }
    }
}

echo json_encode(['farms' => $farms, 'defaultFarmId' => $defaultFarmId]);
?>
