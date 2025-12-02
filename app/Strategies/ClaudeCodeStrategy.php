<?php

namespace App\Strategies;

use App\Contracts\CommitStrategy;
use App\Enums\CommitStyle;
use Illuminate\Support\Facades\Process;

class ClaudeCodeStrategy implements CommitStrategy
{
    public function commit(CommitStyle $style): bool
    {
        $timeout = config('nimble.claude.timeout', 600000);
        $skipPermissions = config('nimble.claude.skip_permissions', true);
        $systemPrompt = config('nimble.system_prompt') ?? $style->systemPrompt();

        $command = sprintf(
            'BASH_DEFAULT_TIMEOUT_MS=%d claude commit%s --append-system-prompt=%s',
            $timeout,
            $skipPermissions ? ' --dangerously-skip-permissions' : '',
            escapeshellarg($systemPrompt)
        );

        $result = Process::timeout(intval($timeout / 1000) + 60)
            ->tty()
            ->run($command);

        return $result->successful();
    }

    public function supports(string $provider): bool
    {
        return $provider === 'claude';
    }
}
