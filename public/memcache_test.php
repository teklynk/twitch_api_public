<?php

/**
 * Example of implementing Memcached.
 * Use this pattern to cache responses from the Twitch API to reduce rate limit usage.
 */

// 1. Check if the extension is available
if (!class_exists('Memcached')) {
    die("Error: The php-memcached extension is not installed or enabled.");
}

// 2. Initialize Memcached and add the server
$memcached = new Memcached();
$memcached->addServer('memcached', 11211);

// 3. Define a unique key for the data you want to cache
$cacheKey = 'twitch_api_status_example';

// 4. Try to retrieve the data from cache
$data = $memcached->get($cacheKey);

if ($memcached->getResultCode() === Memcached::RES_NOTFOUND) {
    echo "Cache MISS: Fetching data from source...\n";

    // Simulate an expensive API request (e.g., calling Twitch API via Guzzle)
    $data = [
        'status' => 'online',
        'viewers' => 1500,
        'timestamp' => time()
    ];

    // 5. Store in cache for 5 minutes (300 seconds)
    $memcached->set($cacheKey, $data, 300);
} else {
    echo "Cache HIT: Serving data from memory.\n";
}

print_r($data);