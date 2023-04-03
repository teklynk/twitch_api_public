<?php
require_once(__DIR__ . '/../config/config.php');

$ItemsArray = array();
$combindArr = array();

$ch = curl_init();

$headers = [
    'Authorization: Bearer ' . AUTH_TOKEN,
    'Client-Id: ' . CLIENT_ID
];

curl_setopt($ch, CURLOPT_URL, "https://api.twitch.tv/helix/users?login=" . trim(strtolower(str_replace('@', '', $_GET['channel']))));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$userInfo = curl_exec($ch);
$userResult = json_decode($userInfo, true);

//Get user 7tv emotes
curl_setopt($ch, CURLOPT_URL, "https://api.7tv.app/v2/users/" . $userResult['data'][0]['id'] . "/emotes");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$userResponse = curl_exec($ch);

//Get global 7tv emotes
curl_setopt($ch, CURLOPT_URL, "https://api.7tv.app/v2/emotes/global");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$globalResponse = curl_exec($ch);

//user 7tvemotes data
$userData = json_decode($userResponse, true);

//global 7tvemotes data
$globalData = json_decode($globalResponse, true);

//combine all 7tvemotes into one array
$combindArr = array_merge($userData, $globalData);

foreach ($combindArr as $data) {
    $ItemsArray[] = array(
        "id" => $data['id'],
        "code" => $data['name']
    );
}

header('Content-type: application/json');

echo json_encode($ItemsArray);

curl_close($ch);
?>