<?php

namespace ValidatorLib\ServiceProviders;

use Illuminate\Support\ServiceProvider;

class ValidatorLibServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->publishes(
            [__DIR__ . "/../../config/validator-lib-config.php" => config_path("validator-lib-config.php") ] ,
            'validator-lib-config'
        );

    }

}
