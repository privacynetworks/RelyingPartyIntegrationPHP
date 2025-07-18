<?php

// The code below has been created to provide some examples for receiving and authenticating a proof of age token in PHP.

// Disclaimer: the code below should not be used in production as is, and is only for demonstration purposes.
// It is important to implement proper security measures, error handling, and validation in a production environment.

// Include the Composer autoload file for the jwt library
require_once '../vendor/autoload.php';

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;
use \Firebase\JWT\JWK;

// Check if data has been posted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the raw POST data (should be in JSON format)
    $rawData = file_get_contents('php://input');
    
    // Decode the raw POST data to extract the JWT
    $data = json_decode($rawData, true);

    if (isset($_SERVER['CONTENT_LENGTH']) && $_SERVER['CONTENT_LENGTH'] > 0) {
        // Get the raw POST body content
        $rawBody = file_get_contents('php://input');
        error_log("Body received: $rawBody");
    } else {
        error_log("No body received.");
    }

    if (isset($data['verificationPackage']['vp']['token'])) {
        try {

            // Extract the JWT token from the data
            $jwt = $data['verificationPackage']['vp']['token'];

            // Step 1: Get the 'kid' (Key ID) from the JWT header
            $jwtParts = explode('.', $jwt);
            $header = json_decode(base64_decode($jwtParts[0]), true);
            $payload = json_decode(base64_decode($jwtParts[1]), true);
            $kid = $header['kid']; // Key ID
            $iss = $payload['iss']; // Issuer

            // Step 2: Fetch the JWKS from the endpoint (replace with your actual JWKS URL)
            $jwksUrl = 'https://service.privacynetworks.io/i/'.$iss.'/pk/'.$kid;

            $jwks = json_decode(file_get_contents($jwksUrl), true);

            /**
             * You can add a leeway to account for when there is a clock skew times between
             * the signing and verifying servers. It is recommended that this leeway should
             * not be bigger than a few minutes.
             *
             * Source: http://self-issued.info/docs/draft-ietf-oauth-json-web-token.html#nbfDef
             */
            JWT::$leeway = 60; // $leeway in seconds

            // Decode the JWT token (no verification for now, adjust for your use case)
            $decoded = JWT::decode($data['verificationPackage']['vp']['token'], JWK::parseKeySet($jwks));

            // Extract the 'sid' (your session id reference) from the decoded payload
            $sid = $data['requestToken']['verifiedToken']['payload']['s'];

            if($decoded->error_code) {
                http_response_code(400);
                echo json_encode(["message" => "User not verified."]);
                exit;
            }

            // If the JWT is valid, you can now use the session ID (sid) to manage the user's session
            // As we have now authenticated the token and the user is verified, you can proceed with your application logic
            // For example, you can update the session in your sesssion storage or db for the user to allow access to protected resources



        } catch (Exception $e) {
            // Handle JWT decoding error
            http_response_code(400);
            echo json_encode(["message" => "Invalid token. Error: " . $e->getMessage()]);
        }
    } else {
        // If no token is found in the POST data
        http_response_code(400);
        echo json_encode(["message" => "Token not provided."]);
    }
} else {
    // If not a POST request, return an error
    http_response_code(405);
    echo json_encode(["message" => "Method Not Allowed"]);
}
?>