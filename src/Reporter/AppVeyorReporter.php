<?php

namespace Weirdan\PhpUnitAppVeyorReporter\Reporter;

use Weirdan\PhpUnitAppVeyorReporter\Reporter;

final class AppVeyorReporter implements Reporter
{
    public function reportStarted(string $test, string $filename): void
    {
        echo "\n\nRunning $test ($filename)\n";
    }

    public function reportFinished(
        string $test,
        string $filename,
        string $outcome,
        float $time,
        ?string $error = null
    ): void {
        echo "Test $test ($filename) has finished with $outcome in {$time}s\n";
        if (null !== $error) {
            echo "Error: $error\n";
        }
    }
}
