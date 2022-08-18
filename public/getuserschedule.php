<?php
require_once(__DIR__ . '/../config/config.php');

$ical = trim($_GET['ical']);

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

if (!empty($ical) && $ical == "true") {

    //Get user ical data
    curl_setopt($ch, CURLOPT_URL, "https://api.twitch.tv/helix/schedule/icalendar?broadcaster_id=" . $userResult['data'][0]['id']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $userIcalResponse = curl_exec($ch);

    header('Content-type: text/calendar');

    echo $userIcalResponse;

} else {

    //Get user schedule
    curl_setopt($ch, CURLOPT_URL, "https://api.twitch.tv/helix/schedule?broadcaster_id=" . $userResult['data'][0]['id']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $userResponse = curl_exec($ch);

    header('Content-type: application/json');

    echo $userResponse;
}

curl_close($ch);
?>