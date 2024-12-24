<?php
require_once(__DIR__ . '/../config/config.php');

use GuzzleHttp\Client;

$client = new Client();

$headers = [
    'Authorization' => 'Bearer ' . AUTH_TOKEN,
    'Client-Id' => getenv('API_TWITCH_CLIENT_ID')
];

if (isset($_GET['id']) || isset($_GET['name'])) {

    try {
        if (!empty($_GET['id'])) {
            $url = "https://api.twitch.tv/helix/games?id=" . trim($_GET['id']);
        } elseif (!empty($_GET['name'])) {
            $url = "https://api.twitch.tv/helix/games?name=" . trim(rawurlencode($_GET['name']));
        }

        // Perform the request
        $response = $client->request('GET', $url, [
            'headers' => $headers, // Pass your headers here
        ]);

        // Get the response body and status code
        $userResponse = $response->getBody()->getContents();
        $userStatus = $response->getStatusCode();

        // Output the response as JSON
        header('Content-type: application/json');
        echo $userResponse;
    } catch (\GuzzleHttp\Exception\RequestException $e) {
        // Handle request errors
        header('Content-type: application/json');
        echo json_encode([
            'error' => 'Request failed',
            'message' => $e->getMessage()
        ]);
    } catch (Exception $e) {
        // Handle general errors
        header('Content-type: application/json');
        echo json_encode([
            'error' => 'Bad Request',
            'message' => $e->getMessage()
        ]);
    }
} else {
    // return an empty data array/object
    $userResponse = ["data" => []];

    header('Content-type: application/json');
    echo json_encode($userResponse, true);
}