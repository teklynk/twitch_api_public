<?php
require_once(__DIR__ . '/../config/config.php');

use GuzzleHttp\Client;

$client = new Client();

$headers = [
    'Authorization' => 'Bearer ' . AUTH_TOKEN,
    'Client-Id' => getenv('API_TWITCH_CLIENT_ID')
];

$ItemsArray = [];

if (isset($_GET['channel'])) {
    try {
        // Get user info
        $url = "https://api.twitch.tv/helix/users?login=" . trim(strtolower(str_replace('@', '', $_GET['channel'])));
        $response = $client->request('GET', $url, [
            'headers' => $headers
        ]);

        $userResult = json_decode($response->getBody(), true);
        $userStatus = $response->getStatusCode();

        if ($userStatus == 200 && count($userResult['data']) > 0) {
            // Get user emotes
            $url = "https://api.twitch.tv/helix/chat/emotes?broadcaster_id=" . $userResult['data'][0]['id'];
            $response = $client->request('GET', $url, [
                'headers' => $headers
            ]);

            $emotesData = json_decode($response->getBody(), true);

            header('Content-type: application/json');
            echo json_encode($emotesData);
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
?>