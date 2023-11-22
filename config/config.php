<?php

return [
    // The absolute path to the blacklist.json file.
    'blacklist_path' => storage_path('framework/blacklist.json'),

    // If true, the blacklist will be cached for some amount of time (see below.)
    'enable_cache' => true,

    // The cache key under which the blacklist data will be cached.
    'cache_key' => 'blacklister',

    // The time the blacklist should be cached.
    'cache_ttl' => '',


];