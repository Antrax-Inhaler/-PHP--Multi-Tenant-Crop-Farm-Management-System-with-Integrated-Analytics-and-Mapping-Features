<?php
// Include your database connection file here
include_once("classes/DBConnection.php");

// Check if the crop_id parameter is set
if (isset($_POST['crop_id'])) {
    $crop_id = $_POST['crop_id'];

    // Perform database query to fetch recommended activity based on crop_id
    // Example query, modify as per your database schema
    $query = $conn->prepare("SELECT Explanation, RecommendedActivity FROM cropactivityrecommendation WHERE CropType = ?");
    $query->bind_param("i", $crop_id);
    $query->execute();
    $result = $query->get_result();

    // Check if query was successful
    if ($result) {
        // Fetch the result row as an associative array
        $row = $result->fetch_assoc();

        // Prepare data to be sent back as JSON response
        $response = array(
            'flash_message' => 'Recommended Activity',
            'explanation' => $row['Explanation'],
            'recommended_activity' => $row['RecommendedActivity']
        );

        // Output JSON response
        echo json_encode($response);
    } else {
        // Handle query error
        echo "Error: " . $conn->error;
    }

    // Close database connection
    $conn->close();
} else {
    // Handle case where crop_id parameter is not set
    echo "Error: crop_id parameter is not set.";
}
?>
