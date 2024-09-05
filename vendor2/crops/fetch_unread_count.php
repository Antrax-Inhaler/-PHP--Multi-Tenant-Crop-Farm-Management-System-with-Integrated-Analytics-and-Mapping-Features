<?php
require_once('./../../config.php');

$user_id = $_settings->userdata('id');
$user_role = 'vendor';

$sql = "
    SELECT COUNT(*) AS unread_count 
    FROM chat_messages 
    WHERE 
        receiver_id = '$user_id' AND 
        receiver_role = '$user_role' AND 
        is_read = 0
";

$result = $conn->query($sql);
if ($result) {
    $row = $result->fetch_assoc();
    echo $row['unread_count'];
} else {
    echo 'Error: ' . $conn->error;
}
?>
