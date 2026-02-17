<?php
require_once(__DIR__ . '/../config/config.php');

use GuzzleHttp\Client;

$client = new Client();

$headers = [
    'Authorization' => 'Bearer ' . AUTH_TOKEN,
    'Client-Id' => getenv('API_TWITCH_CLIENT_ID')
];

$channel = isset($_GET['channel']) ? trim(strtolower($_GET['channel'])) : '';

foreach ($ignoreKeywords as $keyword) {
    if (preg_match("/$keyword/", $channel)) {
        $channel = null;
        break;
    }
}

if ($channel || isset($_GET['id'])) {

    try {
        // Determine the API endpoint for the user info
        if ($channel) {
            $url = "https://api.twitch.tv/helix/users?login=" . trim(strtolower(str_replace('@', '', $channel)));
        } elseif (isset($_GET['id'])) {
            $url = "https://api.twitch.tv/helix/users?id=" . trim($_GET['id']);
        } else {
            throw new Exception("Invalid parameters. Provide either 'channel' or 'id'.");
        }

        // Perform the first request to get user info
        $userResponse = $client->request('GET', $url, [
            'headers' => $headers,
        ]);

        $userStatus = $userResponse->getStatusCode();
        $userResult = json_decode($userResponse->getBody()->getContents(), true);

        if ($userStatus == 200 && count($userResult['data']) > 0) {
            // Get the user ID from the response
            $userId = $userResult['data'][0]['id'];

            // Perform the second request to get the user stream info
            $streamUrl = "https://api.twitch.tv/helix/streams?user_id=" . $userId;

            $streamResponse = $client->request('GET', $streamUrl, [
                'headers' => $headers,
            ]);

            header('Content-type: application/json');

            echo $streamResponse->getBody()->getContents();
        } else {
            // Return an empty data array if user/channel not found
            header('Content-type: application/json');
            echo json_encode(["data" => []]);
        }
    } catch (\GuzzleHttp\Exception\RequestException $e) {
        // Handle request exceptions
        header('Content-type: application/json');
        echo json_encode([
            "error" => "Request failed",
            "message" => $e->getMessage()
        ]);
    } catch (Exception $e) {
        // Handle general exceptions
        header('Content-type: application/json');
        echo json_encode([
            "error" => "Bad Request",
            "message" => $e->getMessage()
        ]);
    }

} else {

    // return and empty data array/object
    $userResponse = ["data" => []];

    header('Content-type: application/json');

    echo json_encode(["data" => []]);
}