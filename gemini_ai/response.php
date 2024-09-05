<?php
// response.php

session_start();
require "GeminiAI.php"; // Adjust path as needed

// Replace with your actual Gemini API key
$apiKey = "AIzaSyA9_Am1Lj-c_LRc46iWn9z5uCyjlvnZDXw";
$geminiAI = new GeminiAI($apiKey);

// Handle incoming POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get input text from POST data
    $input = file_get_contents("php://input");
    $data = json_decode($input);

    // Check if JSON decoding failed
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400); // Bad Request
        echo json_encode([
            'error' => 'Invalid JSON input.'
        ]);
        exit();
    }

    // Extract text from the decoded data
    $text = $data->text ?? '';

    // Generate AI response
    $response = $geminiAI->generateResponse($text);

    // Output JSON response
    header('Content-Type: application/json');
    echo json_encode([
        'response' => $response
    ]);
}
?>
