<?php
require_once(__DIR__ . '/../config/config.php');

$limit = isset($_GET['limit']) ? $_GET['limit'] : 100;
$after = isset($_GET['after']) ? $_GET['after'] : '';
$before = isset($_GET['before']) ? $_GET['before'] : '';
$ref = isset($_GET['ref']) ? $_GET['ref'] : ''; //needs to be base64 encoded
$clientId = isset($_GET['clientId']) ? $_GET['clientId'] : ''; //needs to be base64 encoded

//var_dump(base64_decode($clientId));

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

if ($limit > 100 ) {
    $limit = 100;
}

$headers = [
    'Authorization: Bearer ' . base64_decode($ref),
    'Client-Id: ' . base64_decode($clientId)
];

if (isset($_GET['channel'])) {

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "https://api.twitch.tv/helix/users?login=" . trim(strtolower(str_replace('@', '', $_GET['channel']))));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $userInfo = curl_exec($ch);
    $userStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $userResult = json_decode($userInfo, true);

    //var_dump($userInfo);

    if ($userStatus == 200 && count($userResult['data']) > 0) {
        //Get user followers
        curl_setopt($ch, CURLOPT_URL, "https://api.twitch.tv/helix/channels/followed?user_id=" . $userResult['data'][0]['id'] . "&first=" . trim(strtolower($limit)) . $afterVar);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $userResponse = curl_exec($ch);

        header('Content-type: application/json');

        echo $userResponse;

    } else {
        
        // return and empty data array/object
        $userResponse = array(
            "data" => []
        );

        $userResponse = json_encode($userResponse, true);

        header('Content-type: application/json');

        echo $userResponse;
    }

    curl_close($ch);
}
?>