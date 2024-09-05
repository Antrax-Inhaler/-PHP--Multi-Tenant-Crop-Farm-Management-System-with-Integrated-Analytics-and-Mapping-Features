<?php
require_once('../config.php');

if(isset($_POST['imgBase64'])) {
    $data = $_POST['imgBase64'];
    $data = str_replace('data:image/png;base64,', '', $data);
    $data = str_replace(' ', '+', $data);
    $data = base64_decode($data);
    $file = '../uploads/vendors/signature_' . uniqid() . '.png';

    if(file_put_contents($file, $data)) {
        echo json_encode(["status" => "success", "msg" => "Signature saved successfully"]);
    } else {
        echo json_encode(["status" => "error", "msg" => "Failed to save signature"]);
    }
} else {
    echo json_encode(["status" => "error", "msg" => "No signature data received"]);
}
?>
