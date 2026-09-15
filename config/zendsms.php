<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Zend SMS API Key
    |--------------------------------------------------------------------------
    |
    | Create and manage keys in the Zend SMS customer portal under Developer API.
    | Keys look like: sk_…
    |
    */

    'api_key' => env('ZENDSMS_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | API Base URL
    |--------------------------------------------------------------------------
    */

    'base_url' => env('ZENDSMS_BASE_URL', 'https://api.zendsms.com'),

    /*
    |--------------------------------------------------------------------------
    | Default Sender ID
    |--------------------------------------------------------------------------
    |
    | Optional default CLI / sender ID used by helpers. You can still pass
    | sender_id explicitly on each send.
    |
    */

    'default_sender_id' => env('ZENDSMS_SENDER_ID'),

    /*
    |--------------------------------------------------------------------------
    | HTTP Timeout (seconds)
    |--------------------------------------------------------------------------
    */

    'timeout' => (int) env('ZENDSMS_TIMEOUT', 30),

];
