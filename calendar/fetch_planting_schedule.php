<?php

require_once './../config.php';

// The filters are no longer needed, so we skip this part
// $filters = json_decode(file_get_contents('php://input'), true);

// Define the SQL query without any filters
$sql = "
    SELECT 
        c.Id AS id,
        c.Name AS title,
        c.Type,
        c.PlannedPlantingDate,
        c.DatePlanted,
        c.SizeOfPlantation,
        c.Description,
        c.Picture1,
        c.Picture2,
        c.Picture3,
        v.shop_owner AS shopOwner,
        v.contact AS contact,
        v.facebook AS facebook,
        v.username AS username,
        v.avatar AS avatar,
        h.PlannedHarvestingDate,
        h.HarvestedDate
    FROM crop c
    LEFT JOIN vendor_list v ON c.VendorId = v.id
    LEFT JOIN harvest h ON c.Id = h.CropId
    WHERE c.delete_flag = 0
    AND c.is_deleted = 0
";

$result = $conn->query($sql);
$events = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Check if the crop has been harvested and set the color accordingly
        if (!empty($row['HarvestedDate'])) {
            $events[] = [
                'id' => $row['id'],
                'title' => $row['title'] . ' (Harvest)',
                'start' => $row['HarvestedDate'],
                'color' => 'yellow',  // Harvested event color
                'extendedProps' => [
                    'type' => $row['Type'],
                    'datePlanted' => $row['DatePlanted'],
                    'sizeOfPlantation' => $row['SizeOfPlantation'],
                    'description' => $row['Description'],
                    'pictures' => [
                        $row['Picture1'],
                        $row['Picture2'],
                        $row['Picture3']
                    ],
                    'shopOwner' => $row['shopOwner'],
                    'contact' => $row['contact'],
                    'facebook' => $row['facebook'],
                    'username' => $row['username'],
                    'avatar' => $row['avatar']
                ]
            ];
        } else {
            $events[] = [
                'id' => $row['id'],
                'title' => $row['title'] . ' (Planting)',
                'start' => $row['PlannedPlantingDate'],
                'color' => 'green',  // Planting event color
                'extendedProps' => [
                    'type' => $row['Type'],
                    'datePlanted' => $row['DatePlanted'],
                    'sizeOfPlantation' => $row['SizeOfPlantation'],
                    'description' => $row['Description'],
                    'pictures' => [
                        $row['Picture1'],
                        $row['Picture2'],
                        $row['Picture3']
                    ],
                    'shopOwner' => $row['shopOwner'],
                    'contact' => $row['contact'],
                    'facebook' => $row['facebook'],
                    'username' => $row['username'],
                    'avatar' => $row['avatar']
                ]
            ];
        }
    }
}

// Return events as JSON
echo json_encode($events);

?>
