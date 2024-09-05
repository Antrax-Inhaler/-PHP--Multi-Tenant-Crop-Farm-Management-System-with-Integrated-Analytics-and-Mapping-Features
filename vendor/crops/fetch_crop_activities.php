<?php
// Database connection
require_once('./../../config.php');

if (isset($_GET['crop_id'])) {
    $cropId = $_GET['crop_id'];

    // Fetch the relevant data from your database
    $query = "SELECT 
                    crop.Name AS cropName, 
                    crop.Type AS cropType, 
                    TIMESTAMPDIFF(DAY, crop.PlannedPlantingDate, CURDATE()) AS cropAge, 
                    crop.SizeOfPlantation, 
                    CONCAT(crop.Latitude, ',', crop.Longitude) AS Location, 
                    crop.Status AS cropStatus,
                    crop_activity.id AS activityId,
                    crop_activity.activity_date AS activityDate,
                    crop_activity.activity_type AS activityType,
                    crop_activity.description AS activityDescription
              FROM crop
              LEFT JOIN crop_activity ON crop.Id = crop_activity.crop_id
              WHERE crop.Id = ?";

    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param('i', $cropId);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = [
            'crop' => null,
            'activities' => []
        ];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                if ($data['crop'] === null) {
                    // Fetch crop details only once
                    $data['crop'] = [
                        'cropName' => $row['cropName'],
                        'cropType' => $row['cropType'],
                        'cropAge' => $row['cropAge'],
                        'SizeOfPlantation' => $row['SizeOfPlantation'],
                        'Location' => $row['Location'],
                        'cropStatus' => $row['cropStatus']
                    ];
                }

                // Fetch all activities related to the crop
                if ($row['activityId'] !== null) {
                    $data['activities'][] = [
                        'activityId' => $row['activityId'],
                        'activityDate' => $row['activityDate'],
                        'activityType' => $row['activityType'],
                        'activityDescription' => $row['activityDescription']
                    ];
                }
            }

            echo json_encode(['success' => true, 'data' => $data]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No data found']);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Query preparation failed: ' . $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
