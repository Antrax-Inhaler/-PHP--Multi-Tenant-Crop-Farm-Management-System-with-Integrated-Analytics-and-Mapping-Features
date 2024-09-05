<?php
require_once('./../../config.php');

// Define a placeholder image URL
$placeholderImage = 'uploads/profile-pic.jpg';

$query = isset($_GET['query']) ? $_GET['query'] : '';

$user_id = $_settings->userdata('id');
$user_role = 'vendor';

// Query to get all users with search filter, excluding the current user
$sql = "SELECT 'client' as type, id, firstname, lastname, avatar as image_path FROM client_list
        WHERE CONCAT(firstname, ' ', lastname) LIKE '%$query%'
        AND id != '$user_id'
        UNION
        SELECT 'vendor' as type, id, shop_owner as firstname, '' as lastname, avatar as image_path FROM vendor_list
        WHERE shop_owner LIKE '%$query%'
        AND id != '$user_id'
        UNION
        SELECT 'user' as type, id, firstname, lastname, avatar as image_path FROM users
        WHERE CONCAT(firstname, ' ', lastname) LIKE '%$query%'
        AND id != '$user_id'
        UNION
        SELECT 'nafa' as type, id, username as firstname, '' as lastname, image_path as image_path FROM nafa
        WHERE username LIKE '%$query%'
        AND id != '$user_id'
        ORDER BY RAND()"; // Add ORDER BY RAND() to randomize results

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $imagePath = !empty($row["image_path"]) ? htmlspecialchars($row["image_path"]) : $placeholderImage;
        echo '<div class="user" data-other-id="' . htmlspecialchars($row["id"]) . '" data-other-role="' . htmlspecialchars($row["type"]) . '">';
        echo '<img src="../' . $imagePath . '" alt="Avatar">';
        echo '<div class="user-name-container">';
        echo '<div class="user-name">' . htmlspecialchars($row["firstname"] . ' ' . $row["lastname"]) . '</div>';
        echo '</div>';
        echo '</div>';
    }
} else {
    echo '<div class="user" id="no">';
    echo '<div>No User Found';
    echo '</div>';
    echo '</div>';
}
?>
