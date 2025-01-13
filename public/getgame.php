<?php
require_once(__DIR__ . '/../config/config.php');

use GuzzleHttp\Client;

$client = new Client();

$itemsArray = [];

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
        $userData = json_decode($response->getBody(), true);
        $userStatus = $response->getStatusCode();

        foreach ($userData['data'] as $data) {
            // Use the box_art_url to create the scaled "usable" box art image
            $box_art_url = $data['box_art_url'];
            // define size
            $width = 285;
            $height = 380;
            // Replace box_art_url
            $box_art_scaled = str_replace(
                ['{width}', '{height}'],
                [$width, $height],
                $box_art_url
            );

            $itemsArray[] = [
                "id" => $data['id'],
                "name" => $data['name'],
                "box_art_url" => $data['box_art_url'],
                "igdb_id" => $data['igdb_id'],
                "box_art_url_scaled" => $box_art_scaled,
            ];
        }

        $dataArray = [
            "data" => $itemsArray
        ];

        header('Content-type: application/json');
        echo json_encode($dataArray);

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
    $dataArray = [
        "data" => []
    ];

    header('Content-type: application/json');
    echo json_encode($dataArray);
}