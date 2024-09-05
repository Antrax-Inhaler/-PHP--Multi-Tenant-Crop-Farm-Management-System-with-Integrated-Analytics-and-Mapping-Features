<?php
require_once('./../../config.php');
require_once('vendor2/autoload.php');
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

header('Content-Type: application/json');

if (isset($_POST['generate_qr']) && isset($_POST['crop_id'])) {
    $cropID = intval($_POST['crop_id']);

    $options = new QROptions([
        'eccLevel' => QRCode::ECC_L,
        'outputType' => QRCode::OUTPUT_IMAGE_PNG,
        'imageBase64' => true,
    ]);

    $qrcode = new QRCode($options);
    $qrImage = $qrcode->render($cropID);

    echo json_encode([
        'success' => true,
        'qrImage' => $qrImage,
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request',
    ]);
}
?>
