<?php
require_once './../../config.php';

if (isset($_GET['cropName'])) {
    $cropName = $_GET['cropName'];

    $sql = "SELECT DISTINCT Type FROM crop WHERE Name = ? AND delete_flag = 0 AND is_deleted = 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $cropName);
    $stmt->execute();
    $result = $stmt->get_result();

    $types = [];
    while ($row = $result->fetch_assoc()) {
        $types[] = $row['Type'];
    }

    echo json_encode($types);
}
?>