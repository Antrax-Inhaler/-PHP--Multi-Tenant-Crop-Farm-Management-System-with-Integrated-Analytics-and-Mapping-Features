<?php
require_once('./../../config.php');
$user_id = $_settings->userdata('id');
$message = $_POST['message'];
$other_id = $_POST['other_id'];
$other_role = $_POST['other_role'];
$user_role = 'vendor'; // Your current user role

// Sanitize input
$message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

// Validate sender and receiver
function validate_user($conn, $id, $role) {
    switch ($role) {
        case 'client':
            $table = 'client_list';
            break;
        case 'vendor':
            $table = 'vendor_list';
            break;
        case 'user':
            $table = 'users';
            break;
        case 'nafa':
            $table = 'nafa';
            break;
        default:
            return false;
    }
    $sql = "SELECT 1 FROM $table WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->store_result();
    return $stmt->num_rows > 0;
}

if (validate_user($conn, $user_id, $user_role) && validate_user($conn, $other_id, $other_role)) {
    // Insert message into the database
    $sql = "INSERT INTO chat_messages (sender_id, sender_role, receiver_id, receiver_role, message, timestamp) VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    // Bind parameters as appropriate
    $stmt->bind_param("issss", $user_id, $user_role, $other_id, $other_role, $message);
    if ($stmt->execute()) {
        // Fetch the updated conversation (last message)
        $fetchSql = "SELECT message FROM chat_messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) ORDER BY timestamp DESC LIMIT 1";
        $stmt = $conn->prepare($fetchSql);
        $stmt->bind_param("iiii", $user_id, $other_id, $other_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
        }
    } else {
        echo "Error: " . $stmt->error;
    }
} else {
    echo "Invalid sender or receiver.";
}
?>
