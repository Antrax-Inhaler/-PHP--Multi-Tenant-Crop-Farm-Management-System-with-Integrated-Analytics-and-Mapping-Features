<?php
require_once('../config.php');

// Fetch the latest updated order
$sql = "SELECT * FROM order_list ORDER BY date_updated DESC LIMIT 1";
$result = $conn->query($sql);

$order = [];
if ($result->num_rows > 0) {
    $order = $result->fetch_assoc();
}

$conn->close();

echo json_encode($order);
?>
