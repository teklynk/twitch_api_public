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
        // Determine the API endpoint
        if (isset($_GET['channel'])) {
            $url = "https://api.twitch.tv/helix/users?login=" . trim(strtolower(str_replace('@', '', $_GET['channel'])));
        } elseif (isset($_GET['id'])) {
            $url = "https://api.twitch.tv/helix/users?id=" . trim($_GET['id']);
        } else {
            throw new Exception("Invalid parameters. Provide either 'channel' or 'id'.");
        }

        // Perform the request
        $response = $client->request('GET', $url, [
            'headers' => $headers,
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

    // return and empty data array/object
    $userResponse = ["data" => []];

    header('Content-type: application/json');

    echo json_encode(["data" => []]);
}