<?php
require_once('config.php');

$client_id = $_SESSION['userdata']['id'];  // Assuming the user ID is stored in the session

$qry = $conn->query("
    SELECT o.id AS order_id, o.status, o.date_updated, p.name AS product_name, p.image_path
    FROM `order_list` o
    JOIN `order_items` oi ON o.id = oi.order_id
    JOIN `product_list` p ON oi.product_id = p.id
    WHERE o.client_id = $client_id
    ORDER BY o.date_updated DESC
    LIMIT 1
");

$order = [];
if ($qry->num_rows > 0) {
    $order = $qry->fetch_assoc();
    // Map the status value to its corresponding description
    $status_map = [
        0 => 'Pending',
        1 => 'Confirmed',
        2 => 'Packed',
        3 => 'Out for Delivery',
        4 => 'Delivered',
        5 => 'Cancelled'
    ];
    $order['status'] = $status_map[$order['status']];
}

echo json_encode($order);
?>
