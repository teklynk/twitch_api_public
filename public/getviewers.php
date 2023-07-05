<?php
require_once(__DIR__ . '/../config/config.php');

$headers = [
    'Authorization: Bearer ' . AUTH_TOKEN,
    'Client-Id: ' . CLIENT_ID
];

// This will soon be depricated and will no longer work
if (isset($_GET['channel'])) {

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "https://tmi.twitch.tv/group/user/" . trim($_GET['channel']) . "/chatters");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $viewerInfo = curl_exec($ch);
    $userStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    header('Content-type: application/json');

    echo $viewerInfo;

    curl_close($ch);
}
?>
