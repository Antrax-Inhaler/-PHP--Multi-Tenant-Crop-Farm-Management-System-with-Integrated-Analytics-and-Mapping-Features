<?php
// Assuming you have already established a database connection

// Check if the cropName is set and not empty
if (isset($_POST['cropName']) && !empty($_POST['cropName'])) {
    $cropName = $_POST['cropName'];

    // Prepare and execute query to fetch crop types based on the selected crop name
    $stmt = $conn->prepare("SELECT DISTINCT Type FROM cropactivityrecommendation WHERE CropType = ?");
    $stmt->bind_param("s", $cropName);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch crop types and store them in an array
    $cropTypes = array();
    while ($row = $result->fetch_assoc()) {
        $cropTypes[] = $row['Type'];
    }

    // Return crop types as JSON response
    echo json_encode($cropTypes);
} else {
    // If cropName is not set or empty, return an empty response
    echo json_encode(array());
}
?>
