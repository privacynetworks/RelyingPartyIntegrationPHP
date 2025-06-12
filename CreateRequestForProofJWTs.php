<?php

// The code below has been created to provide some examples for creating a request for a proof of age token in PHP.

// Disclaimer: the code below should not be used in production as is, and is only for demonstration purposes.
// It is important to implement proper security measures, error handling, and validation in a production environment.

// Include the Composer autoload file for the jwt library
require_once '../vendor/autoload.php';

use \Firebase\JWT\JWT;

// This is the RPID (Relying Party ID) that you will use in your SD-JWT request
$__RPID__ = "22ca36cd-cce7-41dd-95c3-0488bc820c34"; // Replace with your actual RPID
$__AgeAwareDomain__ = "https://ageaware.privacynetworks.io/"; 

function randomlySelectPrivateKey()
{   
    // In a real-world scenario, you would load your private keys from a secure storage
    // or configuration file. Here we just return a hardcoded private key for demonstration.
    // Here we just return the single private key for simplicity
    return [
        'kid'        => 'd4cb0b6c-7196-4986-82a7-bb37a7c41cfa',
        'private_key' => <<<EOD
    -----BEGIN EC PRIVATE KEY-----
    MHcCAQEEID1yTiIjTmgydvlY4hYL7N3ZMPz5xGBH6/90S+se6bxwoAoGCCqGSM49
    AwEHoUQDQgAE01Vto9BFADrNb+mSeyxneHk1y9Na0fR9EcSpXTpACiwnpDlnvHVh
    hTGvtol9t9nsY6zoAyKS4P07tQE4hRRG8w==
    -----END EC PRIVATE KEY-----
    EOD
    ];

    // You should have submitted multiple private keys to AgeAware to choose from

    // Load available private keys (from secure storage or configuration)
    $keys = [
        [
            'kid'        => 'd4cb0b6c-7196-4986-82a7-bb37a7c41cfa',
            'private_key' => <<<EOD
            ...
            EOD
        ],
        [
            'kid'        => '5e2d759b-033b-4f0f-9219-490ed450c946',
            'private_key' => <<<EOD
            ...
            EOD
        ],
    ];

    // Randomly select one of the keys
    $randomIndex = array_rand($keys);
    $selectedKey = $keys[$randomIndex];

    return $selectedKey;
}

// Generate a random salt for the claim
function generateSalt()
{
    return 'ben';
    return bin2hex(random_bytes(16));
}

// Hash the claim using SHA-512 and encode it in base64url format
function hashClaim($claim)
{
    $claimJson = json_encode($claim);
    $hash = hash('sha512', $claimJson, true);
    return rtrim(strtr(base64_encode($hash), '+/', '-_'), '=');
}

// Encode data to Base64URL format
function base64UrlEncode(string $data): string {
    // Encode data to standard Base64
    $base64 = base64_encode($data);

    // Convert Base64 to Base64URL by replacing '+' with '-', '/' with '_', and removing '=' padding
    return str_replace(['+', '/', '='], ['-', '_', ''], $base64);
}

// IMPORTANT - create random session id in this format aa4444b2-21ca-4321-95cc-85515099112d - UUIDv4
function generateSessionId()
{
    // This is used to the request coming back to you website or service so that you can update the user's session
    // return a random UUIDv4 as an example
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex(random_bytes(16)), 4));

    // In a real-world scenario, you would use a more secure method to generate the session ID
    // For example, you could use a library or framework function to generate a UUIDv4
    // Which you can youse to track the user's session

}

// Create the redirect url with the signed token in querystring
function createRequestForProofToken() {

    $claims = [
        [generateSalt(), "rpid", $__RPID__]
    ];

    // Define the payload
    $payload = [
        's' => generateSessionId(),  // Selective Disclosure Session ID
        't' => 0, // This references the proof type required from your AgeAware configuration
        'iat' => time(),
        "_sd" => [hashClaim($claims[0])],
    ];

    // Create signed SD-JWT token
    $jwt = JWT::encode($payload, randomlySelectPrivateKey()['private_key'], 'ES256', null, $header);

    // Claims to append to the token value
    $claimsPayload = '.'. base64UrlEncode(json_encode($claims));

    $tokenUrl = $__AgeAwareDomain__ . "?token=" . $jwt . $claimsPayload . "#v/qr-code";

    return $tokenUrl;

}