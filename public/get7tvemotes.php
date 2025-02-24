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

        //Get global 7tv emotes
        curl_setopt($ch, CURLOPT_URL, "https://7tv.io/v3/emote-sets/6353512c802a0e34bac96dd2");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $globalResponse = curl_exec($ch);

        //global 7tvemotes data
        $globalData = json_decode($globalResponse, true);

        //combine all 7tvemotes into one array
        $combindArr = array_merge((array)$userData['emote_set']['emotes'], (array)$globalData['emotes']);

        foreach ($combindArr as $data) {
            if ($data['name'] > "") {

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