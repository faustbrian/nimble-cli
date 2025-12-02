<?php

namespace App\Contracts;

use App\Enums\CommitStyle;

interface CommitStrategy
{
    public function commit(CommitStyle $style): bool;

    public function supports(string $provider): bool;

    public function isAvailable(): bool;
}
