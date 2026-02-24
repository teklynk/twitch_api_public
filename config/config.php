<?php
error_reporting(E_ALL & ~E_WARNING);

ini_set('display_errors', 0);

define('logsPath', __DIR__ . "/../logs");

$authFile = __DIR__ . '/.auth';

$authFileModDate = date('Y-m-d', filemtime($authFile));
$date_now = date('Y-m-d');
$authtoken = '';

require_once(__DIR__ . '/../vendor/autoload.php');

// Instantiate DotEnv
$dotenv = new Dotenv\Dotenv(__DIR__ . '/../', '.env');
$dotenv->load();

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

use GuzzleHttp\Client;

//Instantiate MonoLog as $logger
$logger = new Logger('Monolog');
try {
    $logger->pushHandler(new StreamHandler(logsPath . "/monolog.log", Logger::NOTICE));
} catch (Exception $e) {
}
//Example usage
// Monolog - create a log channel
//$logger = new Logger('Monolog');
//$logger->pushHandler(new StreamHandler(logsPath . 'monolog2.log', Logger::NOTICE));

// Use it like this in your code:
//$logger->warning('Foo');
//$logger->error('Bar');
//$logger->notice('Test');
//$logger->notice('FAILED Login - ' . $user_name . ' - ' . getRealIpAddr());

//Gets clients real IP address - for logging and IP restriction
function getRealIpAddr()
{
    global $clientip;
    if (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $clientip = filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP);
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $clientip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $clientip = filter_var($_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP);
    } elseif (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        $clientip = filter_var($_SERVER['HTTP_CF_CONNECTING_IP'], FILTER_VALIDATE_IP);
    } elseif (filter_var($clientip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) || filter_var($clientip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
        $clientip = "Client IP Not Found";
    }

    return $clientip;
}

// If using an IP allow list, check for it
$apiIPAllow = getenv('API_IP_ALLOW') > '' ? true : false;

if ($apiIPAllow) {
    $ip = getRealIpAddr();
    $allowedIPs = explode(',', getenv('API_IP_ALLOW'));

    if (!in_array($ip, $allowedIPs)) {
        header('Content-type: application/json');
        echo json_encode(['error' => 'Invalid IP Address']);
        exit();
    }
}

// If using an API key, check for it. Make sure X-Api-Key is set in the request headers
$apiKey = getenv('API_KEY') > '' ? true : false;

if ($apiKey) {
    $http_headers = getallheaders();

    if (!isset($http_headers['X-Api-Key']) || $http_headers['X-Api-Key'] != $apiKey) {
        header('Content-type: application/json');
        echo json_encode(['error' => 'Invalid or missing API Key']);
        exit();
    }
}

$current_token = @file_get_contents($authFile);
$token_valid = true;

// Validate token if it exists and is not expired by date
if (!empty($current_token) && strtotime($date_now) <= strtotime($authFileModDate)) {
    try {
        $valClient = new Client();
        $valResponse = $valClient->request('GET', 'https://id.twitch.tv/oauth2/validate', [
            'headers' => ['Authorization' => 'OAuth ' . $current_token],
            'http_errors' => false
        ]);

        if ($valResponse->getStatusCode() != 200) {
            $token_valid = false;
        }
    } catch (\Exception $e) {
    }
}

// Refresh oAuth Token automatically
if (!$token_valid || strtotime($date_now) > strtotime($authFileModDate) || empty($current_token)) {
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

$ignoreKeywords = array("http", "https", "twitch.tv");

$authtoken = file_get_contents($authFile);

define('AUTH_TOKEN', trim($authtoken));

// Only change these if you know what you're doing
define('TWITCH_GRAPHQL_URL', 'https://gql.twitch.tv/gql');
define('TWITCH_CLIENT_ID', 'kimne78kx3ncx6brgo4mv6wki5h1ko');
define('TWITCH_SHA256HASH', '4f35f1ac933d76b1da008c806cd5546a7534dfaff83e033a422a81f24e5991b3');