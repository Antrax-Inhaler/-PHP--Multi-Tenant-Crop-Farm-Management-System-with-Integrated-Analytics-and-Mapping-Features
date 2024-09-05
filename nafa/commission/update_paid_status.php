<?php
require_once('config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $vendor_id = $_POST['vendor_id'];
    $month = $_POST['month'];
    $paid = $_POST['paid'];

    $stmt = $conn->prepare("UPDATE vendor_commissions SET paid = ? WHERE vendor_id = ? AND month = ?");
    $stmt->bind_param('iis', $paid, $vendor_id, $month);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update the paid status.']);
    }
    $stmt->close();
}
?>
