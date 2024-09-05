<?php
require_once('./../../config.php');

$placeholderImage = 'uploads/profile-pic.jpg';
$user_id = $_settings->userdata('id');
$user_role = 'vendor';

// Get the search term from the request
$searchTerm = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

$sql = "
    SELECT 
        cm.id as message_id, 
        cm.sender_id, 
        cm.receiver_id, 
        cm.message, 
        cm.timestamp,
        cm.is_read,
        cm.last_seen,  -- Fetch the last_seen timestamp
        sender.firstname as sender_firstname,
        sender.lastname as sender_lastname,
        sender.avatar as sender_avatar,
        receiver.firstname as receiver_firstname,
        receiver.lastname as receiver_lastname,
        receiver.avatar as receiver_avatar,
        sender.role as sender_role,
        receiver.role as receiver_role
    FROM chat_messages cm
    JOIN (
        SELECT 
            MAX(id) as latest_id 
        FROM chat_messages 
        GROUP BY 
            LEAST(sender_id, receiver_id), 
            GREATEST(sender_id, receiver_id),
            LEAST(sender_role, receiver_role),
            GREATEST(sender_role, receiver_role)
    ) latest 
    ON cm.id = latest.latest_id
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
        ((cm.sender_id = '$user_id' AND cm.sender_role = '$user_role') OR 
        (cm.receiver_id = '$user_id' AND cm.receiver_role = '$user_role')) AND
        NOT (cm.sender_id = '$user_id' AND cm.receiver_id = '$user_id' AND cm.sender_role = '$user_role' AND cm.receiver_role = '$user_role') AND
        (sender.firstname LIKE '%$searchTerm%' OR 
        sender.lastname LIKE '%$searchTerm%' OR 
        receiver.firstname LIKE '%$searchTerm%' OR 
        receiver.lastname LIKE '%$searchTerm%' OR 
        cm.message LIKE '%$searchTerm%')
    ORDER BY cm.timestamp DESC
";

$result = $conn->query($sql);

function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        if ($row["sender_id"] == $user_id && $row["sender_role"] == $user_role) {
            $unreadClass = ($row["is_read"] == 0 && $row["receiver_id"] == $user_id && $row["sender_id"] != $user_id) ? 'unread' : '';
            $otherImage = !empty($row["receiver_avatar"]) ? htmlspecialchars($row["receiver_avatar"]) : $placeholderImage;
            $otherName = htmlspecialchars($row["receiver_firstname"] . ' ' . $row["receiver_lastname"]);
            $otherId = htmlspecialchars($row["receiver_id"]);
            $otherRole = htmlspecialchars($row["receiver_role"]);
        } else {
            $otherImage = !empty($row["sender_avatar"]) ? htmlspecialchars($row["sender_avatar"]) : $placeholderImage;
            $otherName = htmlspecialchars($row["sender_firstname"] . ' ' . $row["sender_lastname"]);
            $otherId = htmlspecialchars($row["sender_id"]);
            $otherRole = htmlspecialchars($row["sender_role"]);
        }

        $latestMessage = htmlspecialchars($row["message"]);
        $timestamp = time_elapsed_string($row["timestamp"]);
        $unreadClass = ($row["is_read"] == 0 && $row["receiver_id"] == $user_id && $row["sender_id"] != $user_id) ? 'unread' : '';
        $lastSeen = $row["last_seen"] ? time_elapsed_string($row["last_seen"]) : '';

        echo '<div class="conversation ' . $unreadClass . '" data-other-id="' . $otherId . '" data-other-role="' . $otherRole . '" data-is-read="' . $row["is_read"] . '" data-last-sender-id="' . $row["sender_id"] . '" data-last-seen="' . $row["last_seen"] . '">';
        echo '<img src="../' . $otherImage . '" alt="User Avatar">';
        echo '<div class="conversation-info">';
        if ($row["sender_id"] == $user_id && $row["sender_role"] == $user_role && $lastSeen) {
            echo '<div class="last-seen">Seen ' . $lastSeen . ' ago</div>';
        }
        echo '<div class="username">' . $otherName . '</div>';
        echo '<div class="latest-message">' . $latestMessage . '</div>';
        echo '<div class="timestamp">' . $timestamp . '</div>';
        echo '</div>';
        echo '</div>';
    }
} else {
    echo "No conversations found";
}
?>
