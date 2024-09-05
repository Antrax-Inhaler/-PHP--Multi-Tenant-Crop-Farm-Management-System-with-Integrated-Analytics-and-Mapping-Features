<?php
require "vendor/autoload.php"; // Include Gemini API client library

use GeminiAPI\Client;
use GeminiAPI\Resources\Parts\TextPart;

// Function to process AI request
function processAIRequest($input) {
    // Initialize Gemini API client with your API key
    $client = new Client("AIzaSyA9_Am1Lj-c_LRc46iWn9z5uCyjlvnZDXw");

    // Generate AI response
    $aiResponse = $client->geminiPro()->generateContent(new TextPart($input))->text();

    // Return AI response
    return $aiResponse;
}

// Main execution starts here
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user input
    $input = isset($_POST['user_input']) ? $_POST['user_input'] : '';

    // Process AI response
    $response = processAIRequest($input);

    // Return response as JSON
    header('Content-Type: application/json');
    echo json_encode(['response' => $response]);
}
?>
