<?php
require_once(__DIR__ . '/../config/config.php');

$headers = [
    'Authorization: Bearer ' . AUTH_TOKEN,
    'Client-Id: ' . CLIENT_ID
];

if (isset($_GET['channel']) || isset($_GET['id'])) {

    $ch = curl_init();

    //Get user id and info
    if (isset($_GET['channel'])) {
        curl_setopt($ch, CURLOPT_URL, "https://api.twitch.tv/helix/users?login=" . trim(strtolower(str_replace('@', '', $_GET['channel']))));
    } elseif (isset($_GET['id'])) {
        curl_setopt($ch, CURLOPT_URL, "https://api.twitch.tv/helix/users?id=" . trim($_GET['id']));
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $userInfo = curl_exec($ch);
    $userStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $userResult = json_decode($userInfo, true);

    if ($userStatus == 200 && count($userResult['data']) > 0) {
        //Get user status
        curl_setopt($ch, CURLOPT_URL, "https://api.twitch.tv/helix/channels?broadcaster_id=" . $userResult['data'][0]['id']);
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
    
} else {
        
    // return and empty data array/object
    $userResponse = array(
        "data" => []
    );

    $userResponse = json_encode($userResponse, true);

    header('Content-type: application/json');

    echo $userResponse;
}
?>