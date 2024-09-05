<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('gemini_ai/vendor/autoload.php');

use GeminiAPI\Client;
use GeminiAPI\Resources\Parts\TextPart;

header('Content-Type: application/json');

$message = $_POST['message'] ?? '';

// Initialize Gemini API client
$client = new Client("AIzaSyA9_Am1Lj-c_LRc46iWn9z5uCyjlvnZDXw");

try {
    // Generate content from Gemini API
    $aiResponse = $client->geminiPro()->generateContent(new TextPart($message))->text();

    if (!empty($aiResponse)) {
        $response = $aiResponse;
    } else {
        $response = "I'm sorry, I couldn't understand your question.";
    }
} catch (Exception $e) {
    error_log('Gemini API Error: ' . $e->getMessage());
    $response = "Oops! An error occurred while processing your request.";
}

echo json_encode([
    'content' => $response
]);
?>
