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
        //Get user frankerfacez emotes
        curl_setopt($ch, CURLOPT_URL, "https://api.frankerfacez.com/v1/room/" . $userResult['data'][0]['login']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $userResponse = curl_exec($ch);

        //Get global frankerfacez emotes
        curl_setopt($ch, CURLOPT_URL, "https://api.frankerfacez.com/v1/set/global");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $globalResponse = curl_exec($ch);

        //user frankerfacez emotes data
        $userData = json_decode($userResponse, true);
        $userRoomSetId = $userData['room']['set'];

        //global frankerfacez emotes data
        $globalData = json_decode($globalResponse, true);
        $globalRoomSet = $globalData['room']['set'];

        //combine all frankerfacez emotes into one array
        $combindArr = array_merge(
            (array)$userData['sets'][$userRoomSetId]['emoticons'],
            (array)$globalData['sets']['3']['emoticons'],
            (array)$globalData['sets']['1532818']['emoticons'],
            (array)$globalData['sets']['1539687']['emoticons']
        );

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
    }

    curl_close($ch);
}
?>