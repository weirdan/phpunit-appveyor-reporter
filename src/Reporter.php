<?php

namespace Weirdan\PhpUnitAppVeyorReporter;

interface Reporter
{
    public const OUTCOME_RUNNING = 'Running';
    public const OUTCOME_PASSED = 'Passed';
    public const OUTCOME_FAILED = 'Failed';
    public const OUTCOME_SKIPPED = 'Skipped';
    public const OUTCOME_INCONCLUSIVE = 'Inconclusive';

    public function reportStarted(string $test, string $filename): void;

    /** @param self::OUTCOME_* $outcome */
    public function reportFinished(
        string $test,
        string $filename,
        string $outcome,
        float $time,
        ?string $error = null
    ): void;
}
