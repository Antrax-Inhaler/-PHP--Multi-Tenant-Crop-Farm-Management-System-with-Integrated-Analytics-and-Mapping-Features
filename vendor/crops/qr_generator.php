<?php
require_once('./../../config.php');
require_once('./../../vendor_qr1/autoload.php');
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['order_id'])) {
    echo json_encode(['success' => false, 'message' => 'Crop ID not provided.']);
    exit;
}

$cropId = $_POST['order_id'];

// URL to be embedded in the QR code
$url = "http://localhost/mvogms/?page=result/crop_info&id=" . $cropId;

// Set QR code options without logo and custom styles
$options = new QROptions([
    'version'    => 5,
    'eccLevel'   => QRCode::ECC_L,
    'scale'      => 5,
    'outputType' => QRCode::OUTPUT_IMAGE_PNG,
    'imageBase64' => true, // Use base64 encoding to embed the image directly
    'colorDark' => '#000000', // QR code color (default black)
    'colorLight' => '#ffffff', // Background color (default white)
]);

// Generate QR code with the URL
$qrcode = new QRCode($options);
$qrCodeImage = $qrcode->render($url);

// Return JSON response with QR code image
echo json_encode(['success' => true, 'qrImage' => $qrCodeImage]);
?>
