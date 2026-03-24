<?php
require_once(__DIR__ . '/../config/config.php');

use GuzzleHttp\Client;

$client = new Client();

$headers = [
    'Authorization' => 'Bearer ' . AUTH_TOKEN,
    'Client-Id' => getenv('API_TWITCH_CLIENT_ID')
];

$id = isset($_GET['id']) ? $_GET['id'] : '';
$url = isset($_GET['url']) ? $_GET['url'] : 'true';

// Get clip url by its ID and return it as a Content-Type: video/mp4
// https://example.com/getclipurl.php?id=LaconicCulturedPieMingLee-qxlzFZb89ZlEgdzP
if ($id) {

    $clip_url = null;

    $cacheTTL = 3600; // 1 hour

    $mem = null;
    if (class_exists('Memcached')) {
        $mem = new Memcached();
        if (gethostbyname('memcached') !== 'memcached') {
            $mem->addServer("memcached", 11211);
        } else {
            $mem->addServer("127.0.0.1", 11211);
        }
        $cacheKey = 'twitch_clip_url_' . md5($id);
        $clip_url = $mem->get($cacheKey);
    }

    if (!$clip_url) {
        try {
            // Get user clips
            $response = $client->request('GET', "https://api.twitch.tv/helix/clips?id=" . $id, [
                'headers' => $headers
            ]);

            $userStatus = $response->getStatusCode();

            if ($userStatus == 200) {
                // all clips data
                $userData = json_decode($response->getBody(), true);

                foreach ($userData['data'] as $data) {

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
                    break;
                }

                if ($mem && isset($clip_url) && $clip_url) {
                    $mem->set($cacheKey, $clip_url, $cacheTTL);
                }
            }
        } catch (\Exception $e) {
            // Log error or ignore
        }
    }

    if (isset($clip_url) && $clip_url) {
        if ($url == 'false') {
            // render as a video source file
            $video = $client->request('GET', $clip_url, ['stream' => true]);
            header('Content-Type: video/mp4');
            header('Content-Length: ' . $video->getHeaderLine('Content-Length'));

            $body = $video->getBody();
            while (!$body->eof()) {
                // Chunk size: 8192 = 8 Kilobytes
                echo $body->read(8192);
            }
            exit();
        } else {
            // redirect to Twitch video clip url
            header('Location: ' . $clip_url, true, 302);
            exit();
        }
    }

    http_response_code(404);
    exit();
}