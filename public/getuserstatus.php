<?php
require_once(__DIR__ . '/../config/config.php');

use GuzzleHttp\Client;

$client = new Client();

$channel = isset($_GET['channel']) ? trim(strtolower($_GET['channel'])) : '';

foreach ($ignoreKeywords as $keyword) {
    if (preg_match("/$keyword/", $channel)) {
        $channel = null;
        break;
    }
}

$cacheTTL = 300; // 5 minutes

$cached = null;
$mem = null;
if (class_exists('Memcached')) {
    $mem = new Memcached();
    if (gethostbyname('memcached') !== 'memcached') {
        $mem->addServer("memcached", 11211);
    } else {
        $mem->addServer("127.0.0.1", 11211);
    }
    $cacheKey = 'twitch_user_status_' . md5(json_encode([$channel, $_GET['id'] ?? '']));
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

$headers = [
    'Authorization' => 'Bearer ' . AUTH_TOKEN,
    'Client-Id' => getenv('API_TWITCH_CLIENT_ID')
];

if ($channel || isset($_GET['id'])) {

    try {
        // Get user id and info
        if ($channel) {
            $url = "https://api.twitch.tv/helix/users?login=" . trim(strtolower(str_replace('@', '', $channel)));
        } elseif (isset($_GET['id'])) {
            $url = "https://api.twitch.tv/helix/users?id=" . trim($_GET['id']);
        }

        $response = $client->request('GET', $url, [
            'headers' => $headers
        ]);

        $userInfo = json_decode($response->getBody(), true);
        $userStatus = $response->getStatusCode();

        if ($userStatus == 200 && count($userInfo['data']) > 0) {
            // Get user status
            $url = "https://api.twitch.tv/helix/channels?broadcaster_id=" . $userInfo['data'][0]['id'];
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
} else {
    // Return an empty data array/object
    $userResponse = ["data" => []];
    header('Content-type: application/json');
    echo json_encode($userResponse, true);
}

if ($mem) {
    $output = ob_get_flush();
    $mem->set($cacheKey, $output, $cacheTTL);
}