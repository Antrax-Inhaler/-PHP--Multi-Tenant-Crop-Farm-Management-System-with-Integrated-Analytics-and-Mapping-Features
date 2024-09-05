<?php
// Check if the request is coming from localhost
$isLocal = $_SERVER['REMOTE_ADDR'] === '127.0.0.1' || $_SERVER['REMOTE_ADDR'] === '::1';

// If it's a local request, proceed to fetch crop details
if($isLocal) {
    // Include your database connection file here
    // Example: include 'db_connection.php';

    // Check if crop ID is provided
    if(isset($_POST['crop_id'])) {
        $cropId = $_POST['crop_id'];

        // Fetch crop details from the database
        $crop_query = $conn->query("SELECT * FROM crop WHERE Id = $cropId");
        $crop = $crop_query->fetch_assoc();

        // Fetch harvest information
        $harvest_query = $conn->query("SELECT * FROM harvest WHERE CropId = $cropId");
        $harvest = $harvest_query->fetch_assoc();

        // Fetch pest and disease information
        $pest_disease_query = $conn->query("SELECT * FROM croppestdisease WHERE CropID = $cropId");
        $pest_disease = $pest_disease_query->fetch_assoc();

        // Prepare the HTML to display crop details
        $cropDetailsHTML = '
            <div id="cropDetailsContainer">
                <h2>Crop Details</h2>
                <p><strong>Name:</strong> ' . $crop['Name'] . '</p>
                <p><strong>Type:</strong> ' . $crop['Type'] . '</p>
                <p><strong>Planned Planting Date:</strong> ' . $crop['PlannedPlantingDate'] . '</p>
                <p><strong>Date Planted:</strong> ' . $crop['DatePlanted'] . '</p>
                <p><strong>Size of Plantation:</strong> ' . $crop['SizeOfPlantation'] . '</p>
                <p><strong>Description:</strong> ' . $crop['Description'] . '</p>
                <p><strong>Status:</strong> ' . $crop['Status'] . '</p>
                <!-- Add more crop details here -->
            </div>
        ';

        // Prepare the HTML to display harvest information
        $harvestInfoHTML = '
            <div id="harvestInfoContainer">
                <h2>Harvesting Information</h2>
                <p><strong>Harvested Date:</strong> ' . $harvest['HarvestedDate'] . '</p>
                <p><strong>Amount Harvested:</strong> ' . $harvest['AmountOfHarvest'] . '</p>
                <!-- Add more harvest information here -->
            </div>
        ';

        // Prepare the HTML to display pest and disease information
        $pestDiseaseHTML = '
            <div id="pestDiseaseContainer">
                <h2>Pest and Disease</h2>
                <p><strong>Name:</strong> ' . $pest_disease['Name'] . '</p>
                <p><strong>Size of Area Affected:</strong> ' . $pest_disease['SizeOfAreaAffected'] . '</p>
                <p><strong>Status:</strong> ' . $pest_disease['Status'] . '</p>
                <!-- Add more pest and disease information here -->
            </div>
        ';

        // Combine all HTML and send as response
        $response = $cropDetailsHTML . $harvestInfoHTML . $pestDiseaseHTML;
        echo $response;
    } else {
        echo 'Crop ID is not provided.';
    }
} else {
    echo 'Access denied.';
}
?>
