<?php

// The code below has been created to provide some examples for creating a request for a proof of age token in PHP.

// Disclaimer: the code below should not be used in production as is, and is only for demonstration purposes.
// It is important to implement proper security measures, error handling, and validation in a production environment.

// Create key pairs and store them
// We would suggest that you run this on a schedule so that create a new series of key pairs every week
// It is very important that these private keys are store securely as they will be used to sign your request for proofs
// You must never send expose your private keys to anyone. If your keys or systems are compromised we would recommend that you generate
// a new series of key pairs and upload them to AgeAware.
// Once your new public keys are uploaded to AgeAware, you can start using them to sign your requests for proofs.
// It is important to disable your old keys in AgeAware so that they can no longer be used to sign requests.

// Generate a random UUID v4 string compliant with RFC 4122
function generate_uuid_v4(): string
{
    // Generate 16 bytes (128 bits) of random data
    $data = random_bytes(16);

    // Set the version to 0100 (version 4)
    $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);

    // Set the variant to 10xx
    $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);

    // Output the 36-character UUID.
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

// Create key pairs and store them
function createKeyPairs($quantity = 100) {

    // Configuration: specify EC and the named curve
    $config = [
        'private_key_type' => OPENSSL_KEYTYPE_EC,
        'curve_name'       => 'prime256v1',  // also known as secp256r1
    ];

    $keyPairsArray = [];
    
    while ($quantity > 0) {

        // 2. Generate the keypair
        $resource = openssl_pkey_new($config);
        if ($resource === false) {
            die('Failed to generate EC key pair: ' . openssl_error_string());
        }

        // 3. Extract the private key to a string
        if (!openssl_pkey_export($resource, $privateKeyPem)) {
            die('Failed to export private key: ' . openssl_error_string());
        }

        // 4. Extract the public key details and grab the PEM string
        $keyDetails = openssl_pkey_get_details($resource);
        if (!isset($keyDetails['key'])) {
            die('Failed to get public key details');
        }
        $publicKeyPem = $keyDetails['key'];

        $keyPairsArray[] = [
            'kid' => generate_uuid_v4(), // Generate a random UUID v4 for the key ID
            'private_key' => $privateKeyPem,
            'public_key'  => $publicKeyPem,
        ];


        // // 5. (Optional) Save them to disk
        // file_put_contents("ec_private_$quantity.pem", $privateKeyPem);
        // file_put_contents("ec_public_$quantity.pem",  $publicKeyPem);

        // // 6. Output to screen
        // echo "=== EC PRIVATE KEY ===\n", $privateKeyPem, "\n";
        // echo "=== EC PUBLIC KEY ===\n",  $publicKeyPem,  "\n";

        $quantity--;
    }

    return $keyPairsArray;
}

// Get public keys and kid from the key pairs
function getPublicKeys($keyPairs) {
    $publicKeys = [];
    foreach ($keyPairs as $keyPair) {
        $publicKeys[] = [
            'kid' => $keyPair['kid'],
            'pem' => $keyPair['public_key'],
        ];
    }
    return $publicKeys;
}

// Get private keys and kid from the key pairs
function getPrivateKeys($keyPairs) {
    $privateKeys = [];
    foreach ($keyPairs as $keyPair) {
        $privateKeys[] = [
            'kid' => $keyPair['kid'],
            'private_key' => $keyPair['private_key'],
        ];
    }
    return $privateKeys;
}

// Save private keys to a secure storage or database
function savePrivateKeys($keyPairs) {
    // This function should implement secure storage of private keys, such as a database or secure file storage.
    // For demonstration purposes, we will just return the private keys and kids.
    return getPrivateKeys($keyPairs);
    
    // Save to a secure location, e.g., database or encrypted file
}

// Upload public keys to AgeAware endpoint
function uploadPublicKeys($keyPairs) {
    // This function should implement the logic to upload public keys to the AgeAware endpoint.
    // For demonstration purposes, we will just return the public keys and kids.
    return getPublicKeys($keyPairs);

    // You can use cURL or any HTTP client to send the public keys to the AgeAware endpoint.
}

$keyPairsData = createKeyPairs(100); // Create 100 key pairs
$publicKeys = uploadPublicKeys($keyPairsData); // Upload public keys to AgeAware
$privateKeys = savePrivateKeys($keyPairsData); // Save private keys securely