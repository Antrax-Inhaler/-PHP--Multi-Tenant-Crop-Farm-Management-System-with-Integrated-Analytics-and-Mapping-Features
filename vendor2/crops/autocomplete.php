// vendor/crops/autocomplete.php
<?php
require_once './../../config.php';

// Check if term and type are set
if (isset($_POST['term']) && isset($_POST['type'])) {
    $term = $_POST['term'];
    $type = $_POST['type'];

    // Initialize an empty array to store autocomplete suggestions
    $suggestions = array();

    // Adjust the SQL query based on the type
    if ($type === 'crop_type') {
        // Fetch distinct Crop Types from cropactivityrecommendation table
        $query = $conn->query("SELECT DISTINCT CropType FROM cropactivityrecommendation WHERE CropType LIKE '%$term%'");
        while ($row = $query->fetch_assoc()) {
            $suggestions[] = $row['CropType'];
        }
    } elseif ($type === 'crop_variety') {
        // Fetch Crop Varieties based on the selected Crop Type
        if (isset($_POST['crop_type'])) {
            $crop_type = $_POST['crop_type'];
            $query = $conn->query("SELECT DISTINCT CropVariety FROM cropactivityrecommendation WHERE CropType = '$crop_type' AND CropVariety LIKE '%$term%'");
            while ($row = $query->fetch_assoc()) {
                $suggestions[] = $row['CropVariety'];
            }
        }
    }

    // Return the suggestions as JSON
    echo json_encode($suggestions);
}
?>
