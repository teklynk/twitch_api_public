<?php
require_once(__DIR__ . '/../config/config.php');

$limit = trim($_GET['limit']);
$after = trim($_GET['after']);
$before = trim($_GET['before']);

if (!empty($after)) {
    $afterVar = "&after=" . $after;
    $beforeVar = "";
} else {
    $afterVar = "";
}

if (!empty($before)) {
    $beforeVar = "&before=" . $before;
    $afterVar = "";
} else {
    $beforeVar = "";
}

if ($limit > 100 ) {
    $limit = 100;
}

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

//Get user followers
curl_setopt($ch, CURLOPT_URL, "https://api.twitch.tv/helix/users/follows?first=" . trim(strtolower($limit)) . "&to_id=" . $userResult['data'][0]['id'] . $afterVar . $beforeVar);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$userResponse = curl_exec($ch);

header('Content-type: application/json');

echo $userResponse;

curl_close($ch);
?>