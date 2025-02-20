<?php
require_once(__DIR__ . '/../config/config.php');

use GuzzleHttp\Client;

$client = new Client();

$headers = [
    'Authorization' => 'Bearer ' . AUTH_TOKEN,
    'Client-Id' => getenv('API_TWITCH_CLIENT_ID')
];

$limit = isset($_GET['limit']) ? $_GET['limit'] : 100;
$after = isset($_GET['after']) ? $_GET['after'] : '';
$before = isset($_GET['before']) ? $_GET['before'] : '';
$random = isset($_GET['random']) ? $_GET['random'] : '';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
$id = isset($_GET['id']) ? $_GET['id'] : '';
$prefer_featured = isset($_GET['prefer_featured']) ? $_GET['prefer_featured'] : '';
$creator_name = isset($_GET['creator_name']) ? trim(strtolower($_GET['creator_name'])) : '';
$ignore = isset($_GET['ignore']) ? $_GET['ignore'] : '';
$itemCount = 0;
$shuffle = isset($_GET['shuffle']) ? $_GET['shuffle'] : 'false';

$itemsArray = [];

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

if (isset($_GET['channel']) && !empty($_GET['channel'])) {
    try {
        // Get user info
        $url = "https://api.twitch.tv/helix/users?login=" . trim(strtolower(str_replace('@', '', $_GET['channel'])));

        $response = $client->request('GET', $url, [
            'headers' => $headers
        ]);

        $userResult = json_decode($response->getBody(), true);
        $userStatus = $response->getStatusCode();

        // check if http status is good and that there is data/clips
        if ($userStatus == 200 && count($userResult['data']) > 0) {
            // Get user clips
            $url = "https://api.twitch.tv/helix/clips?broadcaster_id=" . $userResult['data'][0]['id'] . "&first=" . trim(strtolower($limit)) . $afterVar . $beforeVar . $start_dateVar . $end_dateVar;
            $response = $client->request('GET', $url, [
                'headers' => $headers
            ]);

            $userStatus = $response->getStatusCode();
            if ($userStatus == 200) {
                // all clips data
                $userData = json_decode($response->getBody(), true);

                foreach ($userData['data'] as $data) {

                    if ($ignore === 'new' && strpos($data['thumbnail_url'], 'https://static-cdn.jtvnw.net/twitch-clips-thumbnails-prod/') !== false) {
                        continue; // skip clips that contain the https://static-cdn.jtvnw.net/twitch-clips-thumbnails-prod/ thumnail url. These urls no longer work as of Sept 2024.
                    }

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
                        if (strpos($data['thumbnail_url'], 'https://static-cdn.jtvnw.net/twitch-clips-thumbnails-prod/') !== false) {
                            $body = json_encode([
                                [
                                    "operationName" => "VideoAccessToken_Clip",
                                    "variables" => [
                                        "platform" => "web",
                                        "slug" => $data['id']
                                    ],
                                    "extensions" => [
                                        "persistedQuery" => [
                                            "version" => 1,
                                            "sha256Hash" => TWITCH_SHA256HASH
                                        ]
                                    ]
                                ]
                            ]);

                            $response = $client->request('POST', TWITCH_GRAPHQL_URL, [
                                'headers' => [
                                    'Content-Type' => 'application/json',
                                    'Client-ID' => TWITCH_CLIENT_ID
                                ],
                                'body' => $body
                            ]);

                            $clipsArray = json_decode($response->getBody(), true);

                            $clipsSignature = $clipsArray[0]['data']['clip']['playbackAccessToken']['signature'];
                            $clipsToken = urlencode($clipsArray[0]['data']['clip']['playbackAccessToken']['value']);
                            $clipsVideoSource = $clipsArray[0]['data']['clip']['videoQualities'][0]['sourceURL'];

                            $clipUrl = $clipsVideoSource . '?sig=' . $clipsSignature . '&token=' . $clipsToken;
                        } else {
                            // Use the thumbnail url to create the clip url
                            $clipUrl = explode("-preview-", $data['thumbnail_url']);
                            $clipUrl = $clipUrl[0] . ".mp4";
                        }

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
                            "thumbnail_url" => $data['thumbnail_url'],
                            "duration" => $data['duration'],
                            "vod_offset" => $data['vod_offset'],
                            "is_featured" => $data['is_featured'],
                            "clip_url" => $clipUrl
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
                // Return an empty data array/object if user not found
                $dataArray = ["data" => []];
                header('Content-type: application/json');
                echo json_encode($dataArray, true);
            }
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

            if ($ignore === 'new' && strpos($data['thumbnail_url'], 'https://static-cdn.jtvnw.net/twitch-clips-thumbnails-prod/') !== false) {
                continue; // skip clips that contain the https://static-cdn.jtvnw.net/twitch-clips-thumbnails-prod/ thumnail url. These urls no longer work as of Sept 2024.
            }
            
            if (strpos($data['thumbnail_url'], 'https://static-cdn.jtvnw.net/twitch-clips-thumbnails-prod/') !== false) {
                $body = json_encode([
                    [
                        "operationName" => "VideoAccessToken_Clip",
                        "variables" => ["platform" => "web", "slug" => $data['id']],
                        "extensions" => ["persistedQuery" => ["version" => 1, "sha256Hash" => TWITCH_SHA256HASH]]
                    ]
                ]);

                $response = $client->request('POST', TWITCH_GRAPHQL_URL, [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Client-ID' => TWITCH_CLIENT_ID
                    ],
                    'body' => $body
                ]);

                $clips_array = json_decode($response->getBody(), true);

                $clips_signature = $clips_array[0]['data']['clip']['playbackAccessToken']['signature'];
                $clips_token = urlencode($clips_array[0]['data']['clip']['playbackAccessToken']['value']);
                $clips_video_source = $clips_array[0]['data']['clip']['videoQualities'][0]['sourceURL'];

                $clip_url = $clips_video_source . '?sig=' . $clips_signature . '&token=' . $clips_token;
            } else {
                // Use the thumbnail url to create the clip url
                $clip_url = explode("-preview-", $data['thumbnail_url']);
                $clip_url = $clip_url[0] . ".mp4";
            }

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
                "thumbnail_url" => $data['thumbnail_url'],
                "duration" => $data['duration'],
                "vod_offset" => $data['vod_offset'],
                "is_featured" => $data['is_featured'],
                "clip_url" => $clip_url
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