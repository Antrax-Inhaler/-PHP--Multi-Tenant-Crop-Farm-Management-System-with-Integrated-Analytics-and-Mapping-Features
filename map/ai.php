<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('../gemini_ai/vendor/autoload.php');

use GeminiAPI\Client;
use GeminiAPI\Resources\Parts\TextPart;

// Initialize Gemini API client
$client = new Client("AIzaSyA9_Am1Lj-c_LRc46iWn9z5uCyjlvnZDXw");

// Get message from POST data
$message = $_POST['message'] ?? '';

// Generate response from AI for user's input
$response = generateAIResponse($client, $message);

// Output JSON response
echo json_encode([
    'content' => $response
]);

function generateAIResponse($client, $message) {
    try {
        // Generate content from Gemini API
        $aiResponse = $client->geminiPro()->generateContent(new TextPart($message))->text();
        
        // Validate AI response before using it
        if (!empty($aiResponse)) {
            return $aiResponse;
        } else {
            return "I'm sorry, I couldn't understand your question.";
        }
    } catch (Exception $e) {
        // Log error (optional)
        error_log('Gemini API Error: ' . $e->getMessage());
        return "Oops! An error occurred while processing your request.";
    }
}
?>
