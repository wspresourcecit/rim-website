<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Only the API is meant to be called cross-origin (the admin panel and
    | any public site consuming this API). Origins are restricted to the
    | explicit allowlist below instead of '*' since Passport bearer tokens
    | are involved. Add production domains via CORS_ALLOWED_ORIGINS.
    |
    */

    'paths' => ['api/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
