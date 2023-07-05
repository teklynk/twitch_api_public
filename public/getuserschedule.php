<?php
require_once(__DIR__ . '/../config/config.php');

$ical = isset($_GET['ical']) ? $_GET['ical'] : '';
$html = isset($_GET['html']) ? $_GET['html'] : '';
$limit = isset($_GET['limit']) ? $_GET['limit'] : 100;
$userHtmlResponseArray1['data']['segments'] = array();
$userHtmlResponseArray2['data']['segments'] = array();
$userHtmlResponseArray3['data']['segments'] = array();
$userHtmlResponseArray4['data']['segments'] = array();
$userHtmlResponseArray5['data']['segments'] = array();
$userEventsArray = array();
$userHtmlResponse = array();

if ($limit > 100) {
    $limit = 100;
}

$headers = [
    'Authorization: Bearer ' . AUTH_TOKEN,
    'Client-Id: ' . CLIENT_ID
];

if (isset($_GET['channel'])) {

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "https://api.twitch.tv/helix/users?login=" . trim(strtolower(str_replace('@', '', $_GET['channel']))));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $userInfo = curl_exec($ch);
    $userStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $userResult = json_decode($userInfo, true);

    if (isset($ical) && $ical == "true" && $userStatus == 200 && count($userResult['data']) > 0) {

        // Get user ical data
        curl_setopt($ch, CURLOPT_URL, "https://api.twitch.tv/helix/schedule/icalendar?broadcaster_id=" . $userResult['data'][0]['id']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $userIcalResponse = curl_exec($ch);

        header('Content-type: text/calendar');

        echo $userIcalResponse;

    } elseif (isset($html) && $html == "true" && $userStatus == 200 && count($userResult['data']) > 0) {

        // Get user schedule as html
        curl_setopt($ch, CURLOPT_URL, "https://api.twitch.tv/helix/schedule?broadcaster_id=" . $userResult['data'][0]['id']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $userHtmlResponse1 = curl_exec($ch);
        $userHtmlResponseArray1 = json_decode($userHtmlResponse1, true);

        if (is_array($userHtmlResponseArray1['pagination']) && count($userHtmlResponseArray1['data']['segments']) > 0 && $userHtmlResponseArray1['pagination']['cursor'] > "") {
            // Gets the next 20 items from schedule
            curl_setopt($ch, CURLOPT_URL, "https://api.twitch.tv/helix/schedule?broadcaster_id=" . $userResult['data'][0]['id'] . "&after=" . $userHtmlResponseArray1['pagination']['cursor']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $userHtmlResponse2 = curl_exec($ch);
            $userHtmlResponseArray2 = json_decode($userHtmlResponse2, true);
        }

        if (is_array($userHtmlResponseArray2['pagination']) && count($userHtmlResponseArray2['data']['segments']) > 0 && $userHtmlResponseArray2['pagination']['cursor'] > "") {
            // Gets the next 20 items from schedule
            curl_setopt($ch, CURLOPT_URL, "https://api.twitch.tv/helix/schedule?broadcaster_id=" . $userResult['data'][0]['id'] . "&after=" . $userHtmlResponseArray2['pagination']['cursor']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $userHtmlResponse3 = curl_exec($ch);
            $userHtmlResponseArray3 = json_decode($userHtmlResponse3, true);
        }

        if (is_array($userHtmlResponseArray3['pagination']) && count($userHtmlResponseArray3['data']['segments']) > 0 && $userHtmlResponseArray3['pagination']['cursor'] > "") {
            // Gets the next 20 items from schedule
            curl_setopt($ch, CURLOPT_URL, "https://api.twitch.tv/helix/schedule?broadcaster_id=" . $userResult['data'][0]['id'] . "&after=" . $userHtmlResponseArray3['pagination']['cursor']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $userHtmlResponse4 = curl_exec($ch);
            $userHtmlResponseArray4 = json_decode($userHtmlResponse4, true);
        }

        if (is_array($userHtmlResponseArray4['pagination']) && count($userHtmlResponseArray4['data']['segments']) > 0 && $userHtmlResponseArray4['pagination']['cursor'] > "") {
            // Gets the next 20 items from schedule
            curl_setopt($ch, CURLOPT_URL, "https://api.twitch.tv/helix/schedule?broadcaster_id=" . $userResult['data'][0]['id'] . "&after=" . $userHtmlResponseArray4['pagination']['cursor']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $userHtmlResponse5 = curl_exec($ch);
            $userHtmlResponseArray5 = json_decode($userHtmlResponse5, true);
        }

        // merge all array data into one array
        $userHtmlResponse = array_merge(
            $userHtmlResponseArray1['data']['segments'], 
            $userHtmlResponseArray2['data']['segments'], 
            $userHtmlResponseArray3['data']['segments'], 
            $userHtmlResponseArray4['data']['segments'],
            $userHtmlResponseArray5['data']['segments']
        );
        
        $userEventsArray = $userHtmlResponse;

        $htmlEventContents = "";

        $cnt = 0;

        if (is_array($userEventsArray) && count($userEventsArray) > 0) {
            foreach ($userEventsArray as $event) {

                $cnt++;
        
                $title = trim($event['title']);
                $start = trim($event['start_time']);
        
                if (!empty($title)) {
                    $htmlEventContents .= "<div class='event item_" . $cnt . "'>";
                    $htmlEventContents .= "<span class='start_date'>" . $start . "</span>";
                    $htmlEventContents .= "<span class='title'>" . $title . "</span><br>";
                    $htmlEventContents .= "</div>";
                }
        
                if ($cnt == $limit) {
                    break;
                }
                
            }
        } else {
            // exit - do nothing else
            die();
        }

        // print html page
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

        // Get user schedule
        curl_setopt($ch, CURLOPT_URL, "https://api.twitch.tv/helix/schedule?broadcaster_id=" . $userResult['data'][0]['id']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $userResponse = curl_exec($ch);

        header('Content-type: application/json');

        echo $userResponse;
    }

    curl_close($ch);
}
?>