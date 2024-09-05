<?php
require_once 'gemini_client.php';
require_once 'error_handler.php';

function handleUserRequest($input) {
    try {
        // Initialize Gemini API client
        $geminiClient = new GeminiClient("AIzaSyA9_Am1Lj-c_LRc46iWn9z5uCyjlvnZDXw");

        // Example: Generating AI response
        $response = $geminiClient->generateResponse($input);

        return [
            'role' => 'assistant',
            'content' => $response
        ];
    } catch (Exception $e) {
        logError($e->getMessage());

        return [
            'role' => 'assistant',
            'content' => 'Oops! An error occurred while processing your request.'
        ];
    }
}
?>
