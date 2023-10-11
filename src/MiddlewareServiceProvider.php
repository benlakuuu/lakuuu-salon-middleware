<?php

namespace Lakuuu\SalonMiddleware;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class MiddlewareServiceProvider extends ServiceProvider
{
    public function boot(Router $router)
    {
        $router->pushMiddlewareToGroup('api', SalonMiddleware::class);
    }
}