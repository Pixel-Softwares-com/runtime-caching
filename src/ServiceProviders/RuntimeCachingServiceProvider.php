<?php

namespace RuntimeCaching\ServiceProviders;

use Illuminate\Support\ServiceProvider;

class RuntimeCachingServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->publishes(
            [__DIR__ . "/../../config/runtime-cache-config.php" => config_path("runtime-cache-config.php") ] ,
            'runtime-cache-config'
        );

    }

}
