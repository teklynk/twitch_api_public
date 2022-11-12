<?php
require_once(__DIR__ . '/../config/config.php');

$ItemsArray = array();
$combindArr = array();

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

//Get user bttv emotes
curl_setopt($ch, CURLOPT_URL, "https://api.betterttv.net/3/cached/users/twitch/" . $userResult['data'][0]['id']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $bttvHeaders);
$userResponse = curl_exec($ch);

//Get global bttv emotes
curl_setopt($ch, CURLOPT_URL, "https://api.betterttv.net/3/cached/emotes/global");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $bttvHeaders);
$globalResponse = curl_exec($ch);

//user bttvemotes data
$userData = json_decode($userResponse, true);

//global bttvemotes data
$globalData = json_decode($globalResponse, true);

//combine all bttvemotes into one array
$combindArr = array_merge($userData['channelEmotes'], $userData['sharedEmotes'], $globalData);

foreach ($combindArr as $data) {
    $ItemsArray[] = array(
        "id" => $data['id'],
        "code" => $data['code']
    );
}

header('Content-type: application/json');

echo json_encode($ItemsArray);

curl_close($ch);
?>