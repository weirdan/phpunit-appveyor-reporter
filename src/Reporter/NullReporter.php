<?php

namespace Weirdan\PhpUnitAppVeyorReporter\Reporter;

use Weirdan\PhpUnitAppVeyorReporter\Reporter;

final class NullReporter implements Reporter
{
    public function reportStarted(string $test, string $filename): void
    {
    }

    public function reportFinished(
        string $test,
        string $filename,
        string $outcome,
        float $time,
        ?string $error = null
    ): void {
    }
}
