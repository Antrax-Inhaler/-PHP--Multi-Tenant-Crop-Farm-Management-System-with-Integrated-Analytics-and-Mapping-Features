<?php
session_start(); // Start PHP session
require "vendor/autoload.php";
use GeminiAPI\Client;
use GeminiAPI\Resources\Parts\TextPart;

$data = json_decode(file_get_contents("php://input"));
$text = $data->text;

try {
    // Initialize Gemini API client
    $client = new Client("AIzaSyA9_Am1Lj-c_LRc46iWn9z5uCyjlvnZDXw");

    // Check if session data exists
    $cropData = isset($_SESSION['cropData']) ? $_SESSION['cropData'] : null;

    // If user asks for crop information
    if (strpos(strtolower($text), 'crop') !== false || strpos(strtolower($text), 'my crops') !== false) {
        if ($cropData) {
            $response = "Here is the information about your crops: " . json_encode($cropData);
        } else {
            $response = "I don't have that information right now.";
        }
    } else {
        // Generate response from AI for user's input
        $response = $client->geminiPro()->generateContent(new TextPart($text))->text();

        // Extract and store crop data if mentioned in the response
        $extractedCropData = extractCropData($response);
        if ($extractedCropData) {
            $_SESSION['cropData'] = $extractedCropData;
        }
    }

    // Output JSON response
    echo json_encode([
        'role' => 'assistant',
        'content' => $response
    ]);
} catch (Exception $e) {
    // Handle any exceptions or errors
    http_response_code(500); // Internal Server Error
    echo json_encode([
        'role' => 'assistant',
        'content' => 'Oops! An error occurred while processing your request.'
    ]);
}

function extractCropData($response) {
    // Example function to extract crop data from response
    // You can customize this based on your actual response structure
    $cropData = null;
    // Example: Assuming response contains JSON data
    $pattern = '/```([\s\S]+?)```/';
    if (preg_match($pattern, $response, $matches)) {
        $cropData = json_decode(trim($matches[1]), true);
    }
    return $cropData;
}
?>
