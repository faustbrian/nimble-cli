<?php

namespace App\Commands;

use App\Enums\CommitStyle;
use App\Strategies\CommitStrategyFactory;
use Illuminate\Support\Facades\Process;
use LaravelZero\Framework\Commands\Command;

class CommitCommand extends Command
{
    protected $signature = 'commit
        {--all : Stage all changes before committing}
        {--message= : Skip AI and use provided commit message}
        {--wip : Quick work-in-progress commit}
        {--style= : Commit style (conventional)}
        {--provider= : AI provider to use (claude)}
        {--dry-run : Show what would be committed without actually committing}';

    protected $description = 'Create a commit with an AI-generated commit message';

    public function __construct(
        protected CommitStrategyFactory $strategyFactory
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        if (! $this->isGitRepository()) {
            $this->error('Not a git repository.');

            return self::FAILURE;
        }

        if ($this->option('all') || config('nimble.auto_stage')) {
            $this->stageAllChanges();
        }

        if (! $this->hasStagedChanges()) {
            $this->warn('No staged changes to commit.');

            return self::SUCCESS;
        }

        if ($this->option('dry-run')) {
            $this->showStagedChanges();

            return self::SUCCESS;
        }

        if ($this->option('wip')) {
            return $this->createWipCommit();
        }

        if ($message = $this->option('message')) {
            return $this->createManualCommit($message);
        }

        return $this->createCommit();
    }

    protected function isGitRepository(): bool
    {
        $result = Process::run('git rev-parse --is-inside-work-tree');

        return $result->successful() && trim($result->output()) === 'true';
    }

    protected function stageAllChanges(): void
    {
        $this->info('Staging all changes...');
        Process::run('git add -A');
    }

    protected function hasStagedChanges(): bool
    {
        $result = Process::run('git diff --cached --quiet');

        return ! $result->successful();
    }

    protected function showStagedChanges(): void
    {
        $this->info('Staged changes:');
        $result = Process::run('git diff --cached --stat');
        $this->line($result->output());
    }

    protected function createWipCommit(): int
    {
        $this->info('Creating WIP commit...');

        $strategy = $this->strategyFactory->resolve('wip');
        $style = CommitStyle::Conventional;

        if ($strategy->commit($style)) {
            $this->info('WIP commit created.');

            return self::SUCCESS;
        }

        $this->error('Failed to create WIP commit.');

        return self::FAILURE;
    }

    protected function createManualCommit(string $message): int
    {
        $this->info('Creating commit with provided message...');

        $result = Process::run(['git', 'commit', '-m', $message]);

        if ($result->successful()) {
            $this->info('Commit created successfully!');

            return self::SUCCESS;
        }

        $this->error('Failed to create commit.');
        $this->line($result->errorOutput());

        return self::FAILURE;
    }

    protected function createCommit(): int
    {
        $provider = $this->option('provider') ?? config('nimble.ai_provider', 'claude');
        $styleName = $this->option('style') ?? config('nimble.commit_style', 'conventional');
        $style = CommitStyle::fromString($styleName);

        $strategy = $this->strategyFactory->resolve($provider);

        if (! $strategy->isAvailable()) {
            $this->error("Provider '{$provider}' is not available. Is it installed?");

            return self::FAILURE;
        }

        $this->info("Creating commit with {$provider} ({$style->value} style)...");

        if ($strategy->commit($style)) {
            $this->info('Commit created successfully!');

            return self::SUCCESS;
        }

        $this->error('Failed to create commit.');

        return self::FAILURE;
    }
}
