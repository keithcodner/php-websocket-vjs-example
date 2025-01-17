<?php
$mysqli = new mysqli("localhost", "root", "root", "websocket_example");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$mysqli->query("UPDATE counter SET value = value + 1");

$result = $mysqli->query("SELECT value FROM counter LIMIT 1");
$row = $result->fetch_assoc();
echo json_encode(["value" => $row['value']]);

$mysqli->close();
?>
