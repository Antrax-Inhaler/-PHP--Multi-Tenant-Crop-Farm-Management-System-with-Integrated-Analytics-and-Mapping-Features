<?php
if (!isset($_GET['path'])) {
    echo "QR code path not provided.";
    exit;
}

$qrCodePath = urldecode($_GET['path']);

if (!file_exists($qrCodePath)) {
    echo "QR code file does not exist.";
    exit;
}

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="'.basename($qrCodePath).'"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($qrCodePath));
readfile($qrCodePath);
exit;
?>
