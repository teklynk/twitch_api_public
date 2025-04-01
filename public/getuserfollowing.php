<?php
require_once(__DIR__ . '/../config/config.php');

use GuzzleHttp\Client;

$client = new Client();

$limit = isset($_GET['limit']) ? $_GET['limit'] : 100;
$after = isset($_GET['after']) ? $_GET['after'] : '';
$before = isset($_GET['before']) ? $_GET['before'] : '';
$ref = isset($_GET['ref']) ? $_GET['ref'] : ''; //needs to be base64 encoded
$clientId = isset($_GET['clientId']) ? $_GET['clientId'] : ''; //needs to be base64 encoded

$headers = [
    'Authorization' => 'Bearer ' . base64_decode($ref),
    'Client-Id' => base64_decode($clientId)
];

if (!empty($after)) {
    $afterVar = "&after=" . $after;
    $beforeVar = "";
} else {
    $afterVar = "";
}

if (!empty($before)) {
    $beforeVar = "&before=" . $before;
    $afterVar = "";
} else {
    $beforeVar = "";
}

if ($limit > 100) {
    $limit = 100;
}

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
            // Get user followers
            $url = "https://api.twitch.tv/helix/channels/followed?user_id=" . $userInfo['data'][0]['id'] . "&first=" . trim(strtolower($limit)) . $beforeVar . $afterVar;
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
}