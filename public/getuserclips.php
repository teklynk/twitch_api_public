<?php
require_once(__DIR__ . '/../config/config.php');

$limit = trim($_GET['limit']);
$after = trim($_GET['after']);
$before = trim($_GET['before']);
$random = trim($_GET['random']);
$start_date = trim($_GET['start_date']);
$end_date = trim($_GET['end_date']);
$id = trim($_GET['id']);
$itemCount = 0;

$itemsArray = array();

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

$ch = curl_init();

$headers = [
    'Authorization: Bearer ' . AUTH_TOKEN,
    'Client-Id: ' . CLIENT_ID
];

curl_setopt($ch, CURLOPT_URL, "https://api.twitch.tv/helix/users?login=" . trim(strtolower(str_replace('@', '', $_GET['channel']))));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$userInfo = curl_exec($ch);
$userResult = json_decode($userInfo, true);

//Get user clips
curl_setopt($ch, CURLOPT_URL, "https://api.twitch.tv/helix/clips?broadcaster_id=" . $userResult['data'][0]['id'] . "&first=" . trim(strtolower($limit)) . $afterVar . $beforeVar . $start_dateVar . $end_dateVar);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$userResponse = curl_exec($ch);

//all clips data
$userData = json_decode($userResponse, true);

foreach ($userData['data'] as $data) {

    //Get game details
    curl_setopt($ch, CURLOPT_URL, "https://api.twitch.tv/helix/games?id=" . trim($data['game_id']));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $userGameInfo = curl_exec($ch);

    $gameData = json_decode($userGameInfo, true);

    // Use the thumbnail url to create the clip url
    $clip_url = explode("-preview-", $data['thumbnail_url']);
    $clip_url = $clip_url[0] . ".mp4";

    $itemCount++;

    $itemsArray[] = array(
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
        "game_name" => $gameData['data'][0]['name'],
        "language" => $data['language'],
        "title" => $data['title'],
        "view_count" => $data['view_count'],
        "created_at" => $data['created_at'],
        "thumbnail_url" => $data['thumbnail_url'],
        "duration" => $data['duration'],
        "vod_offset" => $data['vod_offset'],
        "clip_url" => $clip_url
    );
}

$dataArray = array(
    "data" => $itemsArray
);

// Pull a single random clip   IE: &random=true
if (!empty($random) && $random == "true") {

    $array_item = array();

    $array_count = count($dataArray['data']);

    $array_random = rand(0, $array_count - 1);

    $array_item[] = $dataArray['data'][$array_random];

    $array_data = array(
        "data" => $array_item
    );

    header('Content-type: application/json');

    echo json_encode($array_data);

// Pull a specific clip by its ID/Slug   IE: &id=PowerfulCogentChinchillaBCWarrior-WRjKBjtHFKFDc1Dt
} elseif (!empty($id)) {

    //Get user clips
    curl_setopt($ch, CURLOPT_URL, "https://api.twitch.tv/helix/clips?id=" . $id);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $userResponse = curl_exec($ch);

    //all clips data
    $userData = json_decode($userResponse, true);

    foreach ($userData['data'] as $data) {

        //Get game details
        curl_setopt($ch, CURLOPT_URL, "https://api.twitch.tv/helix/games?id=" . trim($data['game_id']));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $userGameInfo = curl_exec($ch);

        $gameData = json_decode($userGameInfo, true);

        // Use the thumbnail url to create the clip url
        $clip_url = explode("-preview-", $data['thumbnail_url']);
        $clip_url = $clip_url[0] . ".mp4";

        $itemCount++;

        $itemsArray[] = array(
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
            "game_name" => $gameData['data'][0]['name'],
            "language" => $data['language'],
            "title" => $data['title'],
            "view_count" => $data['view_count'],
            "created_at" => $data['created_at'],
            "thumbnail_url" => $data['thumbnail_url'],
            "duration" => $data['duration'],
            "vod_offset" => $data['vod_offset'],
            "clip_url" => $clip_url
        );
    }

    $dataArray = array(
        "data" => $itemsArray
    );

    header('Content-type: application/json');

    echo json_encode($dataArray);

} else {

    // Get all clips
    header('Content-type: application/json');

    echo json_encode($dataArray);

}

curl_close($ch);
?>