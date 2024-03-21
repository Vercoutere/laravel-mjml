<?php

namespace Vercoutere\LaravelMjml;

use Illuminate\View\DynamicComponent;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Vercoutere\LaravelMjml\MjmlRenderer;
use Illuminate\View\Engines\CompilerEngine;
use Vercoutere\LaravelMjml\Commands\ViewCacheCommand;
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

        $this->registerMjmlCompiler();
        $this->registerMjmlEngine();

        $this->app['view']->addExtension('mjml.blade.php', 'mjml');
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/config.php' => config_path('mjml.php'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                ViewCacheCommand::class,
            ]);
        }
    }

    /**
     * Register the MJML compiler implementation.
     *
     * @return void
     */
    public function registerMjmlCompiler()
    {
        $this->app->singleton('mjml.compiler', function ($app) {
            return tap(new MjmlCompiler(
                $app['files'],
                $app['config']['view.compiled'],
                $app['config']->get('view.relative_hash', false) ? $app->basePath() : '',
                $app['config']->get('view.cache', true),
                $app['config']->get('view.compiled_extension', 'php'),
            ), function ($compiler) {
                $compiler->setClient($this->app->make(MjmlClient::class));
                $compiler->component('dynamic-component', DynamicComponent::class);
            });
        });
    }

    /**
     * Register the MJML engine implementation.
     *
     * @return void
     */
    public function registerMjmlEngine()
    {
        $this->app['view.engine.resolver']->register('mjml', function () {
            $compiler = new CompilerEngine($this->app['mjml.compiler'], $this->app['files']);

            $this->app->terminating(static function () use ($compiler) {
                $compiler->forgetCompiledOrNotExpired();
            });

            return $compiler;
        });
    }
}
