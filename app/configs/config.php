<?php
/**
 * Config file
 **/

return [
    'appName' => 'vascoframework',
    'apiApp' => true,
    'debug' => true,
    'db' => true,
    'defaultLanguage' => 'en',
    'hash' => 'ripemd160',
    'memcachedPort' => 11211,
    /* session */
    'sessionStart' => true,
    'sessionConfig' => [
        'cookie_lifetime' => 0
    ],
    /* token */
    'token_bytes' => 32,
    'token_expired_time' => 600,
    'user_token_expired_time' => 600
];
