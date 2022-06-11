<?php

namespace Vswteam\LaravelPatchesCommand\Commands;

use Illuminate\Console\Command;
use RuntimeException;
use Symfony\Component\Process\Process;
use Vswteam\LaravelPatchesCommand\Models\Patches;

class PatchesCommand extends Command
{
    /** 指令 */
    public $signature = 'patches:run';

    /** 描述 */
    public $description = 'Run Patches command only once';

    /** 執行 */
    public function handle()
    {
        $patches = config('laravel-patches-command.patches');

        if (empty($patches)) {
            $this->info('The patches array is empty now, we have nothing to do.');

            return true;
        }

        $this->process($patches);

        $this->line('All command executed.');
    }

    /**
     * 執行補丁
     *
     * 1. 透過資料庫，取得已執行補丁紀錄
     * 2. 比對補丁是否存在
     * 3. 執行補丁
     * 4. 新增補丁紀錄
     *
     * @param $patches
     */
    public function process($patches)
    {
        $processedItems = Patches::all();

        foreach ($patches as $command => $description) {
            $count = $processedItems->where('command', '=', $command)
                        ->where('description', '=', $description)
                        ->count();
            if ($count) {
                continue;
            }

            try {
                $this->info('');
                $this->info('Start execute command: ' . $command);

                $this->executeCommand($command);

                $this->info('Execute command ' . $command . ' Success.');
                $this->info('');

                Patches::create([
                    'command' => $command,
                    'description' => $description,
                ]);
            } catch (\Exception $e) {
                var_dump($e->getMessage());
                $this->error($e->getMessage());
            }
        }
    }

    /**
     * 執行指令
     *
     * @param $command
     * @throws \Exception
     */
    public function executeCommand($command)
    {
        if (strpos($command, '!') === false) {
            $this->execArtisanCommand($command);
        } else {
            $this->execExternalCommand(substr($command, 1));
        }
    }

    /**
     * 執行 Artisan Command
     *
     * @param string $command
     * @return void
     */
    protected function execArtisanCommand(string $command)
    {
        list($commandName, $arguments) = $this->splitCommand($command);

        $this->call($commandName, $arguments);

        $this->info('Command: '. $commandName);
        $this->info('Arguments: '. json_encode($arguments));
    }

    /**
     * 執行 External Command
     *
     * @param string $command
     * @return void
     */
    protected function execExternalCommand(string $command)
    {
        $process = Process::fromShellCommandline($command);
        $process->setTimeout(null);

        $isVerbose = true;
        $process->run($isVerbose ? function ($type, $buffer) {
            if (Process::ERR === $type) {
                $this->error($buffer);
            } else {
                $this->line($buffer);
            }
        } : null);

        $error = $process->getErrorOutput();
        $exitCode = $process->getExitCode();

        if ($error and $exitCode > 0) {
            throw new RuntimeException(trim($error));
        }

        $this->info('Command: '. $command);
        $this->info($process->getOutput());
    }

    /**
     * List command name and arguments from command string.
     *
     * @param $command
     * @return array
     */
    protected function splitCommand($command)
    {
        $collect = collect(explode(' ', $command));
        $commandName = $collect->first();
        $collect->forget(0);

        $filtered = $collect->filter(function ($item) {
            return $item !== '';
        })->toArray();

        $filtered = array_values($filtered);

        $arguments = [];
        foreach ($filtered as $argument) {
            $argument = trim($argument, '{');
            $argument = trim($argument, '}');
            $argument = explode(':', $argument);
            $argument = [$argument[0] => $argument[1]];
            $arguments = array_merge($arguments, $argument);
        }

        return [$commandName, $arguments];
    }
}
