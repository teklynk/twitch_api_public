<?php
require_once(__DIR__ . '/../config/config.php');

use GuzzleHttp\Client;

$client = new Client();

$headers = [
    'Authorization' => 'Bearer ' . AUTH_TOKEN,
    'Client-Id' => getenv('API_TWITCH_CLIENT_ID')
];

if (isset($_GET['channel']) || isset($_GET['id'])) {

    try {
        // Get user id and info
        if (isset($_GET['channel'])) {
            $url = "https://api.twitch.tv/helix/users?login=" . trim(strtolower(str_replace('@', '', $_GET['channel'])));
        } elseif (isset($_GET['id'])) {
            $url = "https://api.twitch.tv/helix/users?id=" . trim($_GET['id']);
        }

        $response = $client->request('GET', $url, [
            'headers' => $headers
        ]);

        $userInfo = json_decode($response->getBody(), true);
        $userStatus = $response->getStatusCode();

        if ($userStatus == 200 && count($userInfo['data']) > 0) {
            // Get user status
            $url = "https://api.twitch.tv/helix/channels?broadcaster_id=" . $userInfo['data'][0]['id'];
            $response = $client->request('GET', $url, [
                'headers' => $headers
            ]);

            header('Content-type: application/json');
            echo $response->getBody();
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