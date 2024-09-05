<?php
require_once('./config.php');

$other_id = $_POST['other_id'];
$other_role = $_POST['other_role'];
$user_id = $_settings->userdata('id');
$user_role = 'client';
$current_time = date('Y-m-d H:i:s');

// Update the messages to mark them as read and set last seen time
$sql = "
    UPDATE chat_messages 
    SET is_read = 1, last_seen = '$current_time' 
    WHERE 
        receiver_id = '$user_id' AND 
        receiver_role = '$user_role' AND 
        sender_id = '$other_id' AND 
        sender_role = '$other_role' AND 
        is_read = 0
";

$conn->query($sql);
?>
|