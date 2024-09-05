<?php
require_once('./../../config.php');

$sql = "
    SELECT f.Id, f.Name AS FarmName, f.Latitude AS FarmLat, f.Longitude AS FarmLng,
           c.Name AS CropName, c.Type, c.PlannedPlantingDate, c.DatePlanted, 
           c.SizeOfPlantation, c.Description, c.Picture1, 
           cd.Name AS PestDiseaseName, cd.SizeOfAreaAffected, cd.Status
    FROM farm f
    JOIN crop c ON f.Id = c.FarmId
    JOIN croppestdisease cd ON c.Id = cd.CropID
    WHERE cd.Status != 'Fixed'
    ORDER BY f.Name ASC, c.Name ASC
";

$result = $conn->query($sql);

$farmDataArray = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $farmDataArray[$row['FarmName']]['farmLat'] = $row['FarmLat'];
        $farmDataArray[$row['FarmName']]['farmLng'] = $row['FarmLng'];
        $farmDataArray[$row['FarmName']]['crops'][$row['CropName']]['details'] = [
            "Type" => $row["Type"],
            "PlannedPlantingDate" => $row["PlannedPlantingDate"],
            "DatePlanted" => $row["DatePlanted"],
            "SizeOfPlantation" => $row["SizeOfPlantation"],
            "Description" => $row["Description"],
            "Picture1" => $row["Picture1"]
        ];
        $farmDataArray[$row['FarmName']]['crops'][$row['CropName']]['pestDiseases'][] = [
            "PestDiseaseName" => $row["PestDiseaseName"],
            "SizeOfAreaAffected" => $row["SizeOfAreaAffected"],
            "Status" => $row["Status"]
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($farmDataArray);
?>
