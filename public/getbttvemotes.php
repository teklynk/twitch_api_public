<?php
require_once(__DIR__ . '/../config/config.php');

use GuzzleHttp\Client;

$client = new Client();

$headers = [
    'Authorization' => 'Bearer ' . AUTH_TOKEN,
    'Client-Id' => getenv('API_TWITCH_CLIENT_ID')
];

$bttvHeaders = [
    'Accept' => 'application/json'
];

$channel = isset($_GET['channel']) ? trim(strtolower($_GET['channel'])) : '';

foreach ($ignoreKeywords as $keyword) {
    if (preg_match("/$keyword/", $channel)) {
        $channel = null;
        break;
    }
}

$cacheTTL = 86400; // 24 hours

$cached = null;
$mem = null;
if (class_exists('Memcached')) {
    $mem = new Memcached();
    if (gethostbyname('memcached') !== 'memcached') {
        $mem->addServer("memcached", 11211);
    } else {
        $mem->addServer("127.0.0.1", 11211);
    }
    $cacheKey = 'twitch_bttv_emotes_' . md5($channel);
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

if ($channel) {
    try {
        // Get user info
        $url = "https://api.twitch.tv/helix/users?login=" . trim(strtolower(str_replace('@', '', $channel)));
        $response = $client->request('GET', $url, [
            'headers' => $headers
        ]);

        $userResult = json_decode($response->getBody(), true);
        $userStatus = $response->getStatusCode();

        if ($userStatus == 200 && count($userResult['data']) > 0) {
            // Get user BTTV emotes
            $url = "https://api.betterttv.net/3/cached/users/twitch/" . $userResult['data'][0]['id'];
            $response = $client->request('GET', $url, [
                'headers' => $bttvHeaders
            ]);
            $userData = json_decode($response->getBody(), true);

            // Get global BTTV emotes
            $url = "https://api.betterttv.net/3/cached/emotes/global";
            $response = $client->request('GET', $url, [
                'headers' => $bttvHeaders
            ]);
            $globalData = json_decode($response->getBody(), true);

            // Combine all BTTV emotes into one array
            $combindArr = array_merge((array) $userData['channelEmotes'], (array) $userData['sharedEmotes'], (array) $globalData);

            foreach ($combindArr as $data) {
                if ($data['code'] > "") {
                    // Process each emote data
                }
            }

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

if ($mem) {
    $output = ob_get_flush();
    $mem->set($cacheKey, $output, $cacheTTL);
}
?>