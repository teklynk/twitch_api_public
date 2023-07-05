<?php
require_once(__DIR__ . '/../config/config.php');

$headers = [
    'Authorization: Bearer ' . AUTH_TOKEN,
    'Client-Id: ' . CLIENT_ID
];

$ch = curl_init();

//Get user status
curl_setopt($ch, CURLOPT_URL, "https://api.twitch.tv/helix/chat/emotes/global");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$userResponse = curl_exec($ch);

header('Content-type: application/json');

echo $userResponse;

curl_close($ch);
?>