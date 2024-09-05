<?php
require_once('./../../config.php');

$other_id = isset($_GET['other_id']) ? $conn->real_escape_string($_GET['other_id']) : '';
$other_role = isset($_GET['other_role']) ? $conn->real_escape_string($_GET['other_role']) : '';

$user_id = $_settings->userdata('id');
$user_role = 'vendor';

// Fetch user details for display when no messages are found
$user_sql = "
    SELECT 
        id, firstname, lastname, avatar
    FROM (
        SELECT id, firstname, lastname, avatar FROM client_list
        UNION ALL
        SELECT id, shop_owner as firstname, '' as lastname, avatar FROM vendor_list
        UNION ALL
        SELECT id, firstname, lastname, avatar FROM users
        UNION ALL
        SELECT id, username as firstname, '' as lastname, image_path as avatar FROM nafa
    ) users
    WHERE id = '$other_id' AND '$other_role' = (
        CASE 
            WHEN EXISTS (SELECT 1 FROM client_list WHERE id = '$other_id') THEN 'client'
            WHEN EXISTS (SELECT 1 FROM vendor_list WHERE id = '$other_id') THEN 'vendor'
            WHEN EXISTS (SELECT 1 FROM users WHERE id = '$other_id') THEN 'user'
            WHEN EXISTS (SELECT 1 FROM nafa WHERE id = '$other_id') THEN 'nafa'
        END
    )
";

$user_result = $conn->query($user_sql);

if ($user_result->num_rows > 0) {
    $user_data = $user_result->fetch_assoc();
    $other_firstname = htmlspecialchars($user_data['firstname']);
    $other_lastname = htmlspecialchars($user_data['lastname']);
    $other_avatar = !empty($user_data['avatar']) ? htmlspecialchars($user_data['avatar']) : 'default_avatar.png';
} else {
    $other_firstname = 'Unknown';
    $other_lastname = '';
    $other_avatar = 'default_avatar.png';
}

// Fetch messages between the current user and the selected user
$sql = "
    SELECT 
        cm.id, 
        cm.sender_id, 
        cm.receiver_id, 
        cm.message, 
        cm.timestamp,
        sender.firstname as sender_firstname,
        sender.lastname as sender_lastname,
        sender.avatar as sender_avatar,
        receiver.firstname as receiver_firstname,
        receiver.lastname as receiver_lastname,
        receiver.avatar as receiver_avatar,
        sender.role as sender_role,
        receiver.role as receiver_role
    FROM chat_messages cm
    LEFT JOIN (
        SELECT id, firstname, lastname, avatar, 'client' as role FROM client_list
        UNION ALL
        SELECT id, shop_owner as firstname, '' as lastname, avatar, 'vendor' as role FROM vendor_list
        UNION ALL
        SELECT id, firstname, lastname, avatar, 'user' as role FROM users
        UNION ALL
        SELECT id, username as firstname, '' as lastname, image_path as avatar, 'nafa' as role FROM nafa
    ) sender ON sender.id = cm.sender_id AND sender.role = cm.sender_role
    LEFT JOIN (
        SELECT id, firstname, lastname, avatar, 'client' as role FROM client_list
        UNION ALL
        SELECT id, shop_owner as firstname, '' as lastname, avatar, 'vendor' as role FROM vendor_list
        UNION ALL
        SELECT id, firstname, lastname, avatar, 'user' as role FROM users
        UNION ALL
        SELECT id, username as firstname, '' as lastname, image_path as avatar, 'nafa' as role FROM nafa
    ) receiver ON receiver.id = cm.receiver_id AND receiver.role = cm.receiver_role
    WHERE 
        ((cm.sender_id = '$user_id' AND cm.sender_role = '$user_role' AND cm.receiver_id = '$other_id' AND cm.receiver_role = '$other_role') OR 
        (cm.sender_id = '$other_id' AND cm.sender_role = '$other_role' AND cm.receiver_id = '$user_id' AND cm.receiver_role = '$user_role'))
    ORDER BY cm.timestamp ASC
";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $isOutgoing = $row["sender_id"] == $user_id && $row["sender_role"] == $user_role;
        $message = htmlspecialchars($row["message"]);
        $timestamp = htmlspecialchars($row["timestamp"]);
        $avatar = !empty($row["sender_avatar"]) ? htmlspecialchars($row["sender_avatar"]) : $placeholderImage;

        if ($isOutgoing) {
            echo '<li class="chat outgoing"><h6>' . $message . '</h6></li>';
        } else {
            echo '<li class="chat incoming"><span><img src="../' . $avatar . '" alt=""></span><h6>' . $message . '</h6></li>';
        }
    }
} else {
    echo '
    <div class="no-messages" style="text-align: center" >
        <img src="../' . $other_avatar . '" alt="User Avatar" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);">
        <h6>' . $other_firstname . ' ' . $other_lastname . '</h6>
    </div>
    ';
}
?>
