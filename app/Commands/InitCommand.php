<?php

namespace App\Commands;

use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;

class InitCommand extends Command
{
    protected $signature = 'init
        {--force : Overwrite existing configuration}';

    protected $description = 'Initialize nimble configuration in the current directory';

    public function handle(): int
    {
        $configPath = getcwd().'/.nimble.json';

        if (File::exists($configPath) && ! $this->option('force')) {
            $this->warn('Configuration already exists. Use --force to overwrite.');

            return self::FAILURE;
        }

        $config = [
            'watch_interval' => 30,
            'auto_stage' => true,
            'ai_provider' => 'claude',
            'commit_style' => 'conventional',
            'claude' => [
                'timeout' => 600000,
                'skip_permissions' => true,
            ],
            'system_prompt' => config('nimble.system_prompt'),
        ];

        File::put($configPath, json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        $this->info('Created .nimble.json configuration file.');

        return self::SUCCESS;
    }
}
