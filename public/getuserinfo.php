<?php
require_once(__DIR__ . '/../config/config.php');

$headers = [
    'Authorization: Bearer ' . AUTH_TOKEN,
    'Client-Id: ' . CLIENT_ID
];

if (isset($_GET['channel'])) {

    $ch = curl_init();

    //Get user id and info
    curl_setopt($ch, CURLOPT_URL, "https://api.twitch.tv/helix/users?login=" . trim(strtolower(str_replace('@', '', $_GET['channel']))));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $userResponse = curl_exec($ch);
    $userStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    header('Content-type: application/json');

    echo $userResponse;

    curl_close($ch);
}
?>