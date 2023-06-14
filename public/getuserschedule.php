<?php
require_once(__DIR__ . '/../config/config.php');

$ical = trim($_GET['ical']);
$html = trim($_GET['html']);

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

} elseif (!empty($html) && $html == "true") {

    //Get user schedule as html
    curl_setopt($ch, CURLOPT_URL, "https://api.twitch.tv/helix/schedule?broadcaster_id=" . $userResult['data'][0]['id']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $userHtmlResponse = curl_exec($ch);

    $userHtmlResponse = json_decode($userHtmlResponse, true);

    $userEventsArray = $userHtmlResponse['data']['segments'];

    $htmlEventContents = "";

    $cnt = 0;

    foreach ($userEventsArray as $event) {

        $cnt++;

        $title = trim($event['title']);
        $start = trim($event['start_time']);
   
        if (!empty($title)) {
            $htmlEventContents .= "<div class='event item_" . $cnt . "'>";
            $htmlEventContents .= "<span class='start_date'>" . $start . "</span>";
            $htmlEventContents .= "<span class='title'>" . $title . "</span>";
            $htmlEventContents .= "</div>";
        }
        
    }

    header('Content-type: text/html');

    echo "<!DOCTYPE html>";
    echo "<html>";
    echo "<head>";
    echo "<meta charset='UTF-8'>";
    echo "<link rel='stylesheet' href='/assets/styles.css'>";
    echo "</head>";
    echo "<body>";
    echo "<div id='container'>";

    echo $htmlEventContents;

    echo "</div>";
    echo "<script src='/assets/functions.js'></script>";
    echo "</body>";
    echo "</html>";

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