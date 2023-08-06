<?php

namespace Jeanlucnguyen\LaratranslateSdk\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Jeanlucnguyen\LaratranslateSdk\LaratranslateSdkServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Jeanlucnguyen\\LaratranslateSdk\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            LaratranslateSdkServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        /*
        $migration = include __DIR__.'/../database/migrations/create_laratranslate-sdk_table.php.stub';
        $migration->up();
        */
    }
}
