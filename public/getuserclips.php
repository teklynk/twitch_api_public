<?php
require_once(__DIR__ . '/../config/config.php');

use GuzzleHttp\Client;

$client = new Client();

$headers = [
    'Authorization' => 'Bearer ' . AUTH_TOKEN,
    'Client-Id' => getenv('API_TWITCH_CLIENT_ID')
];

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'];
$limit = isset($_GET['limit']) ? $_GET['limit'] : 100;
$random = isset($_GET['random']) ? $_GET['random'] : 'false';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
$id = isset($_GET['id']) ? $_GET['id'] : '';
$prefer_featured = isset($_GET['prefer_featured']) ? $_GET['prefer_featured'] : 'false';
$creator_name = isset($_GET['creator_name']) ? trim(strtolower($_GET['creator_name'])) : '';
$ignore = isset($_GET['ignore']) ? $_GET['ignore'] : '';
$itemCount = 0;
$shuffle = isset($_GET['shuffle']) ? $_GET['shuffle'] : 'false';
$channel = isset($_GET['channel']) ? trim(strtolower($_GET['channel'])) : '';

foreach ($ignoreKeywords as $keyword) {
    if (preg_match("/$keyword/", $channel)) {
        $channel = null;
        break;
    }
}

// Cache TTL in seconds. Twitch Clip URLs expire after about 1 hour
$cacheTTL = 3600; // 1 hour

$cached = null;
$mem = null;
if (class_exists('Memcached')) {
    $mem = new Memcached();
    // Check if running in Docker (memcached hostname resolves)
    if (gethostbyname('memcached') !== 'memcached') {
        $mem->addServer("memcached", 11211);
    } else {
        $mem->addServer("127.0.0.1", 11211);
    }
    $cacheKey = 'twitch_clips_' . md5(json_encode([$channel, $id, $limit, $random, $start_date, $end_date, $prefer_featured, $creator_name, $shuffle, ($random == 'true' ? ($_GET['count'] ?? '1') : '')]));
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

$itemsArray = [];

if ($limit > 100) {
    $limit = 100;
}

if (!empty($start_date)) {
    $start_dateVar = "&started_at=" . $start_date;
} else {
    $start_dateVar = "";
}

if (!empty($end_date)) {
    $end_dateVar = "&ended_at=" . $end_date;
} else {
    $end_dateVar = "";
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

        // check if http status is good and that there is data/clips
        if ($userStatus == 200 && count($userResult['data']) > 0) {
            // Get user clips
            $url = "https://api.twitch.tv/helix/clips?broadcaster_id=" . $userResult['data'][0]['id'] . "&first=" . trim(strtolower($limit)) . $start_dateVar . $end_dateVar;
            $response = $client->request('GET', $url, [
                'headers' => $headers
            ]);

            $userStatus = $response->getStatusCode();

            // all clips data
            $userData = json_decode($response->getBody(), true);

            if ($userStatus == 200 && count($userData['data']) > 0) {

                foreach ($userData['data'] as $data) {

                    $inc_data = false;

                    // Filter for creator_name and prefer_featured
                    if (!empty($creator_name) && strtolower($data['creator_name']) == $creator_name) {
                        // &creator_name set and this clip creator by that user
                        if (!empty($prefer_featured) && $prefer_featured == "true") {
                            // &prefer_featured=true
                            $inc_data = $data['is_featured'] == "true";
                        } else {
                            // &prefer_featured not set
                            $inc_data = true;
                        }
                    } elseif (empty($creator_name)) {
                        // &creator_name not set
                        if (!empty($prefer_featured) && $prefer_featured == "true") {
                            // &prefer_featured=true
                            $inc_data = $data['is_featured'] == "true";
                        } else {
                            // &prefer_featured not set
                            $inc_data = true;
                        }
                    }

                    if ($inc_data) {

                        $itemCount++;

                        $itemsArray[] = [
                            "item" => $itemCount,
                            "id" => $data['id'],
                            "url" => $data['url'],
                            "embed_url" => $data['embed_url'],
                            "broadcaster_id" => $data['broadcaster_id'],
                            "broadcaster_name" => $data['broadcaster_name'],
                            "creator_id" => $data['creator_id'],
                            "creator_name" => $data['creator_name'],
                            "video_id" => $data['video_id'],
                            "game_id" => $data['game_id'],
                            "language" => $data['language'],
                            "title" => $data['title'],
                            "view_count" => $data['view_count'],
                            "created_at" => $data['created_at'],
                            "thumbnail_url" => str_replace('480x272.jpg', '1920x1080.jpg', $data['thumbnail_url']),
                            "duration" => $data['duration'],
                            "vod_offset" => $data['vod_offset'],
                            "is_featured" => $data['is_featured'],
                            "clip_url" => $protocol . $host . '/getclipurl.php?id=' . $data['id']
                        ];
                    }
                }

                $dataArray = [
                    "data" => $itemsArray
                ];

                // Pull a single random clip   IE: &random=true
                if (!empty($random) && $random == "true") {
                    $array_item = [];
                    $array_count = count($dataArray['data']);
                    $random_cnt = !empty($_GET['count']) ? min((INT) $_GET['count'], $array_count) : 1;

                    $random_keys = array_rand($dataArray['data'], $random_cnt);
                    if ($random_cnt == 1) {
                        $array_item[] = $dataArray['data'][$random_keys];
                    } else {
                        foreach ($random_keys as $key) {
                            $array_item[] = $dataArray['data'][$key];
                        }
                    }

                    $array_data = [
                        "data" => $array_item
                    ];

                    header('Content-type: application/json');
                    echo json_encode($array_data);
                } else {

                    if ($shuffle === 'true') {
                        $itemsArray = array_values($itemsArray); // Reset keys after loop
                        shuffle($itemsArray); // Shuffle the array
                        $dataArray = [
                            "data" => $itemsArray
                        ];
                    } else {
                        $dataArray = [
                            "data" => $itemsArray
                        ];
                    }

                    // Return all clips
                    header('Content-type: application/json');
                    echo json_encode($dataArray);
                }

            } else {
                // Return an empty data array if user/channel not found
                header('Content-type: application/json');
                echo json_encode(["data" => []]);
            }
        } else {
            // return an empty data array/object
            $dataArray = ["data" => []];
            header('Content-type: application/json');
            echo json_encode($dataArray);
        }
    } catch (GuzzleHttp\Exception\RequestException $e) {
        header('Content-type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
    }

} elseif (!isset($_GET['id']) && empty($_GET['id'])) {
    // return an empty data array/object
    $dataArray = ["data" => []];

    header('Content-type: application/json');
    echo json_encode($dataArray);
}

// Get clip by its ID
// https://example.com/getuserclips.php?id=LaconicCulturedPieMingLee-qxlzFZb89ZlEgdzP
if (isset($_GET['id']) && !empty($_GET['id'])) {

    // Get user clips
    $response = $client->request('GET', "https://api.twitch.tv/helix/clips?id=" . $id, [
        'headers' => $headers
    ]);
    $userStatus = $response->getStatusCode();

    if ($userStatus == 200) {
        // all clips data
        $userData = json_decode($response->getBody(), true);

        foreach ($userData['data'] as $data) {

            $itemCount++;

            $itemsArray[] = [
                "item" => $itemCount,
                "id" => $data['id'],
                "url" => $data['url'],
                "embed_url" => $data['embed_url'],
                "broadcaster_id" => $data['broadcaster_id'],
                "broadcaster_name" => $data['broadcaster_name'],
                "creator_id" => $data['creator_id'],
                "creator_name" => $data['creator_name'],
                "video_id" => $data['video_id'],
                "game_id" => $data['game_id'],
                "language" => $data['language'],
                "title" => $data['title'],
                "view_count" => $data['view_count'],
                "created_at" => $data['created_at'],
                "thumbnail_url" => str_replace('-preview-480x272.jpg', '-preview-1920x1080.jpg', $data['thumbnail_url']),
                "duration" => $data['duration'],
                "vod_offset" => $data['vod_offset'],
                "is_featured" => $data['is_featured'],
                "clip_url" => $protocol . $host . '/getclipurl.php?id=' . $data['id']
            ];
        }

        $dataArray = [
            "data" => $itemsArray
        ];
        
        header('Content-type: application/json');
        echo json_encode($dataArray);
    } else {
        // return an empty data array/object
        $dataArray = [
            "data" => []
        ];

        header('Content-type: application/json');
        echo json_encode($dataArray);
    }
}

if ($mem) {
    $output = ob_get_flush();
    $mem->set($cacheKey, $output, $cacheTTL);
}