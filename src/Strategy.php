<?php

namespace Vercoutere\LaravelMjml;

use Vercoutere\LaravelMjml\Render\ApiClient;
use Vercoutere\LaravelMjml\Render\LocalClient;

enum Strategy: String
{
    case API = 'api';
    case LOCAL = 'local';

    public function rendererClass()
    {
        return match ($this) {
            self::API => ApiClient::class,
            self::LOCAL => LocalClient::class,
        };
    }
}
