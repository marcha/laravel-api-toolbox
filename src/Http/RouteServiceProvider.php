<?php

namespace Erpmonster\Http;

use Illuminate\Routing\Router;
use Erpmonster\System\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $router = $this->app->make(Router::class);

        parent::boot($router);
    }
}
