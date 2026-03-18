<?php

require_once(__DIR__ . '/../vendor/autoload.php');

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use GuzzleHttp\Client;
use Dotenv\Dotenv;

// Suppress Deprecated warnings and prevent errors from being displayed to the user
error_reporting(E_ALL & ~E_DEPRECATED);
ini_set('display_errors', 0);

// Constants and Global Variables
define('logsPath', __DIR__ . "/../logs");
define('TWITCH_GRAPHQL_URL', 'https://gql.twitch.tv/gql');
define('TWITCH_CLIENT_ID', 'kimne78kx3ncx6brgo4mv6wki5h1ko');
define('TWITCH_SHA256HASH', '4f35f1ac933d76b1da008c806cd5546a7534dfaff83e033a422a81f24e5991b3');

$authFile = __DIR__ . '/.auth';
$ignoreKeywords = array("http", "https", "twitch.tv");

// Instantiate DotEnv
$dotenv = new Dotenv(__DIR__ . '/../', '.env');
$dotenv->load();

//Instantiate MonoLog as $logger
$logger = new Logger('Monolog');
try {
    $logger->pushHandler(new StreamHandler(logsPath . "/monolog.log", Logger::NOTICE));
} catch (Exception $e) {
    // Handle exception
}

//Gets clients real IP address - for logging and IP restriction
function getRealIpAddr()
{
    global $clientip;
    if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        $clientip = filter_var($_SERVER['HTTP_CF_CONNECTING_IP'], FILTER_VALIDATE_IP);
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $clientip = filter_var($_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP);
    } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $clientip = filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP);
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $clientip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
    }
    
    // If nothing valid was found
    if (!$clientip || !filter_var($clientip, FILTER_VALIDATE_IP)) {
        $clientip = "Client IP Not Found";
    }
    return $clientip;
}

// If using an IP allow list, check for it
$apiIPAllow = getenv('API_IP_ALLOW');

if ($apiIPAllow) {
    $ip = getRealIpAddr();
    $allowedIPs = explode(',', getenv('API_IP_ALLOW'));

    if (!in_array($ip, $allowedIPs)) {
        header('HTTP/1.1 403 Unauthorized');
        http_response_code(403);
        die();
    }
}

// If using an API key, check for it. Make sure X-Api-Key is set in the request headers
$apiKey = getenv('API_KEY');

if ($apiKey) {
    $http_headers = getallheaders();

    if (!isset($http_headers['X-Api-Key']) || $http_headers['X-Api-Key'] != $apiKey) {
        header('HTTP/1.1 403 Unauthorized');
        http_response_code(403);
        die();
    }
}

// If set, the API will only allow requests from the `API_ALLOWED_DOMAINS` list in the .env file.
$allowedDomainsEnv = getenv('API_ALLOWED_DOMAINS');

if ($allowedDomainsEnv) {
    $referrerDomain = parse_url($_SERVER['HTTP_REFERER'] ?? '', PHP_URL_HOST);
    $allowedDomains = explode(',', $allowedDomainsEnv);

    if (!in_array($referrerDomain, $allowedDomains)) {
        header('HTTP/1.1 403 Unauthorized');
        http_response_code(403);
        die();
    }
}

$current_token = trim((string)@file_get_contents($authFile));
$token_valid = false;

// If a token exists and is not older than 60 days, validate it.
if (!empty($current_token) && file_exists($authFile) && filemtime($authFile) > (time() - 5184000)) { // 5184000 seconds = 60 days
    try {
        $valClient = new Client();
        $valResponse = $valClient->request('GET', 'https://id.twitch.tv/oauth2/validate', [
            'headers' => ['Authorization' => 'OAuth ' . $current_token],
            'http_errors' => false
        ]);

        if ($valResponse->getStatusCode() == 200) {
            $token_valid = true;
        }
    } catch (\Exception $e) {
        // if validation fails, $token_valid remains false and a new token will be fetched.
    }
}

// Refresh oAuth Token automatically
if (!$token_valid) {
    $client = new Client();
    $url = "https://id.twitch.tv/oauth2/token";
    $params = [
        'client_id' => getenv('API_TWITCH_CLIENT_ID'),
        'client_secret' => getenv('API_TWITCH_SECRET'),
        'grant_type' => 'client_credentials'
    ];

    try {
        $response = $client->request('POST', $url, [
            'form_params' => $params,
            'http_errors' => false
        ]);

        $result = json_decode($response->getBody(), true);

        if ($response->getStatusCode() != 403) {
            $writeauthtoken = $result['access_token'];
        } else {
            $writeauthtoken = '';
        }

        if ($writeauthtoken > '') {
            // Write auth token to .auth file
            file_put_contents($authFile, $writeauthtoken);
        }
    } catch (\GuzzleHttp\Exception\RequestException $e) {
        // Handle request errors
        error_log('Request failed: ' . $e->getMessage());
    }
}

// Define AUTH_TOKEN after potential refresh
$authtoken = file_get_contents($authFile);
define('AUTH_TOKEN', trim($authtoken));