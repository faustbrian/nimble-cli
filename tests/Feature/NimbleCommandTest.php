<?php

it('shows status', function () {
    $this->artisan('status')->assertExitCode(0);
});

it('can initialize config with force', function () {
    $this->artisan('init', ['--force' => true])->assertExitCode(0);
});

it('shows help for commit', function () {
    $this->artisan('commit', ['--help' => true])->assertExitCode(0);
});

it('shows help for watch', function () {
    $this->artisan('watch', ['--help' => true])->assertExitCode(0);
});
