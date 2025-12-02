<?php

namespace App\Commands;

use Illuminate\Support\Facades\Process;
use LaravelZero\Framework\Commands\Command;

class WatchCommand extends Command
{
    protected $signature = 'watch
        {--interval= : Interval in seconds between checks (default: from config)}
        {--all : Stage all changes before committing}
        {--no-auto-commit : Only notify about changes, do not auto-commit}';

    protected $description = 'Watch for git changes and automatically create commits';

    protected bool $running = true;

    protected string $lastCommitHash = '';

    public function handle(): int
    {
        if (! $this->isGitRepository()) {
            $this->error('Not a git repository.');

            return self::FAILURE;
        }

        $interval = (int) ($this->option('interval') ?? config('nimble.watch_interval', 30));
        $autoCommit = ! $this->option('no-auto-commit');

        $this->info("Watching for changes every {$interval} seconds...");
        $this->info('Press Ctrl+C to stop.');
        $this->newLine();

        $this->lastCommitHash = $this->getLastCommitHash();

        pcntl_async_signals(true);
        pcntl_signal(SIGINT, fn () => $this->running = false);
        pcntl_signal(SIGTERM, fn () => $this->running = false);

        while ($this->running) {
            $this->checkForChanges($autoCommit);
            sleep($interval);
        }

        $this->newLine();
        $this->info('Stopped watching.');

        return self::SUCCESS;
    }

    protected function isGitRepository(): bool
    {
        $result = Process::run('git rev-parse --is-inside-work-tree');

        return $result->successful() && trim($result->output()) === 'true';
    }

    protected function getLastCommitHash(): string
    {
        $result = Process::run('git rev-parse HEAD 2>/dev/null');

        return trim($result->output());
    }

    protected function hasUncommittedChanges(): bool
    {
        $result = Process::run('git status --porcelain');

        return ! empty(trim($result->output()));
    }

    protected function checkForChanges(bool $autoCommit): void
    {
        if (! $this->hasUncommittedChanges()) {
            return;
        }

        $this->info('['.now()->format('H:i:s').'] Changes detected!');

        if ($autoCommit) {
            $this->createCommit();
        } else {
            $this->showChangeSummary();
        }
    }

    protected function showChangeSummary(): void
    {
        $result = Process::run('git status --short');
        $this->line($result->output());
    }

    protected function createCommit(): void
    {
        $this->info('Creating commit...');

        $stageAll = $this->option('all') || config('nimble.auto_stage', true);

        $result = $this->call('commit', [
            '--all' => $stageAll,
        ]);

        if ($result === self::SUCCESS) {
            $this->lastCommitHash = $this->getLastCommitHash();
        }
    }
}
