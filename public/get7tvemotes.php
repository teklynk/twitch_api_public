<?php
require_once(__DIR__ . '/../config/config.php');

$ItemsArray = array();
$combindArr = array();

$headers = [
    'Authorization: Bearer ' . AUTH_TOKEN,
    'Client-Id: ' . CLIENT_ID
];

if (isset($_GET['channel'])) {

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "https://api.twitch.tv/helix/users?login=" . trim(strtolower(str_replace('@', '', $_GET['channel']))));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $userInfo = curl_exec($ch);
    $userStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $userResult = json_decode($userInfo, true);

    if ($userStatus == 200 && count($userResult['data']) > 0) {
        //Get user 7tv emotes
        curl_setopt($ch, CURLOPT_URL, "https://7tv.io/v3/users/twitch/" . $userResult['data'][0]['id']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $userResponse = curl_exec($ch);

        //user 7tvemotes data
        $userData = json_decode($userResponse, true);

        foreach ($userData['emote_set']['emotes'] as $data) {
            if ($data > "") {

                $ItemsArray[] = array(
                    "id" => $data['id'],
                    "code" => $data['name']
                );
            }
        }

        header('Content-type: application/json');

        echo json_encode($ItemsArray);

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

}
?>