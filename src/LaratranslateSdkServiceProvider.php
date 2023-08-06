<?php

namespace Jeanlucnguyen\LaratranslateSdk;

use Illuminate\Support\ServiceProvider;
use Jeanlucnguyen\LaratranslateSdk\Commands\AddMissingKeys;
use Jeanlucnguyen\LaratranslateSdk\Commands\MissingKeys;
use Jeanlucnguyen\LaratranslateSdk\Commands\TranslateMissingKeys;

class LaratranslateSdkServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/laratranslate.php', 'laratranslate',
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                AddMissingKeys::class,
                MissingKeys::class,
                TranslateMissingKeys::class,
            ]);
        }
    }
}
