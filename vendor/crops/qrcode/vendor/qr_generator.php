<?php
require_once('./../../config.php');
require_once('qrcode/vendor/autoload.php');

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

if (!isset($_GET['id'])) {
    echo "Crop ID not provided.";
    exit;
}

$cropId = $_GET['id'];

// Generate the QR code
$qrCode = QrCode::create($cropId)
    ->setSize(300)
    ->setMargin(10)
    ->setErrorCorrectionLevel(new ErrorCorrectionLevel(ErrorCorrectionLevel::HIGH))
    ->setWriterOptions(['exclude_xml_declaration' => true]);

$writer = new PngWriter();
$result = $writer->write($qrCode);

// Save QR code to a temporary file
$tempDir = sys_get_temp_dir();
$filePath = $tempDir . '/qrcode_' . $cropId . '.png';
$result->saveToFile($filePath);

?>
<!DOCTYPE html>
<html>
<head>
    <title>QR Code</title>
</head>
<body>
    <div class="text-center">
        <img src="data:image/png;base64,<?= base64_encode(file_get_contents($filePath)) ?>" alt="QR Code">
        <p>Crop ID: <?= htmlspecialchars($cropId) ?></p>
    </div>
</body>
</html>
