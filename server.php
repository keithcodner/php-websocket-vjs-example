<?php
$host = '127.0.0.1';
$port = 80;

// Create WebSocket server
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);
socket_bind($socket, $host, $port);
socket_listen($socket);

$clients = [];

// Function to perform WebSocket handshake
function handshake($clientSocket) {
    $headers = socket_read($clientSocket, 1024);
    if (preg_match("/Sec-WebSocket-Key: (.*)\r\n/", $headers, $matches)) {
        $key = base64_encode(pack(
            'H*',
            sha1($matches[1] . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')
        ));
        $upgradeHeader = "HTTP/1.1 101 Switching Protocols\r\n" .
                         "Upgrade: websocket\r\n" .
                         "Connection: Upgrade\r\n" .
                         "Sec-WebSocket-Accept: $key\r\n\r\n";
        socket_write($clientSocket, $upgradeHeader);
    }
}

// Function to decode WebSocket frame
function decodeFrame($data) {
    $payloadLength = ord($data[1]) & 127;

    // Determine the start index of the payload and masking key
    if ($payloadLength === 126) {
        $maskStart = 4; // Extended payload length (16-bit)
    } elseif ($payloadLength === 127) {
        $maskStart = 10; // Extended payload length (64-bit)
    } else {
        $maskStart = 2; // Normal payload length
    }

    $mask = substr($data, $maskStart, 4); // Extract the masking key
    $payload = substr($data, $maskStart + 4); // Extract the payload

    // Ensure the lengths match before applying the mask
    if (strlen($payload) > 0 && strlen($mask) === 4) {
        $decoded = '';
        for ($i = 0; $i < strlen($payload); $i++) {
            $decoded .= $payload[$i] ^ $mask[$i % 4]; // Apply mask to payload
        }
        return $decoded;
    }

    return ''; // Return an empty string if payload or mask is invalid
}


// Function to encode WebSocket frame
function encodeFrame($data) {
    $bytes = [0b10000001, strlen($data)];
    return implode('', array_map("chr", $bytes)) . $data;
}

echo "WebSocket server started on ws://$host:$port\n";

while (true) {
    $read = $clients;
    $read[] = $socket;
    socket_select($read, $write, $except, 0);

    if (in_array($socket, $read)) {
        $client = socket_accept($socket);
        $clients[] = $client;
        handshake($client);
        echo "New client connected\n";
    }

    foreach ($clients as $key => $client) {
        if (in_array($client, $read)) {
            $data = @socket_read($client, 1024, PHP_BINARY_READ);
            if (!$data) {
                unset($clients[$key]);
                socket_close($client);
                echo "Client disconnected\n";
                continue;
            }

            $message = decodeFrame($data);
            foreach ($clients as $otherClient) {
                if ($otherClient !== $client) {
                    socket_write($otherClient, encodeFrame($message));
                }
            }
        }
    }
}
?>
