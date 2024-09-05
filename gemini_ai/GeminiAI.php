<?php
// GeminiAI.php

require "vendor/autoload.php"; // Adjust as needed

use GeminiAPI\Client;
use GeminiAPI\Resources\Parts\TextPart;

class GeminiAI {
    private $client;

    public function __construct($apiKey) {
        $this->client = new Client($apiKey);
    }

    public function generateResponse($text) {
        try {
            $response = $this->client->geminiPro()->generateContent(new TextPart($text))->text();
            return $response;
        } catch (Exception $e) {
            error_log("Gemini API Error: " . $e->getMessage());
            return "Oops! Something went wrong while generating response.";
        }
    }
}
?>
