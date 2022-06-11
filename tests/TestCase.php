<?php

namespace Vswteam\LaravelPatchesCommand\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Vswteam\LaravelPatchesCommand\LaravelPatchesCommandServiceProvider;

class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate', [
            '--database' => 'sqlite',
            '--realpath' => realpath(__DIR__.'/../database/factories'),
        ]);
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelPatchesCommandServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        include_once __DIR__ . '/../database/migrations/create_laravel_patches_command_table.php.stub';
        (new \CreateLaravelPatchesCommandTable())->up();
    }
}
