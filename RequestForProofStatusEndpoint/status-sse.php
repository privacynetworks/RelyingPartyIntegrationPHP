<?php

// Disclaimer: the code below should not be used in production as is, and is only for demonstration purposes.
// It is important to implement proper security measures, error handling, and validation in a production environment.

// Allow all origins (change '*' to a specific origin if you want to restrict it)
header("Access-Control-Allow-Origin: *");

// Allow specific methods (GET, POST, etc.)
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

// Allow specific headers (if needed, you can modify the headers you want to allow)
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight requests (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    // Respond with a 200 OK status for preflight requests
    http_response_code(200);
    exit();
}
?>
<?php
// Start the session to access session variables
session_start();

// This script streams updates to the client
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');

// Retrieve the SID from the session
if (isset($_SESSION['sid']) || $_GET['sid']) {
    $sid = $_SESSION['sid'] ?? $_GET['sid'];

    $response = false;

    // Check if you have received a proof of age token from AgeAware
    // If you  have return true, otherwise return false
    if("has received proof of age token from AgeAware") {
        // Send the response as JSON
        $response = true; // Simulate that the proof of age token has been received
    }

    // Send the SSE message
    echo "data: " . json_encode(["success" => $response]) . "\n\n";
    flush();
} else {
    // If the SID is not found in the session, return an error message
    http_response_code(400);
    echo "data: " . json_encode(["success" => false]) . "\n\n";
    flush();
}
?>