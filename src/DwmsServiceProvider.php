<?php

namespace Booni3\Dwms\Laravel;

use Booni3\Dwms\Dwms;
use Booni3\Linnworks\Linnworks;
use Cache\Adapter\Predis\PredisCachePool;
use Cache\Bridge\SimpleCache\SimpleCacheBridge;
use Illuminate\Support\ServiceProvider;

class DwmsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/dwms.php' => config_path('dwms.php'),
        ], 'dwms');
    }

    /**
     * Get the services provided by the provider.
     * This will defer loading of the service until it is requested.
     *
     * @return array
     */
    public function provides()
    {
        return [Dwms::class];
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom( __DIR__.'/../config/dwms.php', 'dwms');
        $this->app->singleton(Dwms::class, function ($app) {

            $config = $app->make('config');

            $baseUri = $config->get('dwms.baseUri');
            $userName = $config->get('dwms.userName');
            $password = $config->get('dwms.password');
            $secret = $config->get('dwms.secret');
            $redisConnection = $config->get('dwms.redisConnection');

            return new Dwms($baseUri, $userName, $password, $secret, $this->getSimpleCache($redisConnection));
        });
        $this->app->alias(Dwms::class, 'dwms');

    }

    /**
     * Setup PSR-16 cache from laravel redis cache
     *
     * @param $redisConnection
     * @return SimpleCacheBridge
     */
    protected function getSimpleCache($redisConnection)
    {
        $client = \Redis::connection($redisConnection ?: 'default');
        $pool = new PredisCachePool($client->client());
        $simpleCache = new SimpleCacheBridge($pool);
        return $simpleCache;
    }


}
