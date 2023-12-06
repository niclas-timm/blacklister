<?php

return [
    // The absolute path to the blacklist.json file.
    'blacklist_path' => storage_path('framework/blacklist.json'),

    // If true, the blacklist will be cached for some amount of time (see below.)
    'enable_cache' => true,

    // The cache key under which the blacklist data will be cached.
    'cache_key' => 'blacklister',

    // The time the blacklist should be cached.
    'cache_ttl' => 60 * 24 * 4,

    // If true, a cookie will be set if the validation fails.
    // The user will fail all following blacklister validations
    // for as long as the cookie is valid.
    'enable_cookies' => false,

    // The name of the cookie that will be set if the validation fails
    // and cookies are enabled.
    'cookie_name' => 'blacklister',

    // The time the blacklist cookie will be valid.
    'cookie_ttl' => 60 * 24 * 30,

    // The validation message presented to the user when the blacklist validation fails.
    'validation_message' => 'The value is not allowed. Please use another one.',
];