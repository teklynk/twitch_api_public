<?php
if (!defined('ALLOW_INC')) {
    die('Direct access not permitted');
}

$authFile = __DIR__ . '/.auth';

$authFileModDate = date('Y-m-d', filemtime($authFile));
$date_now = date('Y-m-d');
$authtoken = '';

// Note: The files are hidden from the file system
define('CLIENT_ID', file_get_contents(__DIR__ . '/.client'));
define('CLIENT_SECRET', file_get_contents(__DIR__ . '/.secret'));
define('API_DOMAIN', file_get_contents(__DIR__ . '/.domain'));

// Refresh oAuth Token automatically
if ($date_now != $authFileModDate) {

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://id.twitch.tv/oauth2/token?client_id=" . CLIENT_ID . "&client_secret=" . CLIENT_SECRET . "&grant_type=client_credentials");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $Response = curl_exec($ch);
    $Result = json_decode($Response, true);

    curl_close($ch);

    $writeauthtoken = $Result['access_token'];

    // Write auth token to .auth file
    file_put_contents($authFile, $writeauthtoken);
}

$authtoken = file_get_contents($authFile);

define('AUTH_TOKEN', $authtoken);

?>