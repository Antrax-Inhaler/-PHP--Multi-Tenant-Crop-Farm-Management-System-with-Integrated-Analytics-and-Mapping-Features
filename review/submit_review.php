<?php
// Include necessary files
require_once('config.php');

// Function to save review
function saveReview($orderId, $productId, $clientId, $rating, $comment) {
    global $conn; // Assuming $conn is your database connection variable

    // Perform SQL insert operation to save the review data
    $orderId = mysqli_real_escape_string($conn, $orderId);
    $productId = mysqli_real_escape_string($conn, $productId);
    $clientId = mysqli_real_escape_string($conn, $clientId);
    $rating = mysqli_real_escape_string($conn, $rating);
    $comment = mysqli_real_escape_string($conn, $comment);

    $sql = "INSERT INTO `review` (order_id, product_id, client_id, rating, comment) 
            VALUES ('$orderId', '$productId', '$clientId', '$rating', '$comment')";

    if (mysqli_query($conn, $sql)) {
        return true; // Review saved successfully
    } else {
        return false; // Error saving review
    }
}
?>
