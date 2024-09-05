<?php
// Database connection
require_once('./../../config.php');

if (isset($_GET['pest_disease_id'])) {
    $pestDiseaseId = $_GET['pest_disease_id'];

    // Fetch the relevant data from your database
    $query = "SELECT 
                    crop.Name AS cropName, 
                    crop.Type AS cropType, 
                    TIMESTAMPDIFF(DAY, crop.PlannedPlantingDate, CURDATE()) AS cropAge, 
                    crop.SizeOfPlantation, 
                    CONCAT(crop.Latitude, ',', crop.Longitude) AS Location, 
                    crop.Status AS cropStatus,
                    croppestdisease.Name AS pestName, 
                    croppestdisease.SizeOfAreaAffected AS affectedArea,
                    croppestdisease.Status AS pestStatus 
              FROM crop
              JOIN croppestdisease ON crop.Id = croppestdisease.CropID
              WHERE croppestdisease.Id = ?";

    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param('i', $pestDiseaseId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
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
