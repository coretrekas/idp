<?php

namespace Coretrek\Idp;

use Illuminate\Support\Carbon;
use Illuminate\Support\ServiceProvider;

class LaravelServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('coretrek-idp.php'),
            ], 'config');
        }

        // If Socialite is installed we will automatically provide a coretrek provider.
        if (class_exists(\Laravel\Socialite\SocialiteServiceProvider::class)) {
            $socialite = $this->app->make(\Laravel\Socialite\Contracts\Factory::class);

            $socialite->extend('coretrek', fn () => $socialite->buildProvider(SocialiteProvider::class, config('services.coretrek')));
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'coretrek-idp');

        $this->app->singleton('coretrekSdk', function ($app) {
            if (! $credentials = $app['cache']->get('coretrek_sdk_credentials')) {
                $credentials = Token::make($app['config']->get('coretrek-idp.base_url'), $app['config']->get('coretrek-idp.client_id'), $app['config']->get('coretrek-idp.client_secret'), $app['config']->get('coretrek-idp.scopes'))->toArray();
                $app['cache']->remember('coretrek_sdk_credentials', Carbon::now()->addSeconds($credentials['expires_in']), fn () => $credentials);
            }

            return new Sdk(
                new Token($credentials['token_type'], $credentials['access_token'], $credentials['expires_in']),
                $app['config']->get('coretrek-idp.base_url'),
                $app['config']->get('coretrek-idp.locale')
            );
        });
    }
}
