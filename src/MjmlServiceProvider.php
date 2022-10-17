<?php

namespace Vercoutere\LaravelMjml;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Vercoutere\LaravelMjml\MjmlRenderer;
use Vercoutere\LaravelMjml\Render\ApiClient;
use Vercoutere\LaravelMjml\Render\MjmlClient;
use Vercoutere\LaravelMjml\Render\LocalClient;

class MjmlServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config.php', 'mjml');

        $this->app->singleton(MjmlRenderer::class);

        $this->app->bind(MjmlClient::class, function (Application $app) {
            return $app->make(Strategy::from(config('mjml.strategy'))->rendererClass());
        });

        $this->app->when(ApiClient::class)
            ->needs('$applicationId')
            ->giveConfig('mjml.api_credentials.application_id');

        $this->app->when(ApiClient::class)
            ->needs('$secretKey')
            ->giveConfig('mjml.api_credentials.secret_key');

        $this->app->when(LocalClient::class)
            ->needs('$binaryPath')
            ->giveConfig('mjml.binary_path');

        $this->app->when(LocalClient::class)
            ->needs('$nodePath')
            ->giveConfig('mjml.node_path');
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/config.php' => config_path('mjml.php'),
        ]);
    }
}
