<?php

namespace App\Strategies;

use App\Contracts\CommitStrategy;
use App\Enums\CommitStyle;
use Illuminate\Support\Facades\Process;

class WipStrategy implements CommitStrategy
{
    public function commit(CommitStyle $style): bool
    {
        $result = Process::run(['git', 'commit', '-m', 'wip']);

        return $result->successful();
    }

    public function supports(string $provider): bool
    {
        return $provider === 'wip';
    }

    public function isAvailable(): bool
    {
        return true;
    }
}
