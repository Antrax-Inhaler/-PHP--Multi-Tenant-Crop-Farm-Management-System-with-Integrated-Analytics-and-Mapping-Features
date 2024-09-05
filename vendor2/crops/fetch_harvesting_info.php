<?php
// Include your database connection file
// Check if the crop_id is set and not empty
if (isset($_POST['crop_id']) && !empty($_POST['crop_id'])) {
    // Sanitize the input to prevent SQL injection
    $crop_id = $_POST['crop_id'];

    // Query to fetch harvesting information for the specified crop
    $query = "SELECT * FROM `harvest` WHERE `CropId` = $crop_id";

    // Execute the query
    $result = $conn->query($query);

    // Check if there are any rows returned
    if ($result->num_rows > 0) {
        // Initialize variable to store HTML output
        $output = '';

        // Fetch data and generate HTML output
        while ($row = $result->fetch_assoc()) {
            $output .= '<p><strong>Harvested Date:</strong> ' . $row['HarvestedDate'] . '</p>';
            $output .= '<p><strong>Amount of Harvest:</strong> ' . $row['AmountOfHarvest'] . '</p>';
        }

        // Output HTML
        echo $output;
    } else {
        // If no rows returned, display a message
        echo '<p>No harvesting information available for this crop.</p>';
    }
} else {
    // If crop_id is not set or empty, display an error message
    echo '<p>Error: Crop ID is missing or invalid.</p>';
}

// Close the database connection
$conn->close();
?>
<H1>Try </H1>