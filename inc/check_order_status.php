<?php
// Perform necessary database connection or include required files

// Example: Query to fetch the latest order update
$sql = "SELECT * FROM `order_list` WHERE `date_updated` = (SELECT MAX(`date_updated`) FROM `order_list`)";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    // Prepare notification data
    $notification = array(
        'title' => 'Order Status Update',
        'message' => 'Order ID ' . $row['id'] . ' status changed to ' . getOrderStatusText($row['status'])
    );
    echo json_encode($notification);
} else {
    echo json_encode(array('error' => 'No new order status updates'));
}

// Close database connection or perform cleanup
$conn->close();

// Helper function to get order status text based on status code
function getOrderStatusText($status) {
    switch($status) {
        case 0: return 'Pending';
        case 1: return 'Confirmed';
        case 2: return 'Packed';
        case 3: return 'Out for Delivery';
        case 4: return 'Delivered';
        case 5: return 'Cancelled';
        default: return 'Unknown';
    }
}
?>
