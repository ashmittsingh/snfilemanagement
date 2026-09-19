<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'bunny' => [

        // Bunny Stream
        'library_id' => env('BUNNY_STREAM_LIBRARY_ID'),

        'api_key' => env('BUNNY_STREAM_API_KEY'),

        'webhook_secret' => env(
            'BUNNY_STREAM_WEBHOOK_SECRET'
        ),

        'host' => env(
            'BUNNY_STREAM_HOSTNAME',
            'https://video.bunnycdn.com'
        ),

        'embed' => env(
            'BUNNY_STREAM_EMBED',
            'https://iframe.mediadelivery.net/embed'
        ),

        'cdn_hostname' => env(
            'BUNNY_CDN_HOSTNAME'
        ),

        // Bunny Storage
        'storage' => [

            'zone' => env(
                'BUNNY_STORAGE_ZONE'
            ),

            'api_key' => env(
                'BUNNY_STORAGE_API_KEY'
            ),

            'host' => env(
                'BUNNY_STORAGE_HOST',
                'https://storage.bunnycdn.com'
            ),

            'cdn_hostname' => env(
                'BUNNY_STORAGE_CDN_HOSTNAME'
            ),

        ],

    ],
];
