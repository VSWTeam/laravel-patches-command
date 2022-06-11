<?php

namespace Vswteam\LaravelPatchesCommand\Tests;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;

class PatchesCommandTest extends TestCase
{
    /** @test */
    public function test_exec_external_command()
    {
        /** arrange */
        $testFilePath = $this->app->basePath('test-external.txt');
        $command = '!echo "test output" > ' . $testFilePath;
        $description = 'Add file';

        Config::set('laravel-patches-command.patches', [
            $command => $description,
        ]);

        $this->assertFileDoesNotExist($testFilePath);

        /** act */
        Artisan::call('patches:run');

        /** assert */
        $this->assertFileExists($testFilePath);
        $this->assertDatabaseCount('patches_command', 1);
        $this->assertDatabaseHas('patches_command', [
            'command' => $command,
            'description' => $description,
        ]);

        unlink($testFilePath);
    }

    /** @test */
    public function test_exec_external_command_only_once()
    {
        /** arrange */
        $testFilePath = $this->app->basePath('test-external.txt');
        $command = '!echo "test output" > ' . $testFilePath;
        $description = 'Add file';

        Config::set('laravel-patches-command.patches', [
            $command => $description,
        ]);

        $this->assertFileDoesNotExist($testFilePath);

        /** act */
        Artisan::call('patches:run');
        Artisan::call('patches:run');

        /** assert */
        $this->assertFileExists($testFilePath);
        $this->assertDatabaseCount('patches_command', 1);
        $this->assertDatabaseHas('patches_command', [
            'command' => $command,
            'description' => $description,
        ]);

        unlink($testFilePath);
    }

    /** @test */
    public function test_exec_artisan_command()
    {
        /** arrange */
        $command = 'list';
        $description = 'Test artisan command list';

        Config::set('laravel-patches-command.patches', [
            $command => $description,
        ]);

        /** act */
        Artisan::call('patches:run');

        /** assert */
        $this->assertDatabaseCount('patches_command', 1);
        $this->assertDatabaseHas('patches_command', [
            'command' => $command,
            'description' => $description,
        ]);
    }
}
