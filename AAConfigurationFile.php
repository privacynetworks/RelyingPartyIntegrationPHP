<?php

// Disclaimer: the code below should not be used in production as is, and is only for demonstration purposes.
// It is important to implement proper security measures, error handling, and validation in a production environment.

// Set headers and add configuration token

// Allow all origins (change '*' to a specific origin if you want to restrict it)
header("Access-Control-Allow-Origin: https://ageaware.privacynetworks.io");

// Allow specific methods (GET, POST, etc.)
header("Access-Control-Allow-Methods: GET, OPTIONS");

// Allow specific headers (if needed, you can modify the headers you want to allow)
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight requests (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    // Respond with a 200 OK status for preflight requests
    http_response_code(200);
    exit();
}

// Add the configuration token here
?>eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCIsI...