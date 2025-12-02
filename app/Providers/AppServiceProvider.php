<?php

namespace App\Providers;

use App\Strategies\CommitStrategyFactory;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadLocalConfiguration();
    }

    public function register(): void
    {
        $this->app->singleton(CommitStrategyFactory::class);
    }

    protected function loadLocalConfiguration(): void
    {
        $configPath = getcwd().'/.nimble.json';

        if (! File::exists($configPath)) {
            return;
        }

        $localConfig = json_decode(File::get($configPath), true);

        if (! is_array($localConfig)) {
            return;
        }

        foreach ($localConfig as $key => $value) {
            if ($key === 'claude' && is_array($value)) {
                foreach ($value as $subKey => $subValue) {
                    config()->set("nimble.claude.{$subKey}", $subValue);
                }
            } else {
                config()->set("nimble.{$key}", $value);
            }
        }
    }
}
