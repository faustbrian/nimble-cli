<?php

namespace App\Strategies;

use App\Contracts\CommitStrategy;
use RuntimeException;

class CommitStrategyFactory
{
    /** @var array<CommitStrategy> */
    protected array $strategies = [];

    public function __construct()
    {
        $this->register(new ClaudeCodeStrategy);
    }

    public function register(CommitStrategy $strategy): self
    {
        $this->strategies[] = $strategy;

        return $this;
    }

    public function resolve(?string $provider = null): CommitStrategy
    {
        $provider ??= config('nimble.ai_provider', 'claude');

        foreach ($this->strategies as $strategy) {
            if ($strategy->supports($provider)) {
                return $strategy;
            }
        }

        throw new RuntimeException("No commit strategy found for provider: {$provider}");
    }
}
