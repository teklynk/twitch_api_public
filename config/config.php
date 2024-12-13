<?php
$authFile = __DIR__ . '/.auth';

$authFileModDate = date('Y-m-d', filemtime($authFile));
$date_now = date('Y-m-d');
$authtoken = '';

// Note: The files are hidden from the file system
define('CLIENT_ID', trim(file_get_contents(__DIR__ . '/.client')));
define('CLIENT_SECRET', trim(file_get_contents(__DIR__ . '/.secret')));

// Refresh oAuth Token automatically
if (strtotime($date_now) > strtotime($authFileModDate) || file_get_contents($authFile) == '') {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://id.twitch.tv/oauth2/token?client_id=" . CLIENT_ID . "&client_secret=" . CLIENT_SECRET . "&grant_type=client_credentials");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $Response = curl_exec($ch);
    $Result = json_decode($Response, true);

    curl_close($ch);

    if ($Result['status'] != 403) {
        $writeauthtoken = $Result['access_token'];
    } else {
        $writeauthtoken = '';
    }

    if ($writeauthtoken > '') {
        // Write auth token to .auth file
        file_put_contents($authFile, $writeauthtoken);
    }
}

$authtoken = file_get_contents($authFile);

define('AUTH_TOKEN', trim($authtoken));

define('TWITCH_GRAPHQL_URL', 'https://gql.twitch.tv/gql');

define('TWITCH_CLIENT_ID', 'kimne78kx3ncx6brgo4mv6wki5h1ko');

define('TWITCH_SHA256HASH', '6fd3af2b22989506269b9ac02dd87eb4a6688392d67d94e41a6886f1e9f5c00f');

?>