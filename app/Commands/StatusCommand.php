<?php

namespace App\Commands;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use LaravelZero\Framework\Commands\Command;

class StatusCommand extends Command
{
    protected $signature = 'status';

    protected $description = 'Show current nimble configuration and git status';

    public function handle(): int
    {
        $this->showConfiguration();
        $this->newLine();
        $this->showGitStatus();

        return self::SUCCESS;
    }

    protected function showConfiguration(): void
    {
        $this->info('Configuration:');

        $hasLocalConfig = File::exists(getcwd().'/.nimble.json');

        $this->table(['Setting', 'Value', 'Source'], [
            ['Watch Interval', config('nimble.watch_interval').'s', $hasLocalConfig ? 'local' : 'default'],
            ['Auto Stage', config('nimble.auto_stage') ? 'yes' : 'no', $hasLocalConfig ? 'local' : 'default'],
            ['AI Provider', config('nimble.ai_provider'), $hasLocalConfig ? 'local' : 'default'],
            ['Commit Style', config('nimble.commit_style'), $hasLocalConfig ? 'local' : 'default'],
            ['Claude Timeout', config('nimble.claude.timeout').'ms', $hasLocalConfig ? 'local' : 'default'],
        ]);
    }

    protected function showGitStatus(): void
    {
        if (! $this->isGitRepository()) {
            $this->warn('Not a git repository.');

            return;
        }

        $this->info('Git Status:');
        $result = Process::run('git status --short');
        $output = trim($result->output());

        if (empty($output)) {
            $this->line('  No changes detected.');
        } else {
            $this->line($output);
        }
    }

    protected function isGitRepository(): bool
    {
        $result = Process::run('git rev-parse --is-inside-work-tree');

        return $result->successful() && trim($result->output()) === 'true';
    }
}
