<?php
require_once(__DIR__ . '/../config/config.php');

use GuzzleHttp\Client;

$client = new Client();

$headers = [
    'Authorization' => 'Bearer ' . AUTH_TOKEN,
    'Client-Id' => getenv('API_TWITCH_CLIENT_ID')
];

$ItemsArray = [];
$ical = isset($_GET['ical']) ? $_GET['ical'] : false;
$html = isset($_GET['html']) ? $_GET['html'] : false;

if (isset($_GET['channel']) || isset($_GET['id'])) {
    try {
        
        // Determine the API endpoint for the user info
        if (isset($_GET['channel'])) {
            $url = "https://api.twitch.tv/helix/users?login=" . trim(strtolower(str_replace('@', '', $_GET['channel'])));
        } elseif (isset($_GET['id'])) {
            $url = "https://api.twitch.tv/helix/users?id=" . trim($_GET['id']);
        } else {
            throw new Exception("Invalid parameters. Provide either 'channel' or 'id'.");
        }

        $response = $client->request('GET', $url, [
            'headers' => $headers
        ]);

        $userResult = json_decode($response->getBody(), true);
        $userStatus = $response->getStatusCode();

        if ($userStatus == 200 && count($userResult['data']) > 0) {
            $broadcasterId = $userResult['data'][0]['id'];
            $url = "https://api.twitch.tv/helix/schedule?broadcaster_id=" . $broadcasterId;
            $pagination = true;

            while ($pagination) {
                $response = $client->request('GET', $url, [
                    'headers' => $headers
                ]);

                $scheduleData = json_decode($response->getBody(), true);
                $ItemsArray = array_merge($ItemsArray, $scheduleData['data']['segments']);

                if (isset($scheduleData['pagination']['cursor'])) {
                    $url = "https://api.twitch.tv/helix/schedule?broadcaster_id=" . $broadcasterId . "&after=" . $scheduleData['pagination']['cursor'];
                } else {
                    $pagination = false;
                }
            }

            if ($ical) {
                // Generate iCal format
                header('Content-type: text/calendar');
                echo generateICal($ItemsArray);
            } elseif ($html) {
                // Generate HTML format
                header('Content-type: text/html');
                echo generateHTML($ItemsArray);
            } else {
                // Return JSON format
                header('Content-type: application/json');
                echo json_encode($ItemsArray);
            }
        } else {
            // Return an empty data array/object
            $userResponse = ["data" => []];
            header('Content-type: application/json');
            echo json_encode($userResponse, true);
        }
    } catch (\GuzzleHttp\Exception\RequestException $e) {
        header('Content-type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    // Return an empty data array/object
    $userResponse = ["data" => []];
    header('Content-type: application/json');
    echo json_encode($userResponse, true);
}

function generateICal($items)
{
    // Implement iCal generation logic here
    return "BEGIN:VCALENDAR\nVERSION:2.0\n" . implode("\n", array_map(function ($item) {
        return "BEGIN:VEVENT\nSUMMARY:{$item['title']}\nDTSTART:{$item['start_time']}\nDTEND:{$item['end_time']}\nEND:VEVENT";
    }, $items)) . "\nEND:VCALENDAR";
}

function generateHTML($items)
{
    // Implement HTML generation logic here
    return "<!DOCTYPE html><html><head><meta charset='UTF-8'><link rel='stylesheet' href='/assets/styles.css?cachebust=oL8nT1pQ5qI7gC8r'></head><body><div id='container'>" . implode("", array_map(function ($item) {
        return "<div class='event item'><span class='start_date'>" . $item['start_time'] . "</span><span class='title'>" . $item['title'] . "</span></div>";
    }, $items)) . "</div><script src='/assets/functions.js?cachebust=oL8nT1pQ5qI7gC8r'></script></body></html>";
}