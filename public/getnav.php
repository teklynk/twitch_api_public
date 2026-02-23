<?php
require_once(__DIR__ . '/../config/config.php');

//Get Twitch Tools nav links

$cacheTTL = 43200; // 12 hours

$cached = null;
$mem = null;
if (class_exists('Memcached')) {
    $mem = new Memcached();
    if (gethostbyname('memcached') !== 'memcached') {
        $mem->addServer("memcached", 11211);
    } else {
        $mem->addServer("127.0.0.1", 11211);
    }
    $cacheKey = 'twitch_nav_links';
    $cached = $mem->get($cacheKey);
}

if ($cached) {
    header('Content-type: application/json');
    echo $cached;
    exit;
}

if ($mem) {
    ob_start();
}

$navJson = file_get_contents(__DIR__ . '/nav.json');

header('Content-type: application/json');

if ($navJson === false) {
    echo json_encode([]);
} else {
    echo $navJson;
}

if ($mem) {
    $output = ob_get_flush();
    $mem->set($cacheKey, $output, $cacheTTL);
}