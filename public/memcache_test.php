<?php

/**
 * Example of implementing Memcached.
 * Use this pattern to cache responses from the Twitch API to reduce rate limit usage.
 */

// Initialize Memcached and add the server
if (class_exists('Memcached')) {
    $memcached = new Memcached();
    // Check if running in Docker (memcached hostname resolves)
    if (gethostbyname('memcached') !== 'memcached') {
        $memcached->addServer("memcached", 11211);
    } else {
        $memcached->addServer("127.0.0.1", 11211);
    }
} else {
    echo "Memcached is not found.";
}

$cacheTTL = 300; // Cache TTL in seconds

// Define a unique key for the data you want to cache
$cacheKey = 'twitch_api_memcached_test';

// Try to retrieve the data from cache
$data = $memcached->get($cacheKey);

if ($memcached->getResultCode() === Memcached::RES_NOTFOUND) {
    echo "Cache MISS: Fetching data from source...\n";

    // Simulate API request
    $data = [
        'status' => 'online',
        'viewers' => 1500,
        'timestamp' => time()
    ];

    // Store in cache 
    $memcached->set($cacheKey, $data, $cacheTTL);
} else {
    echo "Cache HIT: Serving data from memory.\n";
}

print_r($data);