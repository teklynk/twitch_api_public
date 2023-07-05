<?php
require_once(__DIR__ . '/../config/config.php');

$headers = [
    'Authorization: Bearer ' . AUTH_TOKEN,
    'Client-Id: ' . CLIENT_ID
];

if (isset($_GET['id'])) {

    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, "https://api.twitch.tv/helix/games?id=" . trim($_GET['id']));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $userInfo = curl_exec($ch);

    header('Content-type: application/json');

    echo $userInfo;

    curl_close($ch);
}
?>
