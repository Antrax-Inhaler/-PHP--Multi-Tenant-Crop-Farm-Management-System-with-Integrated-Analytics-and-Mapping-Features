<?php
require_once './../../config.php';

$sql = "SELECT DISTINCT Name FROM crop WHERE is_deleted = 0 ORDER BY Name ASC";
$result = $conn->query($sql);

echo "<option value=''>Select Crop Name</option>";

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<option value='{$row['Name']}'>{$row['Name']}</option>";
    }
} else {
    echo "<option value=''>No crops found</option>";
}
?>
