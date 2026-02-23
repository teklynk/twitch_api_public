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

$cacheTTL = 43200; // 12 hours

$cached = null;
$mem = null;
if (class_exists('Memcached')) {
    $mem = new Memcached();
    if (gethostbyname('memcached') !== 'memcached') {
        $mem->addServer("memcached", 11211);
    } else {
        $mem->addServer("127.0.0.1", 11211);
    }
    $cacheKey = 'twitch_user_info_' . md5(json_encode([$channel, $_GET['id'] ?? '']));
    $cached = $mem->get($cacheKey);
}

if ($cached) {
    header('Content-type: application/json');
    echo $cached;
    exit;
}

if ($mem) {
    ob_start();
}

if ($channel || isset($_GET['id'])) {

    try {
        // Determine the API endpoint
        if ($channel) {
            $url = "https://api.twitch.tv/helix/users?login=" . trim(strtolower(str_replace('@', '', $channel)));
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

if ($mem) {
    $output = ob_get_flush();
    $mem->set($cacheKey, $output, $cacheTTL);
}