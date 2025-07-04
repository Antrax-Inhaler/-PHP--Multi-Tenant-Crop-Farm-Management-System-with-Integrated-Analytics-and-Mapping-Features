<?php
require_once './../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cropIds'])) {
    $cropIds = json_decode($_POST['cropIds'], true);

    if (!is_array($cropIds)) {
        echo json_encode([]);
        exit;
    }

    $sanitizedIds = array_filter($cropIds, fn($id) => is_numeric($id));

    if (empty($sanitizedIds)) {
        echo json_encode([]);
        exit;
    }

    $idList = implode(',', $sanitizedIds);

    $query = "
        SELECT 
            Id, Name, Type, SizeOfPlantation, Latitude, Longitude
        FROM 
            crop
        WHERE 
            Id IN ($idList) AND 
            delete_flag = 0 AND 
            is_deleted = 0 AND 
            hide = 0
    ";

    $result = $conn->query($query);

    if ($result) {
        $crops = [];
        while ($row = $result->fetch_assoc()) {
            $crops[] = $row;
        }
        echo json_encode($crops);
    } else {
        error_log("Database query failed: " . $conn->error);
        echo json_encode(['error' => 'Database query failed.']);
    }
} else {
    echo json_encode(['error' => 'Invalid request.']);
}
