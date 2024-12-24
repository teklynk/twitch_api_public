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
            // Get user FFZ emotes
            $url = "https://api.frankerfacez.com/v1/room/id/" . $userResult['data'][0]['id'];
            $response = $client->request('GET', $url);
            $userData = json_decode($response->getBody(), true);
            $userRoomSetId = $userData['room']['set'];

            // Get global FFZ emotes
            $url = "https://api.frankerfacez.com/v1/set/global";
            $response = $client->request('GET', $url);
            $globalData = json_decode($response->getBody(), true);

            // Combine all FFZ emotes into one array
            $combindArr = array_merge(
                (array) $userData['sets'][$userRoomSetId]['emoticons'],
                (array) $globalData['sets']['3']['emoticons'],
                (array) $globalData['sets']['1532818']['emoticons'],
                (array) $globalData['sets']['1539687']['emoticons']
            );

            foreach ($combindArr as $data) {
                if ($data['name'] > "") {
                    $ItemsArray[] = [
                        "id" => $data['id'],
                        "code" => $data['name']
                    ];
                }
            }

            header('Content-type: application/json');
            echo json_encode($ItemsArray);
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