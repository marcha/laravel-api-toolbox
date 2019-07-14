<?php

namespace Erpmonster\System;

use Illuminate\Routing\Router;
use Erpmonster\System\LaravelRouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */

    public function boot()
    {
        $path = realpath(__DIR__ . '/../../config/erpmonster.php');

        $this->publishes([$path => config_path('erpmonster.php')], 'config');
        $this->mergeConfigFrom($path, 'erpmonster');
        parent::boot();
    }



    /**
     * Define the routes for the application.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function map(Router $router)
    {
        $config = config('erpmonster');

        $middleware = $config['protection_middleware'];

        $highLevelParts = array_map(function ($namespace) {
            return glob(sprintf('%s%s*', $namespace, DIRECTORY_SEPARATOR), GLOB_ONLYDIR);
        }, $config['namespaces']);

        foreach ($highLevelParts as $part => $partComponents) {
            foreach ($partComponents as $componentRoot) {
                $component = substr($componentRoot, strrpos($componentRoot, DIRECTORY_SEPARATOR) + 1);

                $namespace = sprintf(
                    '%s\\%s\\Controllers',
                    $part,
                    $component
                );

                $fileNames = [
                    'routes' => true,
                    'routes_protected' => true,
                    'routes_public' => false,
                ];

                foreach ($fileNames as $fileName => $protected) {
                    $path = sprintf('%s/%s.php', $componentRoot, $fileName);

                    if (!file_exists($path)) {
                        continue;
                    }

                    $router->group([
                        // TODO: Resolve domains
                        'domain' => env('API_URL', ''),
                        'middleware' => $protected ? $middleware : [],
                        'namespace'  => $namespace,
                        // TODO: Resolve prefixes ..maybe in config...
                        // 'prefix' => 'api/v1/'
                    ], function ($router) use ($path) {

                        require $path;
                    });
                }
            }
        }
    }
}
