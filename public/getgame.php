<?php
require_once(__DIR__ . '/../config/config.php');

$headers = [
    'Authorization: Bearer ' . AUTH_TOKEN,
    'Client-Id: ' . CLIENT_ID
];

if (isset($_GET['id']) || isset($_GET['name'])) {

    $ch = curl_init();
    
    if (!empty($_GET['id'])) {
        curl_setopt($ch, CURLOPT_URL, "https://api.twitch.tv/helix/games?id=" . trim($_GET['id'])); 
    } elseif (!empty($_GET['name'])) {
        curl_setopt($ch, CURLOPT_URL, "https://api.twitch.tv/helix/games?name=" . trim(rawurlencode($_GET['name'])));
    }
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $userInfo = curl_exec($ch);

    header('Content-type: application/json');

    echo $userInfo;

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
