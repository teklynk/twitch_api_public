<?php
require_once(__DIR__ . '/../config/config.php');

$ch = curl_init();

$headers = [
    'Authorization: Bearer ' . AUTH_TOKEN,
    'Client-Id: ' . CLIENT_ID
];

curl_setopt($ch, CURLOPT_URL, "https://tmi.twitch.tv/group/user/" . trim($_GET['channel']) . "/chatters");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$viewerInfo = curl_exec($ch);

header('Content-type: application/json');

echo $viewerInfo;

curl_close($ch);
?>
