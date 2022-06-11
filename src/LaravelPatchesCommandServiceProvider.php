<?php

namespace Vswteam\LaravelPatchesCommand;

use Illuminate\Support\ServiceProvider;
use Vswteam\LaravelPatchesCommand\Commands\PatchesCommand;

class LaravelPatchesCommandServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/laravel-patches-command.php' => config_path('laravel-patches-command.php'),
            ], 'config');

            $migrationFileName = 'create_laravel_patches_command_table.php';
            if (! $this->migrationFileExists($migrationFileName)) {
                $this->publishes([
                    __DIR__ . "/../database/migrations/{$migrationFileName}.stub" => database_path('migrations/' . date('Y_m_d_His', time()) . '_' . $migrationFileName),
                ], 'migrations');
            }

            $this->commands([
                PatchesCommand::class,
            ]);
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/laravel-patches-command.php', 'laravel-patches-command');
    }

    public static function migrationFileExists(string $migrationFileName): bool
    {
        $len = strlen($migrationFileName);
        foreach (glob(database_path("migrations/*.php")) as $filename) {
            if ((substr($filename, -$len) === $migrationFileName)) {
                return true;
            }
        }

        return false;
    }
}
