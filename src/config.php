<?php

return [
    /**
     * The strategy to use when rendering MJML.
     * This can either be api or local.
     */
    'strategy' => env('MJML_STRATEGY', 'api'),

    /**
     * The API credentials used to authenticate when
     * using the API strategy.
     */
    'api_credentials' => [
        'application_id' => env('MJML_APP_ID'),
        'secret_key' => env('MJML_SECRET_KEY'),
    ],

    /**
     * The path to the MJML binary when using the binary
     * strategy.
     */
    'binary_path' => env('MJML_BINARY_PATH', base_path('node_modules/.bin/mjml')),
];
