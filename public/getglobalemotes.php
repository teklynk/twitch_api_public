<?php
require_once(__DIR__ . '/../config/config.php');

use GuzzleHttp\Client;

$client = new Client();

$headers = [
    'Authorization' => 'Bearer ' . AUTH_TOKEN,
    'Client-Id' => getenv('API_TWITCH_CLIENT_ID')
];

try {
    // Determine the API endpoint for the user info
    $url = "https://api.twitch.tv/helix/chat/emotes/global";

    // Perform the first request to get user info
    $userResponse = $client->request('GET', $url, [
        'headers' => $headers,
    ]);

    $userStatus = $userResponse->getStatusCode();
    $userResult = json_decode($userResponse->getBody()->getContents(), true);

    if ($userStatus == 200 && count($userResult['data']) > 0) {

        header('Content-type: application/json');

        echo json_encode($userResult);
    } else {
        // Return an empty data array
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