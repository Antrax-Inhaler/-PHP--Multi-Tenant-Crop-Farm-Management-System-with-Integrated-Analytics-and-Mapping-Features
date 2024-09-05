<?php
require_once('./../../config.php');

// Prepare the SQL query to join crop and vendor_list tables
$query = "
    SELECT c.Id AS id, c.Name AS title, c.PlannedPlantingDate AS start, v.avatar, v.contact, v.facebook, v.username
    FROM crop c
    LEFT JOIN vendor_list v ON c.VendorId = v.id
    WHERE c.PlannedPlantingDate IS NOT NULL
    AND c.DatePlanted IS NULL
    AND c.is_deleted = 0
    AND c.hide = 0
";

// Execute the query
$result = $conn->query($query);

// Initialize an array to hold the events
$events = array();

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Each crop is added to the events array
        $events[] = array(
            'id' => $row['id'],
            'title' => $row['title'],
            'start' => $row['start'],
            'avatar' => $row['avatar'],
            'contact' => $row['contact'],
            'facebook' => $row['facebook'],
            'username' => $row['username']
        );
    }
}

// Return the events as a JSON object
echo json_encode($events);

?>
