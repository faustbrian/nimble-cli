<?php

namespace App\Enums;

enum CommitStyle: string
{
    case Conventional = 'conventional';

    public function systemPrompt(): string
    {
        return match ($this) {
            self::Conventional => 'Write git commit messages following conventional commits format: <type>[scope]: <description>. Use types: feat, fix, docs, style, refactor, test, chore, perf, ci, build, revert. Keep subject line under 50 chars, use imperative mood (Add not Added), capitalize first letter. If body needed, separate with blank line and wrap at 72 chars. Focus on what and why, not how. Examples: feat: add user authentication, fix(api): resolve timeout issue',
        };
    }

    public static function fromString(string $value): self
    {
        return self::from($value);
    }
}
