<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Watch Interval
    |--------------------------------------------------------------------------
    |
    | The interval in seconds between checking for git changes. Default is 30
    | seconds. Can be overridden with the --interval option.
    |
    */

    'watch_interval' => env('NIMBLE_WATCH_INTERVAL', 30),

    /*
    |--------------------------------------------------------------------------
    | AI Provider
    |--------------------------------------------------------------------------
    |
    | The AI provider to use for generating commit messages. Currently only
    | 'claude' is supported, which uses a local Claude Code installation.
    |
    */

    'ai_provider' => env('NIMBLE_AI_PROVIDER', 'claude'),

    /*
    |--------------------------------------------------------------------------
    | Claude Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for the Claude Code AI provider.
    |
    */

    'claude' => [
        'timeout' => env('NIMBLE_CLAUDE_TIMEOUT', 600000),
        'skip_permissions' => env('NIMBLE_CLAUDE_SKIP_PERMISSIONS', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Commit Message Style
    |--------------------------------------------------------------------------
    |
    | The commit message style to use. Currently only 'conventional' is
    | supported, following the Conventional Commits specification.
    |
    */

    'commit_style' => env('NIMBLE_COMMIT_STYLE', 'conventional'),

    /*
    |--------------------------------------------------------------------------
    | Conventional Commit Types
    |--------------------------------------------------------------------------
    |
    | The allowed commit types for conventional commits.
    |
    */

    'conventional_types' => [
        'feat',
        'fix',
        'docs',
        'style',
        'refactor',
        'test',
        'chore',
        'perf',
        'ci',
        'build',
        'revert',
    ],

    /*
    |--------------------------------------------------------------------------
    | Auto Stage
    |--------------------------------------------------------------------------
    |
    | Automatically stage all changes before committing. When false, only
    | staged changes will be committed.
    |
    */

    'auto_stage' => env('NIMBLE_AUTO_STAGE', true),

    /*
    |--------------------------------------------------------------------------
    | System Prompt
    |--------------------------------------------------------------------------
    |
    | The system prompt appended to Claude for generating commit messages.
    |
    */

    'system_prompt' => env('NIMBLE_SYSTEM_PROMPT', 'Write git commit messages following conventional commits format: <type>[scope]: <description>. Use types: feat, fix, docs, style, refactor, test, chore, perf, ci, build, revert. Keep subject line under 50 chars, use imperative mood (Add not Added), capitalize first letter. If body needed, separate with blank line and wrap at 72 chars. Focus on what and why, not how. Examples: feat: add user authentication, fix(api): resolve timeout issue'),

];
