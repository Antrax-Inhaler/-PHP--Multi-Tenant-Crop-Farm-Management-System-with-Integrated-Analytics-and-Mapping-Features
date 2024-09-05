<?php
$conn = new mysqli("localhost", "root", "", "mvogms_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $client_id = $_settings->userdata('id');
    $order_id = $_POST['order_id'];
    $product_id = $_POST['product_id'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];
    $created_at = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("INSERT INTO review (product_id, order_id, client_id, rating, comment, date_created) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiiss", $product_id, $order_id, $client_id, $rating, $comment, $created_at);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'msg' => 'Review submitted successfully!']);
    } else {
        echo json_encode(['status' => 'failed', 'msg' => 'Failed to submit review: ' . $stmt->error]);
    }
    $stmt->close();
    $conn->close();
    exit;
} else {
    echo json_encode(['status' => 'failed', 'msg' => 'Invalid request']);
    exit;
}
?>
