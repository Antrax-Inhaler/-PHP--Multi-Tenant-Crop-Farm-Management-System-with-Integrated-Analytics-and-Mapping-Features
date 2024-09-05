<?php
require_once('./../../config.php');

// Prepare the SQL query to fetch distinct crop names
$query = "
    SELECT DISTINCT c.Name 
    FROM crop c
    WHERE c.is_deleted = 0
    AND c.hide = 0
";

// Execute the query
$result = $conn->query($query);

// Initialize an array to hold the crops
$crops = array();

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Each crop name is added to the crops array
        $crops[] = array(
            'name' => $row['Name']
        );
    }

    // Prepare the response array with success status
    $response = array(
        'success' => true,
        'crops' => $crops
    );
} else {
    // If the query fails or no rows are returned, send an error response
    $response = array(
        'success' => false,
        'message' => 'No crops found or query failed.'
    );
}

// Return the response as a JSON object
echo json_encode($response);
?>
