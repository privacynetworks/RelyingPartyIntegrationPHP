<?php

// Disclaimer: the code below should not be used in production as is, and is only for demonstration purposes.
// It is important to implement proper security measures, error handling, and validation in a production environment.

// Include the Composer autoload file for the rachet or another library that provides support for websocket
require dirname(__DIR__) . '/vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class FileCheckWebSocket implements MessageComponentInterface {
    public function onOpen(ConnectionInterface $conn) {
        // Connection opened
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        // Start the session to access session variables
        session_start();

        // Retrieve the SID from the session
        if (isset($_SESSION['sid']) || $_GET['sid']) {
            $sid = $_SESSION['sid'] ?? $_GET['sid'];

            $response = false;

            // Check if you have received a proof of age token from AgeAware
            // If you  have return true, otherwise return false
            if("has received proof of age token from AgeAware") {
                $response = true; // Simulate that the proof of age token has been received
            }

            // Send the response back to the client
            $from->send(json_encode(["success" => $response]));
        } else {
            // If SID is not found in the session, send an error message
            $from->send(json_encode(["success" => false]));
        }
    }

    public function onClose(ConnectionInterface $conn) {
        // Connection closed
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        // Handle errors
    }
}

$server = IoServer::factory(new HttpServer(new WsServer(new FileCheckWebSocket())), 8080);
$server->run();