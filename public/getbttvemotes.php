<?php
require_once(__DIR__ . '/../config/config.php');

$ch = curl_init();

$headers = [
    'Authorization: Bearer ' . AUTH_TOKEN,
    'Client-Id: ' . CLIENT_ID
];

$bttvHeaders = [
    'content-type: application/json; charset=utf-8',
    'user-agent:"vscode-client"',
];

curl_setopt($ch, CURLOPT_URL, "https://api.twitch.tv/helix/users?login=" . trim(strtolower(str_replace('@', '', $_GET['channel']))));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$userInfo = curl_exec($ch);
$userResult = json_decode($userInfo, true);

//Get user status
curl_setopt($ch, CURLOPT_URL, "https://api.betterttv.net/3/cached/users/twitch/" . $userResult['data'][0]['id']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $bttvHeaders);
$userResponse = curl_exec($ch);

header('Content-type: application/json');

echo $userResponse;

curl_close($ch);
?>