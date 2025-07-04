<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('gemini_ai/vendor/autoload.php');

use GeminiAPI\Client;
use GeminiAPI\Resources\Parts\TextPart;
use GeminiAPI\Resources\Parts\FilePart;

// Initialize Gemini API client
$client = new Client("AIzaSyA9_Am1Lj-c_LRc46iWn9z5uCyjlvnZDXw");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if an image or message is submitted
    $message = $_POST['message'] ?? '';
    $image = $_FILES['image'] ?? null;

    // If an image is uploaded
    if ($image && $image['tmp_name']) {
        $imageResponse = processImage($client, $image);
        echo json_encode(['content' => $imageResponse]);
    } else {
        // If only a message is sent
        $response = generateAIResponse($client, $message);
        echo json_encode(['content' => $response]);
    }
}

function generateAIResponse($client, $message) {
    try {
        $aiResponse = $client->geminiPro()->generateContent(new TextPart($message))->text();
        return $aiResponse ?: "I'm sorry, I couldn't understand your question.";
    } catch (Exception $e) {
        error_log('Gemini API Error: ' . $e->getMessage());
        return "Oops! An error occurred while processing your request.";
    }
}

function processImage($client, $image) {
    try {
        // Upload image to Gemini API
        $filePart = new FilePart(file_get_contents($image['tmp_name']), $image['type']);
        $uploadResponse = $client->geminiPro()->uploadFile($filePart);
        
        // Prompt to generate content based on image
        $aiResponse = $client->geminiPro()->generateContent(
            [new TextPart("Describe this image."), $uploadResponse->filePart()]
        )->text();

        return $aiResponse ?: "I'm sorry, I couldn't analyze the image.";
    } catch (Exception $e) {
        error_log('Gemini API Error: ' . $e->getMessage());
        return "Oops! An error occurred while processing the image.";
    }
}
?>
