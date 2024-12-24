<?php
require_once(__DIR__ . '/../config/config.php');

$ItemsArray = [];
$combindArr = [];

use GuzzleHttp\Client;

$client = new Client();

$headers = [
    'Authorization' => 'Bearer ' . AUTH_TOKEN,
    'Client-Id' => getenv('API_TWITCH_CLIENT_ID')
];

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
            // Get user 7tv emotes
            $url = "https://7tv.io/v3/users/twitch/" . $userResult['data'][0]['id'];
            $response = $client->request('GET', $url);
            $userData = json_decode($response->getBody(), true);

            // Get global 7tv emotes
            $url = "https://7tv.io/v3/emote-sets/6353512c802a0e34bac96dd2";
            $response = $client->request('GET', $url);
            $globalData = json_decode($response->getBody(), true);

            // Combine user and global emotes
            $combindArr = array_merge($userData, $globalData);

            header('Content-type: application/json');
            echo json_encode($combindArr);
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
}
?>