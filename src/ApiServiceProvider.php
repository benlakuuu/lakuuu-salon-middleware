<?php

namespace Lakuuu\SalonApi;

use Illuminate\Support\ServiceProvider;

class ApiServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(SalonTreatment::class, function () {
            return new SalonTreatment();
        });
    }
}